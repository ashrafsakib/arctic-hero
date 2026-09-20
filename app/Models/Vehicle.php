<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    protected $fillable = ['vehicle_type_id', 'name', 'brand', 'model', 'registration_number', 'color', 'year', 'status', 'image'];
    public function vehicleType(): BelongsTo { return $this->belongsTo(VehicleType::class); }
}
