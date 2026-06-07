<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AdminRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:admins,email',
            'password' => 'required|string|min:8',
        ];
    }
}
