<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AiChatLeadRequest;
use App\Http\Requests\Front\AiChatMessageRequest;
use App\Models\AiChatSession;
use App\Services\Ai\AiChatResponder;
use Illuminate\Http\JsonResponse;

class AiChatController extends Controller
{
    public function message(AiChatMessageRequest $request, AiChatResponder $responder): JsonResponse
    {
        $validated = $request->validated();

        $session = $this->session($validated, $request->ip(), (string) $request->userAgent());
        $reply = $responder->reply($session, $validated['message']);

        return response()->json([
            'session_id' => $session->public_id,
            'message' => $reply['message'],
            'answered' => $reply['answered'],
            'sources' => $reply['sources'],
        ]);
    }

    public function lead(AiChatLeadRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $session = $this->session($validated, $request->ip(), (string) $request->userAgent());

        $session->lead()->updateOrCreate([], [
            'name' => $this->nullableString($validated['name'] ?? null),
            'email' => $this->nullableString(isset($validated['email']) ? strtolower($validated['email']) : null),
            'phone' => $this->nullableString($validated['phone'] ?? null),
            'whatsapp' => $this->nullableString($validated['whatsapp'] ?? null),
            'company' => $this->nullableString($validated['company'] ?? null),
            'country_code' => $validated['country_code'] ?? $session->country_code,
            'need' => $this->nullableString($validated['need'] ?? null),
            'consent' => $request->boolean('consent'),
        ]);

        $session->forceFill(['lead_status' => 'captured'])->save();

        return response()->json([
            'session_id' => $session->public_id,
            'lead_status' => $session->lead_status,
            'message' => $session->locale === 'en'
                ? 'Thank you. A Dream Digital advisor can follow up with you.'
                : 'Merci. Un conseiller Dream Digital peut reprendre contact avec vous.',
        ], 201);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function session(array $validated, ?string $ip, string $userAgent): AiChatSession
    {
        $publicId = $validated['session_id'] ?? null;

        if ($publicId) {
            $existing = AiChatSession::query()->where('public_id', $publicId)->first();

            if ($existing) {
                return $existing;
            }
        }

        return AiChatSession::create([
            'locale' => $validated['locale'],
            'country_code' => $validated['country_code'] ?? 'global',
            'page_url' => $validated['page_url'] ?? null,
            'ip_hash' => $ip ? hash('sha256', $ip) : null,
            'user_agent_hash' => $userAgent !== '' ? hash('sha256', $userAgent) : null,
        ]);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
