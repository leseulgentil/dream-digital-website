<?php

namespace Tests\Feature;

use App\Models\AiChatMessage;
use App\Models\AiChatLead;
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

    public function test_viewer_cannot_view_conversations_or_manage_settings(): void
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
            ->assertForbidden();

        $this->get("/admin/ai/conversations/{$session->id}")
            ->assertForbidden();

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

    public function test_chat_viewer_with_custom_permission_can_view_transcript_without_lead_pii(): void
    {
        $session = AiChatSession::create([
            'locale' => 'fr',
            'country_code' => 'global',
            'lead_status' => 'captured',
        ]);
        AiChatMessage::create([
            'ai_chat_session_id' => $session->id,
            'role' => 'user',
            'content' => 'Transcript visible to chat viewers.',
        ]);
        AiChatLead::create([
            'ai_chat_session_id' => $session->id,
            'name' => 'Alice Lead',
            'email' => 'alice@example.test',
            'phone' => '+243999111222',
            'whatsapp' => '+243888333444',
            'company' => 'Alice Company',
            'need' => 'Private lead need',
            'consent' => true,
        ]);

        $viewer = User::factory()->create([
            'role' => User::ROLE_VIEWER,
            'is_active' => true,
        ]);

        \App\Models\RoleProfile::query()->where('role', User::ROLE_VIEWER)->update([
            'permissions' => [
                \App\Models\RoleProfile::PERMISSION_ADMIN_ACCESS,
                \App\Models\RoleProfile::PERMISSION_AI_CHAT_VIEW,
            ],
        ]);

        $this->actingAs($viewer);

        $this->get("/admin/ai/conversations/{$session->id}")
            ->assertOk()
            ->assertSee('Transcript visible to chat viewers.')
            ->assertDontSee('Alice Lead')
            ->assertDontSee('alice@example.test')
            ->assertDontSee('+243999111222')
            ->assertDontSee('+243888333444')
            ->assertDontSee('Alice Company')
            ->assertDontSee('Private lead need');
    }
}
