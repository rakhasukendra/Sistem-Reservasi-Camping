<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('bookings')
            ->whereNotNull('cancellation_requested_at')
            ->orWhereNotNull('cancellation_review_note')
            ->orWhereNotNull('cancellation_status')
            ->update([
                'cancellation_status' => null,
                'cancellation_requested_at' => null,
                'cancellation_review_note' => null,
                'cancel_reason' => null,
                'previous_booking_status' => null,
                'refund_note' => null,
                'deposit_note' => null,
            ]);
    }

    public function down(): void {}
};
