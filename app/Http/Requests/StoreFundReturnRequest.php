<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreFundReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'frequency' => 'required|in:monthly,quarterly,yearly',
            'return_percentage' => 'required|numeric',
            'is_compounding' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}