<?php

namespace Tests\Unit;

use App\Enums\GarageType;
use App\Enums\RentalType;
use App\Services\RentalPricingService;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RentalPricingServiceTest extends TestCase
{
    private RentalPricingService $pricing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricing = new RentalPricingService();
    }

    #[Test] public function daily_cost_is_price_times_days(): void
    {
        $this->assertSame(150000.0, $this->pricing->baseCost(50000, RentalType::Daily, 3));
    }

    #[Test] public function monthly_cost_is_25_times_daily_price(): void
    {
        $this->assertSame(1250000.0, $this->pricing->baseCost(50000, RentalType::Monthly));
    }

    #[Test] public function more_than_30_days_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->pricing->baseCost(50000, RentalType::Daily, 31);
    }

    #[Test] public function zero_days_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->pricing->endDate(Carbon::parse('2026-11-21'), RentalType::Daily, 0);
    }

    #[Test] public function daily_end_date_is_inclusive(): void
    {
        $end = $this->pricing->endDate(Carbon::parse('2026-11-21'), RentalType::Daily, 3);
        $this->assertSame('2026-11-23', $end->toDateString());
    }

    #[Test] public function monthly_end_date_is_one_month_minus_one_day(): void
    {
        $end = $this->pricing->endDate(Carbon::parse('2026-11-21'), RentalType::Monthly);
        $this->assertSame('2026-12-20', $end->toDateString());
    }

    #[Test] public function charging_only_in_electric_garage(): void
    {
        $this->assertSame(150000.0, $this->pricing->chargingCost(GarageType::Electric, 60, 2500));
        $this->assertSame(0.0, $this->pricing->chargingCost(GarageType::Car, 60, 2500));
    }

    #[Test] public function total_is_base_plus_charging_with_nothing_added(): void
    {
        $this->assertSame(375000.0, $this->pricing->totalCost(225000, 150000));
    }

    #[Test] public function extension_cost_is_always_positive(): void
    {
        $q = $this->pricing->extensionCost(Carbon::parse('2026-11-23'), Carbon::parse('2026-11-26'), 50000);
        $this->assertSame(3, $q['extra_days']);
        $this->assertSame(150000.0, $q['cost']);
        $this->assertGreaterThan(0, $q['cost']);
    }

    #[Test] public function extending_to_an_earlier_date_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->pricing->extensionCost(Carbon::parse('2026-11-23'), Carbon::parse('2026-11-22'), 50000);
    }
}