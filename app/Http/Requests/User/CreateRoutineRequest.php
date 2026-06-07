<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoutineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'routine_name' => 'required|string|max:100',
            'routine_time' => 'required|in:Morning,Evening',
            'steps' => 'nullable|array',
            'steps.*.product_id' => 'required_with:steps|integer|exists:products,product_id',
            'steps.*.step_order' => 'nullable|integer',
            'steps.*.instruction' => 'nullable|string',
        ];
    }
}
