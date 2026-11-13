<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:cancel-expired-bookings')]
#[Description('Membatalkan booking pending yang sudah melewati waktu pembayaran')]
class CancelExpiredBookings extends Command
{
    public function handle()
    {
        $expiredBookings = Booking::where('status', 'pending')
            ->whereNotNull('payment_due_at')
            ->where('payment_due_at', '<', now())
            ->whereNull('payment_proof')
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancel_reason' => 'Pembayaran tidak dilakukan dalam 10 menit. Pesanan dibatalkan otomatis oleh sistem.',
            ]);
        }

        $this->info("Dibatalkan {$expiredBookings->count()} booking yang kadaluarsa.");
    }
}
