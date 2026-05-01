<?php

namespace App\Http\Requests\Raiida;

use Illuminate\Foundation\Http\FormRequest;

class ClassifyVocabularyMetadataRequest extends FormRequest
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
            'limit' => ['nullable', 'integer', 'min:1', 'max:500'],
            'grade' => ['nullable', 'regex:/^N[1-6]$/'],
            'period' => ['nullable', 'regex:/^P[1-5]$/'],
            'week' => ['nullable', 'regex:/^SEM[1-6]$/'],
            'queue' => ['nullable', 'boolean'],
            'dry_run' => ['nullable', 'boolean'],
            'force' => ['nullable', 'boolean'],
        ];
    }
}

