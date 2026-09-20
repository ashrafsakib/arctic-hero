<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    /** @use HasFactory<\Database\Factories\PricingRuleFactory> */
    use HasFactory;
    protected $fillable = ['name', 'description', 'extra_charge', 'starts_at', 'ends_at', 'status'];
    protected function casts(): array { return ['extra_charge' => 'decimal:2', 'starts_at' => 'datetime', 'ends_at' => 'datetime']; }
}
