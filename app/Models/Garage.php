<?php

namespace App\Models;

use App\Enums\GarageStatus;
use App\Enums\GarageType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Garage extends Model
{
    /** @use HasFactory<\Database\Factories\GarageFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'daily_price',
        'electric_price_per_kwh',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type' => GarageType::class,
            'status' => GarageStatus::class,
            'daily_price' => 'decimal:2',
            'electric_price_per_kwh' => 'decimal:2',
        ];
    }


    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }
    
    public function monthlyPriced(): float
    {
        return $this->daily_price * 25;
    }

    public function scopeAvailableOn(Builder $query, Carbon $date): Builder
    {
        return $query->whereDoesntHave('rentals', fn (Builder $q) => 
            $q->activeOnDate($date));
    }

}
