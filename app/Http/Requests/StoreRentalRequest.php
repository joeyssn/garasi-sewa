<?php

namespace App\Http\Requests;

use App\Enums\RentalType;
use App\Enums\VehicleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_type' => ['required', Rule::enum(VehicleType::class)],
            'plate_number' => ['nullable', 'required_unless:vehicle_type,bicycle', 'string', 'max:15'],
            'brand_model' => ['required', 'string', 'max:100'],
            'battery_capacity_kwh' => ['nullable', 'required_if:vehicle_type,electric_car', 'numeric', 'min:1', 'max:300'],
            'rental_type' => ['required', Rule::enum(RentalType::class)],
            'days' => ['nullable', 'required_if:rental_type,daily', 'integer', 'min:1', 'max:30'],
            'garage_id' => ['required', 'exists:garages,id'],
            'payment_proof' => ['required', 'image', 'max:5120'], // 5 MB
        ];
    }
}