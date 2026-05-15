<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiKnowledgeRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:180'],
            'content' => ['required', 'string', 'max:12000'],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['required', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'category' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
