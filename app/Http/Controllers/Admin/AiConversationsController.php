<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatSession;
use App\Models\RoleProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiConversationsController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.ai.conversations-index', [
            'sessions' => AiChatSession::query()
                ->withCount('messages')
                ->latest('updated_at')
                ->paginate(25),
            'canManageAiChat' => $request->user()?->hasPermission(RoleProfile::PERMISSION_AI_CHAT_MANAGE) ?? false,
        ]);
    }

    public function show(Request $request, AiChatSession $session): View
    {
        $canViewLeadDetails = $request->user()?->hasPermission(RoleProfile::PERMISSION_CONTACT_LEADS_VIEW) ?? false;

        return view('admin.ai.conversation-show', [
            'session' => $session->load([
                'messages' => fn ($query) => $query->oldest(),
                ...($canViewLeadDetails ? ['lead'] : []),
            ]),
            'canViewLeadDetails' => $canViewLeadDetails,
        ]);
    }
}
