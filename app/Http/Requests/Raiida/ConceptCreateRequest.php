<?php

namespace App\Http\Requests\Raiida;

use Illuminate\Foundation\Http\FormRequest;

class ConceptCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'skill_id' => ['required', 'integer'],
            'unite_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'week' => ['nullable', 'integer', 'min:1', 'max:52'],
            'status' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
