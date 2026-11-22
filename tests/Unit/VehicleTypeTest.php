<?php

namespace Tests\Unit;

use App\Enums\GarageType;
use App\Enums\VehicleType;
use App\Services\GarageService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class VehicleTypeTest extends TestCase
{
    #[Test] public function bicycles_and_motorcycles_only_in_bicycle_garage(): void
    {
        foreach ([VehicleType::Bicycle, VehicleType::Motorcycle] as $v) {
            $this->assertTrue($v->canUseGarage(GarageType::Bicycle));
            $this->assertFalse($v->canUseGarage(GarageType::Car));
            $this->assertFalse($v->canUseGarage(GarageType::Electric));
        }
    }

    #[Test] public function cars_only_in_car_garage(): void
    {
        $this->assertTrue(VehicleType::Car->canUseGarage(GarageType::Car));
        $this->assertFalse(VehicleType::Car->canUseGarage(GarageType::Electric));
    }

    #[Test] public function electric_cars_in_car_or_electric_garage(): void
    {
        $this->assertTrue(VehicleType::ElectricCar->canUseGarage(GarageType::Car));
        $this->assertTrue(VehicleType::ElectricCar->canUseGarage(GarageType::Electric));
        $this->assertFalse(VehicleType::ElectricCar->canUseGarage(GarageType::Bicycle));
    }

    #[Test] public function garage_code_format(): void
    {
        $this->assertSame('S001', GarageService::formatCode(GarageType::Bicycle, 1));
        $this->assertSame('C010', GarageService::formatCode(GarageType::Car, 10));
        $this->assertSame('E999', GarageService::formatCode(GarageType::Electric, 999));
    }
}