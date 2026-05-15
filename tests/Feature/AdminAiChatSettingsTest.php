<?php

namespace Tests\Feature;

use App\Models\AiChatMessage;
use App\Models\AiChatSession;
use App\Models\AiChatSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAiChatSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_settings(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $response = $this->put('/admin/ai/settings', [
            'enabled' => '1',
            'model' => 'gpt-5.4-mini',
            'max_sources' => 4,
            'max_message_chars' => 900,
            'fallback_contact_mode' => 'whatsapp',
            'greetings' => [
                'fr' => 'Bonjour',
                'en' => 'Hello',
            ],
            'system_prompt' => AiChatSetting::defaultSystemPrompt(),
        ]);

        $response->assertRedirect('/admin/ai/settings');

        $settings = AiChatSetting::current()->fresh();
        $this->assertTrue($settings->enabled);
        $this->assertSame(4, $settings->max_sources);
        $this->assertSame('whatsapp', $settings->fallback_contact_mode);
        $this->assertSame('Bonjour', $settings->greetings['fr']);
    }

    public function test_owner_can_view_conversation(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_OWNER,
            'is_active' => true,
        ]));

        $session = AiChatSession::create([
            'locale' => 'fr',
            'country_code' => 'global',
            'lead_status' => 'none',
        ]);
        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => 'Bonjour',
        ]);
        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => 'Bonjour, comment puis-je aider ?',
        ]);

        $this->get("/admin/ai/conversations/{$session->id}")
            ->assertOk()
            ->assertSee('Bonjour');
    }

    public function test_viewer_can_view_conversations_but_cannot_manage_settings(): void
    {
        $session = AiChatSession::create([
            'locale' => 'fr',
            'country_code' => 'global',
            'lead_status' => 'none',
        ]);

        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_VIEWER,
            'is_active' => true,
        ]));

        $this->get('/admin/ai/conversations')
            ->assertOk()
            ->assertSee($session->public_id)
            ->assertDontSee('/admin/ai/settings');

        $this->get("/admin/ai/conversations/{$session->id}")
            ->assertOk();

        $this->get('/admin/ai/settings')
            ->assertForbidden();

        $this->put('/admin/ai/settings', [
            'enabled' => '1',
            'model' => 'gpt-5.4-mini',
            'max_sources' => 4,
            'max_message_chars' => 900,
            'fallback_contact_mode' => 'whatsapp',
            'greetings' => [
                'fr' => 'Bonjour',
                'en' => 'Hello',
            ],
            'system_prompt' => AiChatSetting::defaultSystemPrompt(),
        ])->assertForbidden();
    }
}
