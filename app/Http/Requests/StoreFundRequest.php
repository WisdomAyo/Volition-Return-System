<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'starting_balance' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|size:3',
            'is_active' => 'sometimes|boolean',
        ];
    }
}