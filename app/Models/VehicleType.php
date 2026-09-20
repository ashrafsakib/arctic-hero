<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description', 'passenger_capacity', 'luggage_capacity', 'base_fare', 'per_km_rate', 'per_minute_rate', 'image', 'status'];
    protected function casts(): array { return ['base_fare' => 'decimal:2', 'per_km_rate' => 'decimal:2', 'per_minute_rate' => 'decimal:2']; }
    public function vehicles(): HasMany { return $this->hasMany(Vehicle::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
}
