<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    protected $fillable = ['user_id', 'booking_code', 'start_date', 'end_date', 'status', 'previous_booking_status', 'cancellation_status', 'contact_name', 'contact', 'cancel_reason', 'cancellation_requested_at', 'cancellation_review_note', 'refund_note', 'deposit_note', 'total', 'contact_verified', 'pickup_verified', 'return_condition', 'replacement_amount', 'replacement_paid', 'payment_proof', 'payment_due_at'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date', 'cancellation_requested_at' => 'datetime', 'payment_due_at' => 'datetime', 'contact_verified' => 'boolean', 'pickup_verified' => 'boolean', 'replacement_paid' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(CampingItem::class, 'booking_items')->withPivot(['quantity', 'unit_rate'])->withTimestamps();
    }

    protected function displayStatus(): Attribute
    {
        return Attribute::get(fn (): string => match ($this->cancellation_status) {
            'requested' => 'cancellation_requested',
            'rejected' => 'cancellation_rejected',
            default => $this->status,
        });
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn (): string => match ($this->display_status) {
            'cancellation_requested' => 'Pembatalan diminta',
            'cancellation_rejected' => 'Pembatalan ditolak',
            default => ucfirst($this->status),
        });
    }

    protected function displayStatusClass(): Attribute
    {
        return Attribute::get(fn (): string => match ($this->display_status) {
            'cancellation_requested' => 'pending',
            'cancellation_rejected' => 'rejected',
            default => $this->status,
        });
    }

    public function getRentalDaysAttribute(): int
    {
        return max(1, (int) $this->start_date->diffInDays($this->end_date) + 1);
    }
}
