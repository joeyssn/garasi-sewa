<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtendRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $rental = $this->route('rental');

        return [
            'new_end_date' => ['required', 'date', 'after:' . $rental->end_date->toDateString()],
            'payment_proof' => ['required', 'image', 'max:5120'], // 5 MB
        ];
    }
}