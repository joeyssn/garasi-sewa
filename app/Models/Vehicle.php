<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vehicle extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'plate_number',
        'brand_model',
        'battery_capacity_kwh',
    ];

    protected function casts(): array
    {
        return [
            'type' => VehicleType::class,
            'battery_capacity_kwh' => 'decimal:2',
        ];
    }

    public function garage(): BelongsTo
    {
        return $this->belongsTo(Garage::class);
    }
}
