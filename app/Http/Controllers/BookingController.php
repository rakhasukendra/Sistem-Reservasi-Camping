<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Booking;
use App\Models\CampingItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $items = CampingItem::query()->where('is_available', true)->orderBy('category')->orderBy('name')->get();
        $articles = Article::query()->where('is_published', true)->latest()->get();

        return view('home', compact('items', 'articles'));
    }

    public function checkAvailability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:camping_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        return response()->json(['items' => $this->availability($data['items'], $data['start_date'], $data['end_date'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'max:120'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:camping_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
        $lines = collect($data['items'])->map(fn ($line) => ['id' => (int) $line['id'], 'quantity' => (int) $line['quantity']])->values()->all();

        $booking = DB::transaction(function () use ($data, $lines, $request) {
            $items = CampingItem::query()->whereIn('id', collect($lines)->pluck('id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            if ($items->count() !== count($lines)) {
                throw ValidationException::withMessages(['items' => 'Salah satu alat sudah tidak tersedia. Muat ulang halaman.']);
            }
            foreach ($this->availability($lines, $data['start_date'], $data['end_date']) as $result) {
                if (! $result['available']) {
                    throw ValidationException::withMessages(['items' => $result['name'].': '.$result['message']]);
                }
            }
            $days = max(1, (int) Carbon::parse($data['start_date'])->diffInDays(Carbon::parse($data['end_date'])) + 1);
            $total = collect($lines)->sum(fn ($line) => $items[$line['id']]->daily_rate * $line['quantity'] * $days);
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'booking_code' => 'CAMP-'.strtoupper(Str::random(8)),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => 'pending',
                'contact_name' => $data['contact_name'],
                'contact' => $data['contact'],
                'total' => $total,
            ]);
            foreach ($lines as $line) {
                $booking->items()->attach($line['id'], ['quantity' => $line['quantity'], 'unit_rate' => $items[$line['id']]->daily_rate]);
            }

            return $booking;
        }, 3);

        session()->forget('cart');

        return redirect()->route('profile')->with('success', 'Reservasi '.$booking->booking_code.' tersimpan dan menunggu konfirmasi staf.');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        if (! in_array($booking->status, ['pending', 'approved'], true) || $booking->cancellation_requested_at || $booking->pickup_verified) {
            return back()->withErrors(['booking' => 'Pembatalan sedang ditinjau, barang sudah diambil, atau reservasi ini tidak dapat dibatalkan.']);
        }
        $data = $request->validate(['cancel_reason' => ['required', 'string', 'max:1000']]);
        $refundDate = Carbon::parse($booking->start_date)->startOfDay()->subDays(3);
        $refundPercentage = now()->startOfDay()->lt($refundDate) ? 100 : 50;
        $booking->update([
            'previous_booking_status' => $booking->status,
            'cancellation_requested_at' => now(),
            'cancel_reason' => $data['cancel_reason'],
            'refund_note' => "Hak refund {$refundPercentage}% dicatat jika staf menerima pembatalan, dana belum dikirim.",
            'deposit_note' => 'Catatan pengembalian deposit menunggu keputusan staf.',
        ]);

        return back()->with('success', 'Permintaan pembatalan terkirim ke staf. Reservasi masih menunggu keputusan. Perkiraan refund: '.$refundPercentage.'%.');
    }

    public function cart(): View
    {
        $cart = session('cart', []);
        $items = CampingItem::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');
        $cartItems = collect($cart)->map(function ($quantity, $itemId) use ($items) {
            return ['item' => $items->get($itemId), 'quantity' => (int) $quantity];
        })->filter()->values();

        $total = $cartItems->sum(fn ($item) => $item['item']->daily_rate * $item['quantity']);

        return view('cart', compact('cartItems', 'total'));
    }

    public function create(Request $request): View
    {
        abort_if(! session('cart') || empty(session('cart')), 404);

        $cart = session('cart', []);
        $items = CampingItem::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');
        $cartItems = collect($cart)->map(function ($quantity, $itemId) use ($items) {
            return ['item' => $items->get($itemId), 'quantity' => (int) $quantity];
        })->filter()->values();

        $total = $cartItems->sum(fn ($item) => $item['item']->daily_rate * $item['quantity']);

        return view('booking', compact('cartItems', 'total'));
    }

    public function myBookings(Request $request): View
    {
        $bookings = Booking::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('bookings', compact('bookings'));
    }

    public function addToCart(Request $request, CampingItem $item): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:100']]);
        $cart = session('cart', []);
        if (isset($cart[$item->id])) {
            $cart[$item->id] = min($cart[$item->id] + $data['quantity'], 100);
        } else {
            $cart[$item->id] = $data['quantity'];
        }
        session()->put('cart', $cart);

        return redirect(route('home').'#alat')->with('success', $item->name.' ditambahkan ke keranjang.');
    }

    public function removeFromCart(Request $request, CampingItem $item): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$item->id]);
        session()->put('cart', $cart);

        return back()->with('success', 'Alat dihapus dari keranjang.');
    }

    public function updateCart(Request $request, CampingItem $item): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:100']]);
        $cart = session('cart', []);
        $cart[$item->id] = $data['quantity'];
        session()->put('cart', $cart);

        return back()->with('success', 'Jumlah diupdate.');
    }

    public function clearCart(Request $request): RedirectResponse
    {
        session()->forget('cart');

        return back()->with('success', 'Keranjang dikosongkan.');
    }

    private function availability(array $lines, string $start, string $end): array
    {
        $ids = collect($lines)->pluck('id');
        $items = CampingItem::query()->whereIn('id', $ids)->get()->keyBy('id');
        $reservations = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->whereIn('booking_items.camping_item_id', $ids)
            ->whereIn('bookings.status', ['pending', 'approved'])
            ->whereDate('bookings.start_date', '<=', $end)
            ->whereDate('bookings.end_date', '>=', $start)
            ->select('booking_items.camping_item_id', 'booking_items.quantity', 'bookings.start_date', 'bookings.end_date')
            ->get()
            ->groupBy('camping_item_id');

        return collect($lines)->map(function ($line) use ($items, $reservations, $start, $end) {
            $item = $items->get($line['id']);
            if (! $item) {
                return ['id' => $line['id'], 'name' => 'Alat tidak tersedia', 'available' => false, 'message' => 'Pilih alat yang masih tersedia.'];
            }

            $events = [];
            $windowStart = Carbon::parse($start);
            $windowEnd = Carbon::parse($end);

            foreach ($reservations->get($item->id, collect()) as $reservation) {
                $reservationStart = Carbon::parse($reservation->start_date);
                $reservationEnd = Carbon::parse($reservation->end_date);
                $overlapStart = $reservationStart->greaterThan($windowStart) ? $reservationStart : $windowStart;
                $overlapEnd = $reservationEnd->lessThan($windowEnd) ? $reservationEnd : $windowEnd;
                $startDate = $overlapStart->toDateString();
                $releaseDate = $overlapEnd->copy()->addDay()->toDateString();
                $quantity = (int) $reservation->quantity;

                $events[$startDate] = ($events[$startDate] ?? 0) + $quantity;
                $events[$releaseDate] = ($events[$releaseDate] ?? 0) - $quantity;
            }

            ksort($events);
            $reserved = 0;
            $maximumReserved = 0;

            foreach ($events as $quantityChange) {
                $reserved += $quantityChange;
                $maximumReserved = max($maximumReserved, $reserved);
            }

            $stock = max(0, $item->stock_total - $item->stock_damaged - $maximumReserved);
            $quantity = (int) $line['quantity'];

            return ['id' => $item->id, 'name' => $item->name, 'remaining' => $stock, 'available' => $item->is_available && $quantity <= $stock, 'message' => ! $item->is_available ? 'Alat sedang tidak disewakan.' : ($quantity > $stock ? 'Stok tersedia '.$stock.' unit.' : 'Stok cukup untuk tanggal yang dipilih.')];
        })->all();
    }
}
