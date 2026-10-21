<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;

    protected $fillable = ['vehicle_type_id', 'name', 'brand', 'model', 'registration_number', 'color', 'year', 'status', 'image'];
    public function vehicleType(): BelongsTo { return $this->belongsTo(VehicleType::class); }

    public function getDisplayImageUrlAttribute(): string
    {
        $image = $this->image ?: $this->vehicleType?->image;

        if ($image && Storage::disk('public')->exists($image)) {
            return asset('storage/'.$image);
        }

        $typeName = Str::lower($this->vehicleType?->name ?? '');
        $fallbackImage = match (true) {
            Str::contains($typeName, 'premium') => 'taxi-driver.jpg',
            Str::contains($typeName, ['mini', 'van']) => 'northern-road.jpg',
            default => 'taxi-city.jpg',
        };

        return asset('images/'.$fallbackImage);
    }
}
