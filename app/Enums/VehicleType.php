<?php

namespace App\Enums;

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
 * Biycle and motorcyle -bicycle garage only
 * car - car garage only
 * electric car - electric car garage only
 */

public function AllowedGarageType(): array
{
    return match ($this) {
        self::Bicycle, self::Motorcycle => [GarageType::Bicycle],
        self::Car => [GarageType::Car],
        self::ElectricCar => [GarageType::Electric , GarageType::Car],
    };
}

public function canUseGarage(GarageType $garageType): bool
{
    return in_array($garageType, $this->AllowedGarageType(), true);

}
/** 
 * Determines if the vehicle type requires a plate number.
 */
public function requiresPlateNumber(): bool
{
    return $this !== self::Bicycle;
}
/**
 * Determines if the vehicle type requires a license.
 */
public function requiresBatteryCapacity(): bool
{
    return $this === self::ElectricCar;
}
}