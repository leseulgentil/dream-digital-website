<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiChatSettingsRequest extends FormRequest
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
            'enabled' => ['nullable', 'boolean'],
            'model' => ['required', 'string', 'max:80'],
            'max_sources' => ['required', 'integer', 'min:1', 'max:10'],
            'max_message_chars' => ['required', 'integer', 'min:200', 'max:2000'],
            'fallback_contact_mode' => ['required', Rule::in(['contact_form', 'whatsapp'])],
            'greetings.fr' => ['required', 'string', 'max:240'],
            'greetings.en' => ['required', 'string', 'max:240'],
            'system_prompt' => ['required', 'string', 'max:4000'],
        ];
    }
}
