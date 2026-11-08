<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\CampingItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_demo_accounts_and_public_catalogue_are_available(): void
    {
        $this->get('/')->assertOk()->assertSee('Tenda Dome 4 Orang')->assertSee('Tips memilih tenda untuk pemula');
        $this->assertDatabaseHas('users', ['email' => 'admin@demo.test', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'staff@demo.test', 'role' => 'staff']);
        $this->assertDatabaseHas('users', ['email' => 'user@demo.test', 'role' => 'user']);
        $this->assertSame(3, Article::count());
    }

    public function test_home_lists_published_guide_articles(): void
    {
        $published = Article::where('slug', 'tips-memilih-tenda-untuk-pemula')->firstOrFail();
        $draft = Article::create([
            'title' => 'Draf panduan tersembunyi',
            'slug' => 'draf-panduan-tersembunyi',
            'excerpt' => 'Belum siap terbit',
            'content' => 'Konten draf',
            'is_published' => false,
        ]);

        $response = $this->get('/');
        $response->assertOk()
            ->assertSee('Panduan Camping')
            ->assertSee($published->title)
            ->assertSee(route('articles.show', $published), false)
            ->assertDontSee($draft->title);

        $this->assertSame(
            Article::where('is_published', true)->count(),
            substr_count($response->getContent(), 'class="article-image"'),
            'Setiap panduan terbit harus tampil dengan satu gambar.',
        );

        $this->get(route('articles.show', $published))->assertOk()->assertSee($published->excerpt);
    }

    public function test_home_loads_its_images_from_the_local_public_folder(): void
    {
        $response = $this->get('/')->assertOk();
        $content = $response->getContent();

        $this->assertStringNotContainsString('images.unsplash.com', $content);
        $this->assertStringNotContainsString('mediadecathlon.com', $content);

        preg_match_all('#/images/([a-z-]+\.jpg)#', $content, $matches);
        $this->assertNotEmpty($matches[1], 'Halaman depan harus memakai gambar lokal.');

        foreach (array_unique($matches[1]) as $file) {
            $this->assertFileExists(public_path('images/'.$file), 'Gambar '.$file.' belum ada di public/images.');
        }
    }

    public function test_staff_can_attach_a_guide_image_by_upload_or_url(): void
    {
        Storage::fake('public');
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Panduan gambar unggahan',
            'excerpt' => 'Ringkasan panduan unggahan',
            'content' => 'Isi panduan unggahan',
            'is_published' => 1,
            'image_file' => UploadedFile::fake()->image('panduan.jpg'),
        ])->assertRedirect();

        $uploaded = Article::where('title', 'Panduan gambar unggahan')->firstOrFail();
        $this->assertNotNull($uploaded->image);
        Storage::disk('public')->assertExists($uploaded->image);
        $this->assertSame(Storage::disk('public')->url($uploaded->image), $uploaded->imageUrl());
        $this->get('/')->assertOk()->assertSee($uploaded->imageUrl(), false);
        $originalSlug = $uploaded->slug;

        $this->actingAs($staff)->put('/staff/artikel/'.$uploaded->id, [
            'title' => 'Panduan gambar unggahan diperbarui',
            'excerpt' => 'Ringkasan diperbarui',
            'content' => 'Isi diperbarui',
            'is_published' => 1,
        ])->assertRedirect();
        $this->assertSame($originalSlug, $uploaded->fresh()->slug);
        $this->assertSame($uploaded->image, $uploaded->fresh()->image);

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Panduan gambar tautan',
            'excerpt' => 'Ringkasan panduan tautan',
            'content' => 'Isi panduan tautan',
            'is_published' => 1,
            'image_url' => 'https://example.com/panduan.jpg',
        ])->assertRedirect();

        $linked = Article::where('title', 'Panduan gambar tautan')->firstOrFail();
        $this->assertSame('https://example.com/panduan.jpg', $linked->image);
        $this->assertSame('https://example.com/panduan.jpg', $linked->imageUrl());
        $this->get('/')->assertOk()->assertSee('https://example.com/panduan.jpg', false);

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Panduan foto lima megabita',
            'excerpt' => 'Ringkasan foto besar',
            'content' => 'Isi foto besar',
            'is_published' => 1,
            'image_file' => UploadedFile::fake()->image('foto.jpg')->size(5000),
        ])->assertRedirect();
        $large = Article::where('title', 'Panduan foto lima megabita')->firstOrFail();
        Storage::disk('public')->assertExists($large->image);

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Panduan foto kebesaran',
            'excerpt' => 'Ringkasan foto kebesaran',
            'content' => 'Isi foto kebesaran',
            'is_published' => 1,
            'image_file' => UploadedFile::fake()->image('foto.jpg')->size(11000),
        ])->assertSessionHasErrors('image_file');
        $this->assertDatabaseMissing('articles', ['title' => 'Panduan foto kebesaran']);
    }

    public function test_navbar_shows_the_signed_in_name_and_role_for_each_account_type(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $admin = User::where('email', 'admin@demo.test')->firstOrFail();

        $this->actingAs($user)->get('/')->assertOk()->assertSee('User Demo')->assertSee('Masuk sebagai Penyewa');
        $this->actingAs($staff)->get('/')->assertOk()->assertSee('Staff Demo')->assertSee('Masuk sebagai Staff');
        $this->actingAs($admin)->get('/')->assertOk()->assertSee('Admin Demo')->assertSee('Masuk sebagai Admin');
    }

    public function test_account_menu_holds_the_profile_and_logout_actions(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();

        $this->actingAs($user)->get('/')->assertOk()
            ->assertSee('class="nav-account-dropdown"', false)
            ->assertSee('href="'.route('profile').'"', false)
            ->assertSee('action="'.route('logout').'"', false)
            ->assertDontSee('class="nav-form"', false);

        $this->post('/keluar')->assertRedirect('/');
        $this->assertGuest();

        $this->get('/')->assertOk()->assertDontSee('class="nav-account-menu"', false);
    }

    public function test_staff_and_admin_do_not_see_rental_actions_in_the_catalogue(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $admin = User::where('email', 'admin@demo.test')->firstOrFail();

        $this->actingAs($user)->get('/')->assertOk()->assertSee('+ Keranjang');
        $this->actingAs($staff)->get('/')->assertOk()->assertDontSee('+ Keranjang');
        $this->actingAs($admin)->get('/')->assertOk()->assertDontSee('+ Keranjang');
    }

    public function test_staff_and_admin_cannot_create_customer_reservations(): void
    {
        $item = CampingItem::firstOrFail();

        foreach (['staff@demo.test', 'admin@demo.test'] as $email) {
            $account = User::where('email', $email)->firstOrFail();

            $this->actingAs($account)->post('/reservasi', [
                'start_date' => Carbon::tomorrow()->toDateString(),
                'end_date' => Carbon::tomorrow()->addDay()->toDateString(),
                'contact_name' => $account->name,
                'contact' => $account->email,
                'items' => [$item->id => ['quantity' => 1]],
            ])->assertForbidden();
        }

        $this->assertSame(0, Booking::count());
    }

    public function test_new_account_is_created_with_user_role_and_password_hash(): void
    {
        $response = $this->post('/daftar', [
            'name' => 'Penyewa Baru',
            'email' => 'renter@example.test',
            'phone' => '08123456789',
            'password' => 'Camping#2026',
            'password_confirmation' => 'Camping#2026',
        ]);

        $response->assertRedirect('/profil');
        $account = User::where('email', 'renter@example.test')->firstOrFail();
        $this->assertSame('user', $account->role);
        $this->assertTrue(Hash::check('Camping#2026', $account->password));
    }

    public function test_renter_can_create_a_booking_and_stock_is_reserved_for_its_date_range(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $item = CampingItem::where('name', 'Tenda Dome 4 Orang')->firstOrFail();
        $start = Carbon::today()->addDays(10)->toDateString();
        $end = Carbon::today()->addDays(12)->toDateString();

        $this->actingAs($user)->post('/reservasi', [
            'start_date' => $start,
            'end_date' => $end,
            'contact_name' => $user->name,
            'contact' => $user->phone,
            'items' => [['id' => $item->id, 'quantity' => 2]],
        ])->assertRedirect('/profil');

        $booking = Booking::firstOrFail();
        $this->assertSame('pending', $booking->status);
        $this->assertSame(510000, $booking->total);
        $this->assertDatabaseHas('booking_items', ['booking_id' => $booking->id, 'camping_item_id' => $item->id, 'quantity' => 2]);

        $this->actingAs($user)->postJson('/ketersediaan', [
            'start_date' => $start,
            'end_date' => $end,
            'items' => [['id' => $item->id, 'quantity' => 7]],
        ])->assertOk()->assertJsonPath('items.0.available', false)->assertJsonPath('items.0.remaining', 6);

        $this->actingAs($user)->postJson('/ketersediaan', [
            'start_date' => Carbon::today()->addDays(20)->toDateString(),
            'end_date' => Carbon::today()->addDays(21)->toDateString(),
            'items' => [['id' => $item->id, 'quantity' => 8]],
        ])->assertOk()->assertJsonPath('items.0.available', true)->assertJsonPath('items.0.remaining', 8);
    }

    public function test_availability_uses_peak_overlapping_reservations_instead_of_summing_all_bookings(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $item = CampingItem::where('name', 'Tenda Dome 4 Orang')->firstOrFail();
        $start = Carbon::today()->addDays(10);

        foreach ([
            [$start, $start->copy()->addDay()],
            [$start->copy()->addDays(2), $start->copy()->addDays(3)],
        ] as $index => [$bookingStart, $bookingEnd]) {
            $booking = Booking::create([
                'user_id' => $user->id,
                'booking_code' => 'CAMP-NONOVERLAP'.$index,
                'start_date' => $bookingStart,
                'end_date' => $bookingEnd,
                'status' => 'approved',
                'contact_name' => $user->name,
                'contact' => $user->phone,
                'total' => $item->daily_rate * 6,
            ]);
            $booking->items()->attach($item->id, ['quantity' => 6, 'unit_rate' => $item->daily_rate]);
        }

        $this->actingAs($user)->postJson('/ketersediaan', [
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(3)->toDateString(),
            'items' => [['id' => $item->id, 'quantity' => 2]],
        ])->assertOk()
            ->assertJsonPath('items.0.available', true)
            ->assertJsonPath('items.0.remaining', 2);
    }

    public function test_cancellation_waits_for_staff_before_releasing_stock_and_records_refund_policy(): void
    {
        $this->travelTo(Carbon::parse('2026-10-28 10:00:00'));
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $item = CampingItem::where('name', 'Tenda Dome 4 Orang')->firstOrFail();
        $start = Carbon::today()->addDays(2);
        $booking = Booking::create([
            'user_id' => $user->id,
            'booking_code' => 'CAMP-CANCEL01',
            'start_date' => $start,
            'end_date' => $start->copy()->addDays(2),
            'status' => 'approved',
            'contact_name' => $user->name,
            'contact' => $user->email,
            'total' => $item->daily_rate,
        ]);
        $booking->items()->attach($item->id, ['quantity' => 2, 'unit_rate' => $item->daily_rate]);
        $rentalStart = Carbon::parse($booking->start_date);

        $this->actingAs($user)->post('/reservasi/'.$booking->id.'/batal', ['cancel_reason' => 'Rencana perjalanan berubah'])->assertRedirect();
        $booking->refresh();
        $this->assertSame('approved', $booking->status);
        $this->assertNotNull($booking->cancellation_requested_at);
        $this->assertStringContainsString('50%', $booking->refund_note);

        $this->actingAs($user)->postJson('/ketersediaan', [
            'start_date' => $rentalStart->toDateString(),
            'end_date' => $rentalStart->copy()->addDays(2)->toDateString(),
            'items' => [['id' => $item->id, 'quantity' => 7]],
        ])->assertOk()->assertJsonPath('items.0.available', false)->assertJsonPath('items.0.remaining', 6);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/keputusan', [
            'decision' => 'rejected',
            'reason' => 'Pembatalan disetujui',
        ])->assertRedirect();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'approved']);

        $this->actingAs($user)->postJson('/ketersediaan', [
            'start_date' => $rentalStart->toDateString(),
            'end_date' => $rentalStart->copy()->addDays(2)->toDateString(),
            'items' => [['id' => $item->id, 'quantity' => 8]],
        ])->assertOk()->assertJsonPath('items.0.available', false)->assertJsonPath('items.0.remaining', 6);
    }

    public function test_staff_can_verify_contact_and_record_pickup_for_approved_bookings(): void
    {
        $renter = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $booking = Booking::create([
            'user_id' => $renter->id,
            'booking_code' => 'CAMP-VERIFY01',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(2),
            'status' => 'approved',
            'contact_name' => $renter->name,
            'contact' => $renter->phone,
            'total' => 0,
        ]);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/verifikasi-kontak')->assertRedirect();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'contact_verified' => true]);
        $this->assertDatabaseHas('audit_logs', ['actor_id' => $staff->id, 'action' => 'booking.contact_verified', 'details' => 'Kontak diverifikasi untuk CAMP-VERIFY01']);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/pengambilan')->assertRedirect();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'pickup_verified' => true]);
        $this->assertDatabaseHas('audit_logs', ['actor_id' => $staff->id, 'action' => 'booking.pickup_verified', 'details' => 'Kartu dummy pengambilan dibuat untuk CAMP-VERIFY01']);
    }

    public function test_recording_early_pickup_keeps_booking_visible_until_return_is_recorded(): void
    {
        $this->travelTo(Carbon::parse('2026-10-30 10:00:00'));
        $renter = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $booking = Booking::create([
            'user_id' => $renter->id,
            'booking_code' => 'CAMP-EARLYPICKUP',
            'start_date' => Carbon::tomorrow(),
            'end_date' => Carbon::tomorrow()->addDays(2),
            'status' => 'approved',
            'contact_name' => $renter->name,
            'contact' => $renter->phone,
            'total' => 0,
            'contact_verified' => true,
        ]);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/pengambilan')->assertRedirect();
        $this->get('/staff/sewa?period=monthly&month=2026-10')->assertOk()
            ->assertSee('CAMP-EARLYPICKUP')
            ->assertSee('Catat kondisi pengembalian');

        $this->travelTo(Carbon::parse('2026-11-03 10:00:00'));
        $this->get('/staff/sewa?period=monthly&month=2026-10')->assertOk()
            ->assertSee('CAMP-EARLYPICKUP')
            ->assertSee('Catat kondisi pengembalian');

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/kondisi', [
            'condition' => 'good',
        ])->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'pickup_verified' => true,
            'return_condition' => 'good',
        ]);
        $this->get('/staff/sewa?period=monthly&month=2026-10')->assertOk()
            ->assertSee('CAMP-EARLYPICKUP')
            ->assertSee('Reservasi ditemukan: 1')
            ->assertSee('Kondisi pengembalian')
            ->assertSee('✓ Baik')
            ->assertDontSee('Catat kondisi pengembalian');

        $this->get('/staff/sewa?period=daily&day=2026-10-31')->assertOk()
            ->assertSee('CAMP-EARLYPICKUP')
            ->assertSee('Reservasi ditemukan: 1');
        $this->get('/staff/sewa?period=daily&day=2026-11-03')->assertOk()
            ->assertSee('Reservasi ditemukan: 0')
            ->assertSee('Tidak ada reservasi disetujui pada periode ini.');
    }

    public function test_contact_and_pickup_cannot_be_verified_for_unapproved_bookings(): void
    {
        $renter = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $booking = Booking::create([
            'user_id' => $renter->id,
            'booking_code' => 'CAMP-PENDING01',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(2),
            'status' => 'pending',
            'contact_name' => $renter->name,
            'contact' => $renter->phone,
            'total' => 0,
        ]);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/verifikasi-kontak')->assertStatus(422);
        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/pengambilan')->assertStatus(422);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'contact_verified' => false, 'pickup_verified' => false]);
        $this->assertDatabaseMissing('audit_logs', ['action' => 'booking.contact_verified']);
        $this->assertDatabaseMissing('audit_logs', ['action' => 'booking.pickup_verified']);
    }

    public function test_staff_can_reset_contact_and_pickup_verification(): void
    {
        $renter = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $booking = Booking::create([
            'user_id' => $renter->id,
            'booking_code' => 'CAMP-RESET01',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(2),
            'status' => 'approved',
            'contact_name' => $renter->name,
            'contact' => $renter->phone,
            'total' => 0,
            'contact_verified' => true,
            'pickup_verified' => true,
        ]);

        $this->actingAs($staff)->get('/staff/sewa')->assertOk()
            ->assertSee('Reset verifikasi kontak')
            ->assertSee('Reset catatan pengambilan');

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/verifikasi-kontak', ['reset' => 1])->assertRedirect();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'contact_verified' => false, 'pickup_verified' => true]);
        $this->assertDatabaseHas('audit_logs', ['actor_id' => $staff->id, 'action' => 'booking.contact_reset', 'details' => 'Verifikasi kontak dibatalkan untuk CAMP-RESET01']);

        $this->actingAs($staff)->get('/staff/sewa')->assertOk()
            ->assertSee('Edit Status Verifikasi')
            ->assertSee('Status saat ini: Pengambilan tercatat')
            ->assertDontSee('Reset verifikasi kontak')
            ->assertSee('Reset catatan pengambilan');

        $this->actingAs($renter)->get('/reservasi-saya')->assertOk()
            ->assertSee('⏱ Menunggu')
            ->assertSee('✓ Sudah diambil');

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/pengambilan', ['reset' => 1])->assertRedirect();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'contact_verified' => false, 'pickup_verified' => false]);
        $this->assertDatabaseHas('audit_logs', ['actor_id' => $staff->id, 'action' => 'booking.pickup_reset', 'details' => 'Catatan pengambilan dibatalkan untuk CAMP-RESET01']);

        $this->actingAs($staff)->get('/staff/sewa')->assertOk()
            ->assertDontSee('Edit Status Verifikasi')
            ->assertDontSee('Reset catatan pengambilan');
    }

    public function test_staff_cannot_change_booking_status_after_pickup_is_recorded(): void
    {
        $renter = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $booking = Booking::create([
            'user_id' => $renter->id,
            'booking_code' => 'CAMP-STATUS01',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(2),
            'status' => 'approved',
            'contact_name' => $renter->name,
            'contact' => $renter->phone,
            'total' => 0,
            'contact_verified' => true,
            'pickup_verified' => true,
        ]);

        $this->actingAs($renter)->get('/reservasi-saya')->assertOk()
            ->assertSee('✓ Terverifikasi')
            ->assertSee('✓ Sudah diambil')
            ->assertSee('Kartu pengambilan dummy');

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/keputusan', [
            'decision' => 'pending',
            'reason' => 'Perlu pemeriksaan ulang',
        ])->assertStatus(422);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'approved', 'contact_verified' => true, 'pickup_verified' => true]);

        $this->actingAs($renter)->get('/reservasi-saya')->assertOk()
            ->assertSee('✓ Terverifikasi')
            ->assertSee('✓ Sudah diambil')
            ->assertSee('Kartu pengambilan dummy');
    }

    public function test_returned_damaged_and_lost_equipment_updates_sellable_inventory_once(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $item = CampingItem::where('name', 'Tenda Dome 4 Orang')->firstOrFail();
        $booking = Booking::create([
            'user_id' => $user->id,
            'booking_code' => 'CAMP-RETURN01',
            'start_date' => Carbon::today()->addDays(2),
            'end_date' => Carbon::today()->addDays(4),
            'status' => 'approved',
            'contact_name' => $user->name,
            'contact' => $user->email,
            'total' => $item->daily_rate * 2,
            'contact_verified' => true,
            'pickup_verified' => true,
        ]);
        $booking->items()->attach($item->id, ['quantity' => 2, 'unit_rate' => $item->daily_rate]);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/kondisi', [
            'condition' => 'damaged',
            'replacement_amount' => 50000,
        ])->assertRedirect();
        $this->assertSame(2, $item->fresh()->stock_damaged);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/kondisi', [
            'condition' => 'good',
        ])->assertRedirect()->assertSessionHasErrors('condition');
        $this->assertSame(2, $item->fresh()->stock_damaged);

        $lostBooking = Booking::create([
            'user_id' => $user->id,
            'booking_code' => 'CAMP-LOST0001',
            'start_date' => Carbon::today()->addDays(6),
            'end_date' => Carbon::today()->addDays(8),
            'status' => 'approved',
            'contact_name' => $user->name,
            'contact' => $user->email,
            'total' => $item->daily_rate,
            'contact_verified' => true,
            'pickup_verified' => true,
        ]);
        $lostBooking->items()->attach($item->id, ['quantity' => 1, 'unit_rate' => $item->daily_rate]);
        $this->actingAs($staff)->post('/staff/reservasi/'.$lostBooking->id.'/kondisi', ['condition' => 'lost'])->assertRedirect();
        $this->assertSame(7, $item->fresh()->stock_total);
        $this->assertDatabaseHas('audit_logs', ['action' => 'booking.return_condition']);
    }

    public function test_return_condition_requires_contact_and_pickup_verification(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $item = CampingItem::where('name', 'Tenda Dome 4 Orang')->firstOrFail();
        $booking = Booking::create([
            'user_id' => $user->id,
            'booking_code' => 'CAMP-RETURNGATE',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDay(),
            'status' => 'approved',
            'contact_name' => $user->name,
            'contact' => $user->phone,
            'total' => $item->daily_rate,
        ]);
        $booking->items()->attach($item->id, ['quantity' => 1, 'unit_rate' => $item->daily_rate]);

        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/kondisi', [
            'condition' => 'good',
        ])->assertStatus(422);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'return_condition' => 'not_returned']);
        $this->assertSame(0, $item->fresh()->stock_damaged);

        $this->post('/staff/reservasi/'.$booking->id.'/verifikasi-kontak')->assertRedirect();
        $this->post('/staff/reservasi/'.$booking->id.'/kondisi', [
            'condition' => 'good',
        ])->assertStatus(422);

        $this->post('/staff/reservasi/'.$booking->id.'/pengambilan')->assertRedirect();
        $this->post('/staff/reservasi/'.$booking->id.'/kondisi', [
            'condition' => 'good',
        ])->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'contact_verified' => true,
            'pickup_verified' => true,
            'return_condition' => 'good',
        ]);

        $this->post('/staff/reservasi/'.$booking->id.'/pengambilan', ['reset' => 1])->assertStatus(422);
        $this->post('/staff/reservasi/'.$booking->id.'/verifikasi-kontak', ['reset' => 1])->assertStatus(422);
    }

    public function test_admin_assessment_page_rejects_invalid_month_filters(): void
    {
        $admin = User::where('email', 'admin@demo.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin/penilaian-staff?month=invalid')
            ->assertSessionHasErrors('month');
    }

    public function test_admin_sees_account_management_and_audit_on_profile_only(): void
    {
        $admin = User::where('email', 'admin@demo.test')->firstOrFail();
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        AuditLog::create(['actor_id' => $staff->id, 'action' => 'dashboard.test_action', 'details' => 'Tes riwayat audit dashboard']);

        $this->actingAs($admin)->get('/profil')->assertOk()
            ->assertSee('Akun pengguna & audit', false)
            ->assertSee('Daftar akun')
            ->assertSee('staff@demo.test')
            ->assertDontSee('dashboard.test_action');
        $this->actingAs($admin)->get('/staff')->assertOk()->assertDontSee('Akun pengguna & audit', false);
        $this->actingAs($admin)->get('/staff/dashboard')->assertOk()
            ->assertSee('Riwayat audit')
            ->assertSee('dashboard.test_action');
    }

    public function test_staff_and_renter_do_not_see_account_management_on_profile(): void
    {
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $renter = User::where('email', 'user@demo.test')->firstOrFail();

        $this->actingAs($staff)->get('/profil')->assertOk()->assertDontSee('Akun pengguna & audit', false)->assertDontSee('Tambah pengguna baru');
        $this->actingAs($staff)->get('/staff')->assertOk()->assertDontSee('Akun pengguna & audit', false);
        $this->actingAs($renter)->get('/profil')->assertOk()->assertDontSee('Akun pengguna & audit', false)->assertDontSee('Riwayat audit');
        $this->actingAs($staff)->get('/staff/dashboard')->assertOk()->assertDontSee('Riwayat audit');
    }

    public function test_admin_can_change_roles_while_staff_cannot(): void
    {
        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $renter = User::where('email', 'user@demo.test')->firstOrFail();

        $this->actingAs($staff)->put('/admin/akun/'.$renter->id, ['role' => 'admin'])->assertForbidden();
        $this->actingAs(User::where('email', 'admin@demo.test')->firstOrFail())
            ->put('/admin/akun/'.$renter->id, ['role' => 'staff'])->assertRedirect();
        $this->assertSame('staff', $renter->fresh()->role);
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.role_updated']);
    }

    public function test_role_middleware_and_staff_decision_update_audit_log(): void
    {
        $user = User::where('email', 'user@demo.test')->firstOrFail();
        $this->actingAs($user)->get('/staff')->assertForbidden();

        $staff = User::where('email', 'staff@demo.test')->firstOrFail();
        $item = CampingItem::firstOrFail();
        $booking = Booking::create([
            'user_id' => $user->id,
            'booking_code' => 'CAMP-TEST0001',
            'start_date' => Carbon::today()->addDays(5),
            'end_date' => Carbon::today()->addDays(7),
            'status' => 'pending',
            'contact_name' => $user->name,
            'contact' => $user->email,
            'total' => $item->daily_rate,
        ]);
        $booking->items()->attach($item->id, ['quantity' => 1, 'unit_rate' => $item->daily_rate]);

        $this->actingAs($staff)->get('/staff/sewa')->assertOk()->assertSee('CAMP-TEST0001')->assertDontSee('Tren reservasi dan pembatalan');
        $this->actingAs($staff)->get('/staff/dashboard')->assertOk()->assertSee('Tren reservasi dan pembatalan')->assertSee('Pemakaian stok alat selama periode');
        $admin = User::where('email', 'admin@demo.test')->firstOrFail();
        $this->actingAs($admin)->get('/staff')->assertOk()->assertSee('Panel admin')->assertSee('Kelola Stok');
        $this->actingAs($admin)->get('/staff/dashboard')->assertOk()->assertSee('Tren reservasi dan pembatalan')->assertSee('Pemakaian stok alat selama periode');
        $this->actingAs($user)->get('/staff/dashboard')->assertForbidden();
        $this->actingAs($staff)->post('/staff/reservasi/'.$booking->id.'/keputusan', [
            'decision' => 'approved',
            'reason' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'approved']);
        $this->assertDatabaseHas('audit_logs', ['actor_id' => $staff->id, 'action' => 'booking.approved']);
        $this->actingAs($user)->put('/admin/akun/'.$staff->id, ['role' => 'admin'])->assertForbidden();
    }
}
