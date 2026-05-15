<?php

namespace Tests\Feature;

use App\Models\AiChatSetting;
use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\RoleProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AiChatSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_chat_tables_are_created(): void
    {
        foreach ([
            'ai_chat_settings',
            'ai_knowledge_sources',
            'ai_knowledge_chunks',
            'ai_chat_sessions',
            'ai_chat_messages',
            'ai_chat_leads',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected table [{$table}] to exist.");
        }
    }

    public function test_ai_chat_permissions_are_available_and_defaulted_by_role(): void
    {
        $permissions = RoleProfile::availablePermissions();

        $this->assertSame('Assistant IA - conversations', $permissions[RoleProfile::PERMISSION_AI_CHAT_VIEW]);
        $this->assertSame('Assistant IA - parametres', $permissions[RoleProfile::PERMISSION_AI_CHAT_MANAGE]);
        $this->assertSame('Base IA - lecture', $permissions[RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW]);
        $this->assertSame('Base IA - edition', $permissions[RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE]);

        foreach ([
            User::ROLE_OWNER,
            User::ROLE_ADMIN,
            User::ROLE_EDITOR,
        ] as $role) {
            $this->assertContains(RoleProfile::PERMISSION_AI_CHAT_VIEW, RoleProfile::defaultPermissionsFor($role));
            $this->assertContains(RoleProfile::PERMISSION_AI_CHAT_MANAGE, RoleProfile::defaultPermissionsFor($role));
            $this->assertContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW, RoleProfile::defaultPermissionsFor($role));
            $this->assertContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE, RoleProfile::defaultPermissionsFor($role));
        }

        $this->assertContains(RoleProfile::PERMISSION_AI_CHAT_VIEW, RoleProfile::defaultPermissionsFor(User::ROLE_VIEWER));
        $this->assertNotContains(RoleProfile::PERMISSION_AI_CHAT_MANAGE, RoleProfile::defaultPermissionsFor(User::ROLE_VIEWER));
        $this->assertContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW, RoleProfile::defaultPermissionsFor(User::ROLE_VIEWER));
        $this->assertNotContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE, RoleProfile::defaultPermissionsFor(User::ROLE_VIEWER));
    }

    public function test_ai_chat_setting_current_creates_defaults(): void
    {
        $settings = AiChatSetting::current();

        $this->assertFalse($settings->enabled);
        $this->assertSame('gpt-5.4-mini', $settings->model);
        $this->assertSame(5, $settings->max_sources);
    }

    public function test_ai_knowledge_source_has_chunks_relation(): void
    {
        $source = AiKnowledgeSource::create([
            'type' => AiKnowledgeSource::TYPE_MANUAL,
            'title' => 'Guide Dream Digital',
        ]);

        $chunk = AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Couverture',
            'content' => 'Dream Digital couvre les corridors RDC, CI et SN.',
        ]);

        $this->assertTrue($source->chunks->contains($chunk));
    }
}
