<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    public const PENDING = 'pending';
    public const CONTACTED = 'contacted';
    public const CONFIRMED = 'confirmed';
    public const REJECTED = 'rejected';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    protected $fillable = ['booking_code', 'customer_id', 'pickup_address', 'pickup_lat', 'pickup_lng', 'destination_address', 'destination_lat', 'destination_lng', 'booking_date', 'booking_time', 'passengers', 'luggage_count', 'vehicle_type_id', 'distance_km', 'estimated_duration_minutes', 'base_fare', 'distance_fare', 'time_fare', 'extra_charge', 'total_fare', 'status', 'customer_notes', 'admin_notes', 'payment_method', 'payment_status'];
    protected function casts(): array { return ['booking_date' => 'date', 'base_fare' => 'decimal:2', 'distance_fare' => 'decimal:2', 'time_fare' => 'decimal:2', 'extra_charge' => 'decimal:2', 'total_fare' => 'decimal:2']; }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function vehicleType(): BelongsTo { return $this->belongsTo(VehicleType::class); }
    public function statusHistories(): HasMany { return $this->hasMany(BookingStatusHistory::class); }
    public function review(): HasOne { return $this->hasOne(Review::class); }
}
