<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->numerify('################'),
            'name' => fake()->name(),
            'phone' => '0812' . fake()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => UserRole::Tenant,
            'status' => UserStatus::Approved,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['nik' => null, 'role' => UserRole::Admin]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => UserStatus::Pending]);
    }
}