<?php

namespace App\Enums;

enum GarageType: string
{
    case Bicycle = 'bicycle';
    case Car = 'car';
    case Electric = 'electric';

public function prefix(): string
{
    return match ($this) {
        self::Bicycle => 'S',
        self::Car => 'C',
        self::Electric => 'E',
    };
}

public function label(): string
{
    return match ($this) {
        self::Bicycle => 'Bicycle Garage',
        self::Car => 'Car Garage',
        self::Electric => 'Electric Car Garage',
    };
}
}