<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoutineStepsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization checks are performed in the Policy, but we can set authorization to true here.
        return true;
    }

    public function rules(): array
    {
        return [
            'steps' => 'required|array|min:1',
            'steps.*.product_id' => 'required|integer|exists:products,product_id',
            'steps.*.step_order' => 'required|integer',
            'steps.*.instruction' => 'nullable|string',
        ];
    }
}
