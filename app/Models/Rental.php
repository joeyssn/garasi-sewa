<?php

namespace App\Models;

use App\Enums\RentalType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/** One tenant renting one garage for one vehicle. */
class Rental extends Model
{
    protected $fillable = [
        'user_id', 'vehicle_id', 'garage_id', 'rental_type', 'days',
        'start_date', 'end_date', 'base_cost', 'charging_cost', 'total_cost', 'payment_proof',
    ];

    protected function casts(): array
    {
        return [
            'rental_type' => RentalType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'base_cost' => 'decimal:2',
            'charging_cost' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function garage(): BelongsTo { return $this->belongsTo(Garage::class); }
    public function extensions(): HasMany { return $this->hasMany(RentalExtension::class); }

    /** Rentals still running on the given date: start <= date <= end. */
    public function scopeActiveOn(Builder $query, Carbon $date): Builder
    {
        return $query->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }
}