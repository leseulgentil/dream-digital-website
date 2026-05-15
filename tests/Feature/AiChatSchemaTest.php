<?php

namespace Tests\Feature;

use App\Models\AiChatSetting;
use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\RoleProfile;
use App\Models\User;
use Database\Seeders\RoleProfileSeeder;
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

    public function test_ai_chat_migration_matches_specified_column_definitions(): void
    {
        $migration = file_get_contents(database_path('migrations/2026_05_15_000007_create_ai_chat_tables.php'));

        $this->assertStringContainsString("\$table->unsignedSmallInteger('max_sources')->default(5);", $migration);
        $this->assertStringContainsString("\$table->unsignedSmallInteger('max_message_chars')->default(1200);", $migration);
        $this->assertSame(4, substr_count($migration, "\$table->string('country_code', 12)"));
        $this->assertStringContainsString("\$table->string('ip_hash')->nullable();", $migration);
        $this->assertStringContainsString("\$table->string('user_agent_hash')->nullable();", $migration);
        $this->assertStringNotContainsString("\$table->string('ip_hash', 64)->nullable();", $migration);
        $this->assertStringNotContainsString("\$table->string('user_agent_hash', 64)->nullable();", $migration);
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

    public function test_ai_permissions_migration_merges_missing_permissions_without_removing_customizations(): void
    {
        $migration = require database_path('migrations/2026_05_15_000008_merge_ai_permissions_into_role_profiles.php');

        RoleProfile::query()->where('role', User::ROLE_ADMIN)->update([
            'permissions' => [
                RoleProfile::PERMISSION_ADMIN_ACCESS,
                'custom.permission',
                RoleProfile::PERMISSION_AI_CHAT_VIEW,
            ],
        ]);
        RoleProfile::query()->where('role', User::ROLE_VIEWER)->update([
            'permissions' => [
                RoleProfile::PERMISSION_ADMIN_ACCESS,
                'viewer.custom',
            ],
        ]);

        $migration->up();

        $adminPermissions = RoleProfile::query()->where('role', User::ROLE_ADMIN)->value('permissions');
        $viewerPermissions = RoleProfile::query()->where('role', User::ROLE_VIEWER)->value('permissions');

        $this->assertContains('custom.permission', $adminPermissions);
        $this->assertContains(RoleProfile::PERMISSION_AI_CHAT_VIEW, $adminPermissions);
        $this->assertContains(RoleProfile::PERMISSION_AI_CHAT_MANAGE, $adminPermissions);
        $this->assertContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW, $adminPermissions);
        $this->assertContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE, $adminPermissions);

        $this->assertContains('viewer.custom', $viewerPermissions);
        $this->assertContains(RoleProfile::PERMISSION_AI_CHAT_VIEW, $viewerPermissions);
        $this->assertNotContains(RoleProfile::PERMISSION_AI_CHAT_MANAGE, $viewerPermissions);
        $this->assertContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW, $viewerPermissions);
        $this->assertNotContains(RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE, $viewerPermissions);

        $migration->down();

        $this->assertSame(
            [RoleProfile::PERMISSION_ADMIN_ACCESS, 'custom.permission'],
            RoleProfile::query()->where('role', User::ROLE_ADMIN)->first()->permissions,
        );
        $this->assertSame(
            [RoleProfile::PERMISSION_ADMIN_ACCESS, 'viewer.custom'],
            RoleProfile::query()->where('role', User::ROLE_VIEWER)->first()->permissions,
        );
    }

    public function test_role_profile_seeder_merges_defaults_without_removing_customizations(): void
    {
        RoleProfile::query()->where('role', User::ROLE_EDITOR)->update([
            'permissions' => [
                RoleProfile::PERMISSION_ADMIN_ACCESS,
                'editor.custom',
            ],
        ]);

        (new RoleProfileSeeder)->run();

        $permissions = RoleProfile::query()->where('role', User::ROLE_EDITOR)->first()->permissions;

        $this->assertContains('editor.custom', $permissions);
        foreach (RoleProfile::defaultPermissionsFor(User::ROLE_EDITOR) as $permission) {
            $this->assertContains($permission, $permissions);
        }
    }

    public function test_ai_chat_setting_current_creates_defaults(): void
    {
        $settings = AiChatSetting::current();

        $this->assertFalse($settings->enabled);
        $this->assertSame('gpt-5.4-mini', $settings->model);
        $this->assertSame(5, $settings->max_sources);
        $this->assertSame([
            'fr' => 'Bonjour, comment puis-je aider ?',
            'en' => 'Hello, how can I help?',
        ], $settings->greetings);
        $this->assertSame(['pages' => ['*']], $settings->display_rules);
        $this->assertSame(
            "Tu es l'assistant Dream Digital. Reponds uniquement avec les informations presentes dans la base de connaissances fournie. Si l'information n'est pas disponible, dis que Dream Digital ne peut pas confirmer et propose de contacter l'equipe. Ne cherche pas sur internet. N'invente pas de prix, delais, pays couverts, conditions contractuelles ou coordonnees.",
            $settings->system_prompt,
        );
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
