<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    protected $fillable = ['user_id', 'booking_code', 'start_date', 'end_date', 'status', 'previous_booking_status', 'contact_name', 'contact', 'cancel_reason', 'cancellation_requested_at', 'cancellation_review_note', 'refund_note', 'deposit_note', 'total', 'contact_verified', 'pickup_verified', 'return_condition', 'replacement_amount', 'replacement_paid'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'cancellation_requested_at' => 'datetime', 'contact_verified' => 'boolean', 'pickup_verified' => 'boolean', 'replacement_paid' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(CampingItem::class, 'booking_items')->withPivot(['quantity', 'unit_rate'])->withTimestamps();
    }

    public function getRentalDaysAttribute(): int
    {
        return max(1, (int) $this->start_date->diffInDays($this->end_date) + 1);
    }
}
