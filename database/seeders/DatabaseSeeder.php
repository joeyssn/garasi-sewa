<?php

namespace Database\Seeders;

use App\Enums\GarageType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Services\GarageService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(GarageService $garages): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@garasi.test',
            'password' => 'password',
            'role' => UserRole::Admin,
            'status' => UserStatus::Approved,
        ]);

        User::create([
            'nik' => '3578010101010001',
            'name' => 'Joey Tenant',
            'phone' => '081234567890',
            'email' => 'joey@garasi.test',
            'password' => 'password',
            'role' => UserRole::Tenant,
            'status' => UserStatus::Approved,
        ]);

        $garages->createBatch(GarageType::Bicycle, 5, 10000);
        $garages->createBatch(GarageType::Car, 5, 50000);
        $garages->createBatch(GarageType::Electric, 3, 75000, 2500);
    }
}