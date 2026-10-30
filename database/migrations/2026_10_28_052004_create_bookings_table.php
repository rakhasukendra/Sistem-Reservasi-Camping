<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('booking_code')->unique();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->string('status')->default('pending')->index();
            $table->string('previous_booking_status')->nullable();
            $table->string('contact_name');
            $table->string('contact');
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancellation_requested_at')->nullable()->index();
            $table->text('cancellation_review_note')->nullable();
            $table->text('refund_note')->nullable();
            $table->text('deposit_note')->nullable();
            $table->unsignedBigInteger('total')->default(0);
            $table->boolean('contact_verified')->default(false);
            $table->boolean('pickup_verified')->default(false);
            $table->string('return_condition')->default('not_returned');
            $table->unsignedBigInteger('replacement_amount')->default(0);
            $table->boolean('replacement_paid')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
