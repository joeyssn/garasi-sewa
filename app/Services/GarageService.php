<?php

namespace App\Services;

use App\Enums\GarageStatus;
use App\Enums\GarageType;
use App\Models\Garage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/** Creates garages in batches with automatic codes. */
class GarageService
{
    public const MAX_PER_TYPE = 999;

    /**
     * Create $count garages of $type. Code = prefix + 3 digits,
     * continuing from the last number of the same type.
     *
     * @return Collection<int, Garage>
     */
    public function createBatch(GarageType $type, int $count, float $dailyPrice, ?float $pricePerKwh = null): Collection
    {
        if ($type === GarageType::Electric && $pricePerKwh === null) {
            throw new InvalidArgumentException('Electric garages need a price per kWh.');
        }

        return DB::transaction(function () use ($type, $count, $dailyPrice, $pricePerKwh) {
            $last = $this->lastNumber($type);

            if ($last + $count > self::MAX_PER_TYPE) {
                $left = self::MAX_PER_TYPE - $last;
                throw new InvalidArgumentException("Limit is 999 garages per type. Remaining: {$left}.");
            }

            $garages = collect();
            for ($i = 1; $i <= $count; $i++) {
                $garages->push(Garage::create([
                    'code' => self::formatCode($type, $last + $i),
                    'type' => $type,
                    'daily_price' => $dailyPrice,
                    'electricity_price_per_kwh' => $type === GarageType::Electric ? $pricePerKwh : null,
                    'status' => GarageStatus::Available,
                ]));
            }

            return $garages;
        });
    }

    /** formatCode(Car, 10) => "C010" */
    public static function formatCode(GarageType $type, int $number): string
    {
        return $type->prefix() . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }

    /** Highest number used for this type, 0 if none. */
    private function lastNumber(GarageType $type): int
    {
        $lastCode = Garage::where('type', $type)->lockForUpdate()->max('code');

        return $lastCode ? (int) substr($lastCode, 1) : 0;
    }
}