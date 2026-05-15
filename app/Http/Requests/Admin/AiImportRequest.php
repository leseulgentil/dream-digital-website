<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiImportRequest extends FormRequest
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
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['required', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'category' => ['nullable', 'string', 'max:80'],
            'file' => ['required', 'file', 'max:10240', 'mimes:md,markdown,csv,pdf'],
        ];
    }
}
