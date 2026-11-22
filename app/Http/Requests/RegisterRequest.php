<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Anyone (a guest) may register as a tenant; admin approval happens afterwards.
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => ['required', 'digits:16', 'unique:users,nik'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'regex:/^(\+62|0)[0-9]{8,13}$/'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }
}