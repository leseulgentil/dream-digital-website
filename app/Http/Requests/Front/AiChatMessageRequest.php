<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiChatMessageRequest extends FormRequest
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
            'session_id' => ['nullable', 'uuid'],
            'message' => ['required', 'string', 'max:1200'],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['nullable', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'page_url' => ['nullable', 'string', 'max:500'],
        ];
    }
}
