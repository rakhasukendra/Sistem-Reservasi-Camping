<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('booking_items')->delete();
        DB::table('bookings')->delete();
        DB::table('audit_logs')->delete();
    }

    public function down(): void {}
};
