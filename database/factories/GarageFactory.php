<?php

namespace Database\Factories;

use App\Enums\GarageStatus;
use App\Enums\GarageType;
use Illuminate\Database\Eloquent\Factories\Factory;

class GarageFactory extends Factory
{
    private static int $seq = 0;

    public function definition(): array
    {
        self::$seq++;

        return [
            'code' => 'C' . str_pad((string) self::$seq, 3, '0', STR_PAD_LEFT),
            'type' => GarageType::Car,
            'daily_price' => 50000,
            'electricity_price_per_kwh' => null,
            'status' => GarageStatus::Available,
        ];
    }

    public function electric(): static
    {
        return $this->state(fn () => [
            'code' => 'E' . str_pad((string) self::$seq, 3, '0', STR_PAD_LEFT),
            'type' => GarageType::Electric,
            'daily_price' => 75000,
            'electricity_price_per_kwh' => 2500,
        ]);
    }
}