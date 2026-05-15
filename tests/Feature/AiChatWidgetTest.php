<?php

namespace Tests\Feature;

use App\Models\AiChatSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiChatWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_is_hidden_when_disabled(): void
    {
        AiChatSetting::current()->update(['enabled' => false]);

        $response = $this->get('/fr/contact');

        $response
            ->assertOk()
            ->assertDontSee('dd-ai-chat-widget', false);
    }

    public function test_widget_is_visible_when_enabled(): void
    {
        AiChatSetting::current()->update(['enabled' => true]);

        $response = $this->get('/fr/contact');

        $response
            ->assertOk()
            ->assertSee('dd-ai-chat-widget', false)
            ->assertSee('data-ai-chat-endpoint', false);
    }

    public function test_widget_is_only_included_on_public_front_pages(): void
    {
        AiChatSetting::current()->update(['enabled' => true]);

        $this->actingAs(User::factory()->create());

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('dd-ai-chat-widget', false);

        $this->get('/fr/contact')
            ->assertOk()
            ->assertSee('dd-ai-chat-widget', false);
    }

    public function test_widget_uses_public_endpoint_and_does_not_expose_openai_key(): void
    {
        config(['services.openai.api_key' => 'sk-test-secret-from-config']);

        AiChatSetting::current()->update(['enabled' => true]);

        $response = $this->get('/fr/contact');

        $response
            ->assertOk()
            ->assertSee('data-ai-chat-endpoint="' . route('front.ai-chat.message') . '"', false)
            ->assertSee('<meta name="csrf-token"', false)
            ->assertDontSee('sk-test-secret-from-config', false)
            ->assertDontSee('OPENAI_API_KEY', false);
    }

    public function test_widget_uses_configured_message_limit(): void
    {
        AiChatSetting::current()->update([
            'enabled' => true,
            'max_message_chars' => 1800,
        ]);

        $this->get('/fr/contact')
            ->assertOk()
            ->assertSee('maxlength="1800"', false);
    }
}
