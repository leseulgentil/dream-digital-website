<?php

namespace App\Http\Requests\Front;

use App\Models\AiChatSetting;
use App\Services\Ai\AiChatVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AiChatLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return rescue(function (): bool {
            $settings = AiChatSetting::current();

            return app(AiChatVisibility::class)->allowsPayload(
                $settings,
                $this->input('page_url'),
                $this->input('country_code'),
                $this->input('locale'),
            );
        }, false, false);
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'AI chat is disabled or not available on this page.',
        ], 403));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'session_id' => ['nullable', 'uuid'],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['nullable', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'page_url' => ['nullable', 'string', 'max:500'],
            'name' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'whatsapp' => ['nullable', 'string', 'max:80'],
            'company' => ['nullable', 'string', 'max:190'],
            'need' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $hasContact = collect(['email', 'phone', 'whatsapp'])
                ->contains(fn (string $field): bool => filled($this->input($field)));

            if (! $hasContact) {
                $validator->errors()->add('email', 'Ajoutez au moins un moyen de contact: email, telephone ou WhatsApp.');
            }
        });
    }
}
