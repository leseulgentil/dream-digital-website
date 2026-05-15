<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
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
        ]);
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
}
