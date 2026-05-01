<?php

namespace App\Http\Requests\Raiida;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApiProviderUpsertRequest extends FormRequest
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
            'slug' => ['required', 'string', 'max:120', 'alpha_dash'],
            'provider_type' => ['required', 'string', 'max:60'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'api_key' => ['nullable', 'string', 'max:2048'],
            'auth_cookie' => ['nullable', 'string', 'max:60000'],
            'base_url' => ['nullable', 'url', 'max:2048'],
            'model' => ['nullable', 'string', 'max:255'],
            'usage_endpoint' => ['nullable', 'url', 'max:2048'],
            'monthly_limit' => ['nullable', 'integer', 'min:1'],
            'limit_unit' => ['nullable', Rule::in(['requests', 'tokens', 'characters'])],
            'is_active' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
