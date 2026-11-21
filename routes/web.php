<?php

use App\Http\Controllers\Admin\GarageController;
use App\Http\Controllers\Admin\RentalExtensionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TenantVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Tenant\RentalController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Guests (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Tenant
Route::middleware(['auth', 'role:tenant'])->group(function () {
    Route::get('/rentals', [RentalController::class, 'index'])->name('rentals.index');
    Route::get('/rentals/create', [RentalController::class, 'create'])->name('rentals.create');
    Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store');
    Route::get('/api/available-garages', [RentalController::class, 'availableGarages'])->name('rentals.available');
    Route::get('/api/rental-quote', [RentalController::class, 'quote'])->name('rentals.quote');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/garages', [GarageController::class, 'index'])->name('garages.index');
    Route::get('/garages/create', [GarageController::class, 'create'])->name('garages.create');
    Route::post('/garages', [GarageController::class, 'store'])->name('garages.store');

    Route::get('/tenants', [TenantVerificationController::class, 'index'])->name('tenants.index');
    Route::patch('/tenants/{user}/approve', [TenantVerificationController::class, 'approve'])->name('tenants.approve');
    Route::patch('/tenants/{user}/reject', [TenantVerificationController::class, 'reject'])->name('tenants.reject');

    Route::get('/extensions', [RentalExtensionController::class, 'index'])->name('extensions.index');
    Route::get('/extensions/{rental}', [RentalExtensionController::class, 'edit'])->name('extensions.edit');
    Route::get('/extensions/{rental}/quote', [RentalExtensionController::class, 'quote'])->name('extensions.quote');
    Route::put('/extensions/{rental}', [RentalExtensionController::class, 'update'])->name('extensions.update');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});