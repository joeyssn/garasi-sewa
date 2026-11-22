<?php

namespace App\Enums;

/**
 * Tenant vehicle types and which garage types each one may use.
 */
enum VehicleType: string
{
    case Bicycle = 'bicycle';
    case Motorcycle = 'motorcycle';
    case Car = 'car';
    case ElectricCar = 'electric_car';

    public function prefix(): string
    {
        return match ($this) {
            self::Bicycle => 'Bicycle',
            self::Motorcycle => 'Motorcycle',
            self::Car => 'Car',
            self::ElectricCar => 'Electric Car',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Bicycle => 'Bicycle',
            self::Motorcycle => 'Motorcycle',
            self::Car => 'Car',
            self::ElectricCar => 'Electric Car',
        };
    }

    /**
     * Business rules:
     * - Bicycle and motorcycle: bicycle garage only
     * - Car: car garage only
     * - Electric car: car garage or electric car garage
     *
     * @return GarageType[]
     */
    public function allowedGarageTypes(): array
    {
        return match ($this) {
            self::Bicycle, self::Motorcycle => [GarageType::Bicycle],
            self::Car => [GarageType::Car],
            self::ElectricCar => [GarageType::Car, GarageType::Electric],
        };
    }

    public function canUseGarage(GarageType $garageType): bool
    {
        return in_array($garageType, $this->allowedGarageTypes(), true);
    }

    /** A plate number is required for every vehicle except a bicycle. */
    public function requiresPlateNumber(): bool
    {
        return $this !== self::Bicycle;
    }

    /** Battery capacity is only required for an electric car. */
    public function requiresBatteryCapacity(): bool
    {
        return $this === self::ElectricCar;
    }
}
