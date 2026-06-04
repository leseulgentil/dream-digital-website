<?php

namespace App\Http\Requests\Front;

use App\Models\AiChatSetting;
use App\Services\Ai\AiChatVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AiChatMessageRequest extends FormRequest
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
            'message' => 'AI chat is disabled.',
        ], 403));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxMessageChars = rescue(
            fn () => max(200, min(2000, (int) (AiChatSetting::current()->max_message_chars ?? 1200))),
            1200,
            false,
        );

        return [
            'session_id' => ['nullable', 'uuid'],
            'message' => ['required', 'string', 'max:' . $maxMessageChars],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['nullable', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'page_url' => ['nullable', 'string', 'max:500'],
        ];
    }
}
