<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\CampingItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function inventory(): View
    {
        $items = CampingItem::query()->orderBy('category')->get();

        return view('staff.inventory', compact('items'));
    }

    public function rentals(Request $request): View
    {
        $filters = $request->validate([
            'period' => ['nullable', Rule::in(['daily', 'monthly'])],
            'day' => ['nullable', 'date_format:Y-m-d'],
            'month' => ['nullable', 'date_format:Y-m'],
            'condition' => ['nullable', Rule::in(['all', 'good', 'damaged', 'lost', 'not_returned'])],
        ]);
        $period = $filters['period'] ?? 'monthly';
        $day = $filters['day'] ?? now()->toDateString();
        $month = $filters['month'] ?? now()->format('Y-m');
        $condition = $filters['condition'] ?? 'all';

        $bookings = Booking::with(['user', 'items'])
            ->where('status', 'approved')
            ->when($period === 'daily', fn ($query) => $query->whereDate('start_date', $day))
            ->when($period === 'monthly', fn ($query) => $query
                ->whereYear('start_date', substr($month, 0, 4))
                ->whereMonth('start_date', substr($month, 5, 2)))
            ->when($condition !== 'all', fn ($query) => $query->where('return_condition', $condition))
            ->orderByDesc('start_date')
            ->latest()
            ->get();

        $allBookings = Booking::with(['user', 'items'])->latest()->get();

        return view('staff.rentals', compact('bookings', 'allBookings', 'period', 'day', 'month', 'condition'));
    }

    public function articles(): View
    {
        $articles = Article::query()->latest()->get();

        return view('staff.articles', compact('articles'));
    }

    public function analytics(): View
    {
        $period = request('period', 'month') === 'year' ? 'year' : 'month';
        $since = $period === 'year' ? now()->startOfYear() : now()->startOfMonth();
        $until = $period === 'year' ? now()->endOfYear() : now()->endOfMonth();
        $daily = Booking::query()->whereDate('start_date', '>=', $since)->whereDate('start_date', '<=', $until)
            ->selectRaw('start_date, COUNT(*) as booking_count, SUM(CASE WHEN status = ? OR cancellation_requested_at IS NOT NULL THEN 1 ELSE 0 END) as cancellation_count, SUM(CASE WHEN status = ? THEN total ELSE 0 END) as revenue', ['cancelled', 'approved'])
            ->groupBy('start_date')->orderBy('start_date')->get();
        $chartMaximum = max(1, (int) $daily->max('booking_count'));
        $items = CampingItem::query()->orderBy('category')->get();
        $periodDays = max(1, (int) $since->diffInDays($until) + 1);
        $uses = DB::table('booking_items')->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'approved')->whereDate('bookings.start_date', '<=', $until)->whereDate('bookings.end_date', '>=', $since)
            ->select('booking_items.camping_item_id', 'booking_items.quantity', 'bookings.start_date', 'bookings.end_date')->get()->groupBy('camping_item_id');
        $utilization = $items->map(function (CampingItem $item) use ($uses, $since, $until, $periodDays) {
            $rentedUnitDays = ($uses->get($item->id, collect()))->sum(function ($use) use ($since, $until) {
                $rentalStart = Carbon::parse($use->start_date)->max($since);
                $rentalEnd = Carbon::parse($use->end_date)->min($until);
                $days = max(1, (int) $rentalStart->diffInDays($rentalEnd) + 1);

                return $days * (int) $use->quantity;
            });
            $capacity = max(0, $item->stock_total - $item->stock_damaged) * $periodDays;

            return ['item' => $item, 'unit_days' => $rentedUnitDays, 'percent' => $capacity ? min(100, (int) round($rentedUnitDays / $capacity * 100)) : 0];
        });

        $transactions = Booking::with('user')->latest()->take(30)->get();
        $logs = auth()->user()->role === 'admin' ? AuditLog::with('actor')->latest()->take(30)->get() : collect();

        return view('staff.analytics', compact('daily', 'chartMaximum', 'period', 'utilization', 'transactions', 'logs'));
    }

    public function decide(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate(['decision' => ['required', Rule::in(['approved', 'rejected', 'pending'])], 'reason' => ['nullable', 'string', 'max:1000']]);

        if ($data['decision'] === 'rejected' && empty($data['reason'])) {
            return back()->withErrors(['reason' => 'Tuliskan alasan penolakan atau catatan pembatalan.']);
        }

        if ($booking->cancellation_requested_at) {
            abort_unless($booking->return_condition === 'not_returned', 422, 'Status reservasi tidak dapat diubah setelah kondisi pengembalian dicatat.');
            abort_unless(! $booking->pickup_verified, 422, 'Status reservasi tidak dapat diubah setelah pengambilan dicatat.');

            if ($data['decision'] === 'approved') {
                $booking->update([
                    'status' => 'cancelled',
                    'cancellation_review_note' => $data['reason'] ?: 'Pembatalan disetujui oleh staf.',
                    'refund_note' => $booking->refund_note ?? 'Catatan refund diperbarui oleh staf.',
                    'deposit_note' => 'Pengembalian deposit dicatat untuk diperiksa staf.',
                ]);
                $action = 'booking.cancellation_approved';
            } else {
                $booking->update([
                    'status' => $booking->previous_booking_status ?? 'approved',
                    'cancellation_requested_at' => null,
                    'cancellation_review_note' => $data['reason'] ?: 'Ajuan pembatalan ditolak oleh staf.',
                    'cancel_reason' => null,
                ]);
                $action = 'booking.cancellation_rejected';
            }
        } else {
            $contactVerified = $data['decision'] === 'approved';
            $pickupVerified = false;
            $cancelReason = $data['decision'] === 'approved' ? null : ($data['reason'] ?? $booking->cancel_reason);
            $refundNote = $data['decision'] === 'rejected' ? 'Tidak ada refund. Reservasi ditolak sebelum pembayaran.' : ($data['decision'] === 'approved' ? null : $booking->refund_note);
            $booking->update([
                'status' => $data['decision'],
                'contact_verified' => $contactVerified,
                'pickup_verified' => $pickupVerified,
                'cancel_reason' => $cancelReason,
                'refund_note' => $refundNote,
            ]);
            $action = 'booking.'.$data['decision'];
        }
        $this->log($request, $action, 'Reservasi '.$booking->booking_code.': '.($data['reason'] ?? ''));

        return back()->with('success', 'Keputusan reservasi disimpan.');
    }

    public function verifyContact(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === 'approved', 422, 'Kontak hanya dapat diverifikasi untuk reservasi yang disetujui.');

        if ($request->boolean('reset')) {
            $booking->update(['contact_verified' => false]);
            $this->log($request, 'booking.contact_reset', 'Verifikasi kontak dibatalkan untuk '.$booking->booking_code);

            return back()->with('success', 'Verifikasi kontak direset. Tandai ulang setelah kontak penyewa dikonfirmasi.');
        }

        if ($booking->contact_verified) {
            return back()->with('success', 'Kontak reservasi sudah terverifikasi.');
        }

        $booking->update(['contact_verified' => true]);
        $this->log($request, 'booking.contact_verified', 'Kontak diverifikasi untuk '.$booking->booking_code);

        return back()->with('success', 'Kontak reservasi ditandai terverifikasi.');
    }

    public function pickup(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === 'approved', 422, 'Reservasi harus disetujui sebelum alat diambil.');

        if ($request->boolean('reset')) {
            $booking->update(['pickup_verified' => false]);
            $this->log($request, 'booking.pickup_reset', 'Catatan pengambilan dibatalkan untuk '.$booking->booking_code);

            return back()->with('success', 'Catatan pengambilan direset.');
        }

        if ($booking->pickup_verified) {
            return back()->with('success', 'Pengambilan alat sudah dicatat untuk reservasi ini.');
        }

        $booking->update(['pickup_verified' => true]);
        $this->log($request, 'booking.pickup_verified', 'Kartu dummy pengambilan dibuat untuk '.$booking->booking_code);

        return back()->with('success', 'Pengambilan alat terverifikasi. Kartu pengambilan dummy '.$booking->booking_code.' tersedia di profil penyewa.');
    }

    public function condition(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate(['condition' => ['required', Rule::in(['good', 'damaged', 'lost'])], 'replacement_amount' => ['nullable', 'integer', 'min:0'], 'replacement_paid' => ['nullable', 'boolean']]);

        DB::transaction(function () use ($booking, $data) {
            $lockedBooking = Booking::query()->with('items')->lockForUpdate()->findOrFail($booking->id);
            abort_unless($lockedBooking->status === 'approved', 422, 'Kondisi hanya dapat dicatat untuk reservasi yang disetujui.');
            abort_unless($lockedBooking->contact_verified && $lockedBooking->pickup_verified, 422, 'Verifikasi kontak dan catat pengambilan sebelum kondisi pengembalian.');

            foreach ($lockedBooking->items->sortBy('id') as $bookingItem) {
                $quantity = (int) $bookingItem->pivot->quantity;
                $item = CampingItem::query()->lockForUpdate()->findOrFail($bookingItem->id);

                if ($lockedBooking->return_condition !== 'not_returned') {
                    if ($lockedBooking->return_condition === 'damaged' && $data['condition'] !== 'damaged') {
                        $item->stock_damaged = max(0, $item->stock_damaged - $quantity);
                    }
                    if ($lockedBooking->return_condition === 'lost' && $data['condition'] !== 'lost') {
                        $item->stock_total += $quantity;
                    }
                    if ($data['condition'] === 'damaged') {
                        $item->stock_damaged += $quantity;
                    }
                    if ($data['condition'] === 'lost') {
                        $item->stock_total = max(0, $item->stock_total - $quantity);
                    }
                } else {
                    if ($data['condition'] === 'damaged') {
                        $item->stock_damaged += $quantity;
                    }
                    if ($data['condition'] === 'lost') {
                        $item->stock_total = max(0, $item->stock_total - $quantity);
                    }
                }
                $item->save();
            }

            $lockedBooking->update(['return_condition' => $data['condition'], 'replacement_amount' => (int) ($data['replacement_amount'] ?? 0), 'replacement_paid' => (bool) ($data['replacement_paid'] ?? false)]);
        });
        $this->log($request, 'booking.return_condition', 'Kondisi pengembalian '.$booking->booking_code.': '.$data['condition']);

        return back()->with('success', 'Kondisi alat dan catatan penggantian diperbarui.');
    }

    public function editItem(CampingItem $item): View
    {
        return view('staff.edit-item', compact('item'));
    }

    public function saveItem(Request $request, ?CampingItem $item = null): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'category' => ['required', 'string', 'max:80'], 'description' => ['required', 'string', 'max:2000'], 'stock_total' => ['required', 'integer', 'min:0'], 'stock_damaged' => ['required', 'integer', 'min:0'], 'daily_rate' => ['required', 'integer', 'min:0'], 'is_available' => ['nullable', 'boolean']]);

        if ($data['stock_damaged'] > $data['stock_total']) {
            return back()->withErrors(['stock_damaged' => 'Stok rusak tidak boleh lebih besar dari stok total.'])->withInput();
        }

        $data['is_available'] = (bool) ($data['is_available'] ?? false);
        if ($item) {
            $item->update($data);
            $message = 'Data alat diperbarui.';
            $this->log($request, 'equipment.saved', $data['name']);

            return redirect()->route('staff.inventory')->with('success', $message);
        } else {
            CampingItem::create($data);
            $message = 'Alat ditambahkan.';
            $this->log($request, 'equipment.saved', $data['name']);

            return back()->with('success', $message);
        }
    }

    public function deleteItem(Request $request, CampingItem $item): RedirectResponse
    {
        if ($item->bookings()->exists()) {
            return back()->withErrors(['equipment' => 'Alat memiliki riwayat reservasi dan tidak dapat dihapus. Nonaktifkan penyewaan sebagai gantinya.']);
        }
        $name = $item->name;
        $item->delete();
        $this->log($request, 'equipment.deleted', $name);

        return back()->with('success', 'Alat dihapus.');
    }

    public function saveArticle(Request $request, ?Article $article = null): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['required', 'string', 'max:300'],
            'content' => ['required', 'string', 'max:12000'],
            'is_published' => ['nullable', 'boolean'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);
        unset($data['image_url'], $data['image_file']);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        if ($request->hasFile('image_file')) {
            $data['image'] = $request->file('image_file')->store('articles', 'public');
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }
        if ($article) {
            $article->update($data);
        } else {
            $data['slug'] = Str::slug($data['title']).'-'.Str::lower(Str::random(5));
            Article::create($data);
        }
        $this->log($request, 'article.saved', $data['title']);

        return back()->with('success', 'Artikel disimpan.');
    }

    public function deleteArticle(Request $request, Article $article): RedirectResponse
    {
        $title = $article->title;
        $article->delete();
        $this->log($request, 'article.deleted', $title);

        return back()->with('success', 'Artikel dihapus.');
    }

    public function saveUser(Request $request, ?User $user = null): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate($user ? ['role' => ['required', Rule::in(['user', 'staff', 'admin'])]] : [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['user', 'staff', 'admin'])],
        ]);
        if ($user) {
            if ($user->is($request->user()) && $data['role'] !== 'admin') {
                return back()->withErrors(['role' => 'Admin tidak dapat menghapus akses admin miliknya sendiri.']);
            }
            $user->update($data);
            $action = 'user.role_updated';
            $details = 'Role akun '.$user->email.' diubah menjadi '.$data['role'];
        } else {
            User::create(array_merge($data, ['password' => bcrypt($data['password'])]));
            $action = 'user.created';
            $details = 'Akun baru dibuat: '.$data['email'];
        }
        $this->log($request, $action, $details);

        return back()->with('success', $user ? 'Peran akun diperbarui.' : 'Akun pengguna baru dibuat.');
    }

    public function deleteUser(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_unless(! $user->is($request->user()), 403, 'Admin tidak dapat menghapus akun miliknya sendiri.');

        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['account' => 'Tidak dapat menghapus akun admin terakhir.']);
            }
        }

        $email = $user->email;
        $user->delete();
        $this->log($request, 'user.deleted', 'Akun '.$email.' dihapus oleh admin');

        return back()->with('success', 'Akun pengguna berhasil dihapus.');
    }

    public function assessStaff(Request $request, User $staff): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_unless($staff->role === 'staff', 422, 'Hanya staff yang dapat dinilai.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'assessment_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->log($request, 'staff.assessed', 'Penilaian untuk '.$staff->name.': Rating '.$data['rating'].'/5. '.$data['assessment_notes']);

        return back()->with('success', 'Penilaian staff disimpan.');
    }

    public function assessmentPage(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        $filters = $request->validate(['month' => ['nullable', 'date_format:Y-m']]);
        $month = $filters['month'] ?? now()->format('Y-m');
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $staffUsers = User::query()->where('role', 'staff')->orderBy('name')->get();

        $staffUsers = $staffUsers->map(function ($staff) use ($year, $monthNum) {
            $monthAssessments = AuditLog::where('action', 'staff.assessed')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->where('details', 'like', '%Penilaian untuk '.$staff->name.'%')
                ->latest()
                ->get();

            preg_match('/Rating (\d)\/5/', $monthAssessments->first()?->details ?? '', $matches);
            $staff->current_rating = $matches[1] ?? null;
            $staff->assessments_this_month = $monthAssessments;

            return $staff;
        });

        $logs = AuditLog::with('actor')
            ->where('action', 'staff.assessed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->latest()
            ->get();

        return view('admin.assessments', compact('staffUsers', 'logs', 'month', 'year', 'monthNum'));
    }

    public function updateAssessment(Request $request, AuditLog $log): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_unless($log->action === 'staff.assessed', 422);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'assessment_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        preg_match('/Penilaian untuk (.+?):/i', $log->details, $matches);
        $staffName = $matches[1] ?? 'Staff';

        $log->update([
            'details' => 'Penilaian untuk '.$staffName.': Rating '.$data['rating'].'/5. '.$data['assessment_notes'],
        ]);

        return back()->with('success', 'Penilaian diperbarui.');
    }

    public function deleteAssessment(Request $request, AuditLog $log): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_unless($log->action === 'staff.assessed', 422);

        $log->delete();

        return back()->with('success', 'Penilaian dihapus.');
    }

    private function log(Request $request, string $action, string $details): void
    {
        AuditLog::create(['actor_id' => $request->user()->id, 'action' => $action, 'details' => $details]);
    }
}
