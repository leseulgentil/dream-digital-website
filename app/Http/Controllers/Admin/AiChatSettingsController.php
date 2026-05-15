<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiChatSettingsRequest;
use App\Models\AiChatSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiChatSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.ai.settings', [
            'settings' => AiChatSetting::current(),
        ]);
    }

    public function update(AiChatSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        AiChatSetting::current()->update([
            'enabled' => $request->boolean('enabled'),
            'model' => $validated['model'],
            'max_sources' => $validated['max_sources'],
            'max_message_chars' => $validated['max_message_chars'],
            'fallback_contact_mode' => $validated['fallback_contact_mode'],
            'greetings' => $validated['greetings'],
            'system_prompt' => $validated['system_prompt'],
        ]);

        return redirect()
            ->route('admin.ai.settings.edit')
            ->with('status', 'Parametres de l assistant IA mis a jour.');
    }
}
