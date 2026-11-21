<?php

namespace App\Services;

use App\Enums\GarageType;
use App\Enums\RentalType;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/** All rental date and cost calculations. Pure: no database access. */
class RentalPricingService
{
    public const MONTHLY_MULTIPLIER = 25;
    public const MAX_DAILY_DAYS = 30;

    /**
     * End date (inclusive).
     * Daily, 3 days from 21 Nov  -> 23 Nov.
     * Monthly from 21 Nov        -> 20 Dec.
     */
    public function endDate(Carbon $start, RentalType $type, ?int $days = null): Carbon
    {
        if ($type === RentalType::Monthly) {
            return $start->copy()->addMonthNoOverflow()->subDay();
        }

        $this->assertValidDays($days);

        return $start->copy()->addDays($days - 1);
    }

    /** Base cost: daily = price x days, monthly = 25 x daily price. */
    public function baseCost(float $dailyPrice, RentalType $type, ?int $days = null): float
    {
        if ($type === RentalType::Monthly) {
            return round($dailyPrice * self::MONTHLY_MULTIPLIER, 2);
        }

        $this->assertValidDays($days);

        return round($dailyPrice * $days, 2);
    }

    /**
     * Charging cost. Only for an electric car in an electric garage (must charge to full).
     * Assumption: charged from empty = full battery capacity x price per kWh.
     */
    public function chargingCost(GarageType $garageType, ?float $batteryKwh, ?float $pricePerKwh): float
    {
        if ($garageType !== GarageType::Electric) {
            return 0.0;
        }

        return round(($batteryKwh ?? 0) * ($pricePerKwh ?? 0), 2);
    }

    /** Total = base cost + charging cost. Nothing else is added. */
    public function totalCost(float $baseCost, float $chargingCost): float
    {
        return round($baseCost + $chargingCost, 2);
    }

    /**
     * Remaining amount for an extension = extra days x daily price.
     * The new end date must be after the old one, so the result is always positive.
     *
     * @return array{extra_days:int, cost:float}
     */
    public function extensionCost(Carbon $oldEnd, Carbon $newEnd, float $dailyPrice): array
    {
        if ($newEnd->lte($oldEnd)) {
            throw new InvalidArgumentException('The new end date must be after the current end date.');
        }

        $extraDays = (int) $oldEnd->copy()->startOfDay()->diffInDays($newEnd->copy()->startOfDay());

        return ['extra_days' => $extraDays, 'cost' => round($extraDays * $dailyPrice, 2)];
    }

    private function assertValidDays(?int $days): void
    {
        if ($days === null || $days < 1 || $days > self::MAX_DAILY_DAYS) {
            throw new InvalidArgumentException('A daily rental must be 1 to 30 days.');
        }
    }
}