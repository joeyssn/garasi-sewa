<?php

namespace Tests\Feature;

use App\Models\Garage;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RentalFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');         // uploads go to a fake disk
        Carbon::setTestNow('2026-11-21'); // "today" is fixed
    }

    private function payload(Garage $garage, array $override = []): array
    {
        return array_merge([
            'vehicle_type' => 'electric_car', 'plate_number' => 'L 1234 AB', 'brand_model' => 'Hyundai Ioniq 5',
            'battery_capacity_kwh' => 60, 'rental_type' => 'daily', 'days' => 3,
            'garage_id' => $garage->id, 'payment_proof' => UploadedFile::fake()->image('proof.jpg'),
        ], $override);
    }

    #[Test] public function electric_car_in_electric_garage_pays_charging_and_garage_becomes_used(): void
    {
        $garage = Garage::factory()->electric()->create(); // 75,000/day, 2,500/kWh

        $this->actingAs(User::factory()->create())->post('/rentals', $this->payload($garage))->assertRedirect('/rentals');

        $rental = Rental::first();
        $this->assertEquals(225000, $rental->base_cost);     // 3 x 75,000
        $this->assertEquals(150000, $rental->charging_cost); // 60 kWh x 2,500
        $this->assertEquals(375000, $rental->total_cost);    // nothing else added
        $this->assertSame('2026-11-23', $rental->end_date->toDateString());
        $this->assertSame('used', $garage->fresh()->status->value);
    }

    #[Test] public function a_rented_garage_cannot_be_rented_again(): void
    {
        $garage = Garage::factory()->electric()->create();
        $this->actingAs(User::factory()->create())->post('/rentals', $this->payload($garage));

        $this->actingAs(User::factory()->create())
            ->post('/rentals', $this->payload($garage))
            ->assertSessionHasErrors('garage_id');
        $this->assertSame(1, Rental::count());
    }

    #[Test] public function a_motorcycle_cannot_use_a_car_garage(): void
    {
        $garage = Garage::factory()->create();
        $this->actingAs(User::factory()->create())
            ->post('/rentals', $this->payload($garage, ['vehicle_type' => 'motorcycle', 'battery_capacity_kwh' => null]))
            ->assertSessionHasErrors('garage_id');
    }

    #[Test] public function admin_extension_charges_a_positive_remaining_amount(): void
    {
        $garage = Garage::factory()->create(); // 50,000/day
        $this->actingAs(User::factory()->create())
            ->post('/rentals', $this->payload($garage, ['vehicle_type' => 'car', 'battery_capacity_kwh' => null]));
        $rental = Rental::first(); // ends 2026-11-23

        $this->actingAs(User::factory()->admin()->create())->put("/admin/extensions/{$rental->id}", [
            'new_end_date' => '2026-11-28',
            'payment_proof' => UploadedFile::fake()->image('proof2.jpg'),
        ])->assertRedirect('/admin/extensions');

        $ext = $rental->extensions()->first();
        $this->assertSame(5, $ext->extra_days);
        $this->assertEquals(250000, $ext->additional_cost);
        $this->assertGreaterThan(0, (float) $ext->additional_cost);
    }

    // Add more feature tests here (multiple garages per tenant, monthly rental, ...).
}