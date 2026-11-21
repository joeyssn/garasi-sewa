<?php

namespace App\Services;

use App\Enums\GarageStatus;
use App\Enums\RentalType;
use App\Enums\VehicleType;
use App\Models\Garage;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** New rentals and rental extensions. */
class RentalService
{
    public function __construct(private RentalPricingService $pricing)
    {
    }

    /** Dates and costs for a rental, used by the form preview and when saving. */
    public function quote(Garage $garage, RentalType $type, ?int $days, ?float $batteryKwh): array
    {
        $start = Carbon::today();
        $end = $this->pricing->endDate($start, $type, $days);
        $base = $this->pricing->baseCost((float) $garage->daily_price, $type, $days);
        $charging = $this->pricing->chargingCost(
            $garage->type,
            $batteryKwh,
            $garage->electricity_price_per_kwh !== null ? (float) $garage->electricity_price_per_kwh : null
        );

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'base_cost' => $base,
            'charging_cost' => $charging,
            'total_cost' => $this->pricing->totalCost($base, $charging),
        ];
    }

    /** Save a new rental: vehicle + rental + proof, then mark the garage 'used'. */
    public function createRental(User $tenant, array $data, UploadedFile $proof): Rental
    {
        return DB::transaction(function () use ($tenant, $data, $proof) {
            $vehicleType = VehicleType::from($data['vehicle_type']);
            $rentalType = RentalType::from($data['rental_type']);
            $days = $rentalType === RentalType::Daily ? (int) $data['days'] : null;

            $garage = Garage::whereKey($data['garage_id'])->lockForUpdate()->firstOrFail();

            if (! $vehicleType->canUseGarage($garage->type)) {
                throw ValidationException::withMessages(['garage_id' => 'This garage type does not fit the vehicle.']);
            }

            if (! Garage::whereKey($garage->id)->availableOn(Carbon::today())->exists()) {
                throw ValidationException::withMessages(['garage_id' => 'This garage is already rented. Choose another one.']);
            }

            $battery = $vehicleType->requiresBatteryCapacity() ? (float) $data['battery_capacity_kwh'] : null;
            $quote = $this->quote($garage, $rentalType, $days, $battery);

            $vehicle = $tenant->vehicles()->create([
                'type' => $vehicleType,
                'plate_number' => $vehicleType->requiresPlateNumber() ? strtoupper($data['plate_number']) : null,
                'brand_model' => $data['brand_model'],
                'battery_capacity_kwh' => $battery,
            ]);

            $rental = $tenant->rentals()->create([
                'vehicle_id' => $vehicle->id,
                'garage_id' => $garage->id,
                'rental_type' => $rentalType,
                'days' => $days,
                'start_date' => $quote['start_date'],
                'end_date' => $quote['end_date'],
                'base_cost' => $quote['base_cost'],
                'charging_cost' => $quote['charging_cost'],
                'total_cost' => $quote['total_cost'],
                'payment_proof' => $proof->store('payment-proofs', 'public'),
            ]);

            $garage->update(['status' => GarageStatus::Used]);

            return $rental;
        });
    }

    /** Remaining amount for a new end date (preview). */
    public function extensionQuote(Rental $rental, Carbon $newEnd): array
    {
        return $this->pricing->extensionCost($rental->end_date, $newEnd, (float) $rental->garage->daily_price);
    }

    /** Admin extends a rental: save history + proof, move the end date. */
    public function extend(Rental $rental, Carbon $newEnd, UploadedFile $proof, User $admin): Rental
    {
        return DB::transaction(function () use ($rental, $newEnd, $proof, $admin) {
            $quote = $this->extensionQuote($rental, $newEnd);

            $rental->extensions()->create([
                'admin_id' => $admin->id,
                'old_end_date' => $rental->end_date->toDateString(),
                'new_end_date' => $newEnd->toDateString(),
                'extra_days' => $quote['extra_days'],
                'additional_cost' => $quote['cost'],
                'payment_proof' => $proof->store('payment-proofs', 'public'),
            ]);

            $rental->update(['end_date' => $newEnd->toDateString()]);

            return $rental->fresh();
        });
    }

    /** Set garages back to 'available' when no rental is running today. */
    public function syncGarageStatuses(): int
    {
        return Garage::where('status', GarageStatus::Used)
            ->availableOn(Carbon::today())
            ->update(['status' => GarageStatus::Available]);
    }
}