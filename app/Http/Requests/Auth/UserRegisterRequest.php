<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:8',
            'skin_type' => 'nullable|string|max:50',
            'skin_concern' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|string|max:255',
        ];
    }
}
