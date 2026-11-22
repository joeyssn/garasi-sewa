<?php

namespace App\Http\Requests;

use App\Enums\GarageType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGarageBatchRequest extends FormRequest
{
    /**
     * Only admins may create garages.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(GarageType::class)],
            'count' => ['required', 'integer', 'min:1', 'max:999'],
            'daily_price' => ['required', 'numeric', 'min:1'],
            // Price per kWh is only required for electric car garages.
            'electricity_price_per_kwh' => ['nullable', 'required_if:type,electric', 'numeric', 'min:1'],
        ];
    }
}
