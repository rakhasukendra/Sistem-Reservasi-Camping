<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CampingItem extends Model
{
    protected $fillable = ['name', 'category', 'description', 'stock_total', 'stock_damaged', 'daily_rate', 'is_available'];

    protected function casts(): array
    {
        return ['is_available' => 'boolean'];
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_items')->withPivot(['quantity', 'unit_rate'])->withTimestamps();
    }
}
