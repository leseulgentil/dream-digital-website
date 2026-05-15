# AI Chat Knowledge Base Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a public AI chat assistant that answers only from a local, admin-published Dream Digital knowledge base with required Markdown, CSV, and PDF imports.

**Architecture:** Laravel owns all data, retrieval, prompts, chat state, and imports. The browser widget calls Laravel only; Laravel retrieves published local chunks, sends those chunks to the AI provider with strict instructions, stores the transcript, and falls back to human contact when no local source supports the answer.

**Tech Stack:** Laravel 12, PostgreSQL full-text search, Blade admin views, Vite frontend assets, OpenAI Responses API through Laravel HTTP client, `smalot/pdfparser` for server-side PDF text extraction.

---

## File Structure

- Create `database/migrations/2026_05_15_000007_create_ai_chat_tables.php`: AI settings, knowledge sources/chunks, sessions, messages, leads.
- Create `app/Models/AiChatSetting.php`: singleton-style settings model and defaults.
- Create `app/Models/AiKnowledgeSource.php`: imported/manual source metadata.
- Create `app/Models/AiKnowledgeChunk.php`: published searchable blocks and query scopes.
- Create `app/Models/AiChatSession.php`: public visitor session.
- Create `app/Models/AiChatMessage.php`: transcript rows and source references.
- Create `app/Models/AiChatLead.php`: captured human handoff details.
- Create `app/Services/Ai/AiKnowledgeChunker.php`: deterministic text chunking.
- Create `app/Services/Ai/AiKnowledgeImporter.php`: Markdown, CSV, PDF extraction and draft chunk creation.
- Create `app/Services/Ai/AiKnowledgeRetriever.php`: local-only PostgreSQL full-text retrieval.
- Create `app/Services/Ai/AiChatResponder.php`: guardrails, provider call, fallback, message persistence.
- Create `app/Http/Controllers/Admin/AiKnowledgeController.php`: list/create/edit/publish/delete knowledge entries and chunks.
- Create `app/Http/Controllers/Admin/AiImportController.php`: upload and import files.
- Create `app/Http/Controllers/Admin/AiConversationsController.php`: conversation and lead review.
- Create `app/Http/Controllers/Admin/AiChatSettingsController.php`: enable chat and configure prompt/greeting/model.
- Create `app/Http/Controllers/Front/AiChatController.php`: public chat session/message endpoint.
- Create `app/Http/Requests/Admin/AiKnowledgeRequest.php`, `AiImportRequest.php`, `AiChatSettingsRequest.php`: admin validation.
- Create `app/Http/Requests/Front/AiChatMessageRequest.php`: public validation.
- Create views under `resources/views/admin/ai/`: `knowledge-index.blade.php`, `knowledge-edit.blade.php`, `import.blade.php`, `conversations-index.blade.php`, `conversation-show.blade.php`, `settings.blade.php`.
- Create `resources/views/front/components/ai-chat-widget.blade.php`: public widget markup.
- Create `resources/assets/js/dd-ai-chat-widget.js` and `resources/assets/css/dd-ai-chat-widget.css`: public widget behavior and styling.
- Modify `app/Models/RoleProfile.php`: add AI permissions.
- Modify `database/seeders/RoleProfileSeeder.php`: seed AI permissions for owner/admin and view-only where appropriate.
- Modify `resources/menu/verticalMenu.json`: add `Assistant IA` admin menu items.
- Modify `routes/web.php`: add admin and public chat routes.
- Modify `resources/views/layouts/commonMaster.blade.php`: include widget when enabled.
- Modify `config/services.php`: add chat model, timeout, and provider flags under `openai`.
- Modify `composer.json`: require `smalot/pdfparser`.

---

### Task 1: Schema, Models, Permissions, And Menu

**Files:**
- Create: `database/migrations/2026_05_15_000007_create_ai_chat_tables.php`
- Create: `app/Models/AiChatSetting.php`
- Create: `app/Models/AiKnowledgeSource.php`
- Create: `app/Models/AiKnowledgeChunk.php`
- Create: `app/Models/AiChatSession.php`
- Create: `app/Models/AiChatMessage.php`
- Create: `app/Models/AiChatLead.php`
- Modify: `app/Models/RoleProfile.php`
- Modify: `database/seeders/RoleProfileSeeder.php`
- Modify: `resources/menu/verticalMenu.json`
- Test: `tests/Feature/AiChatSchemaTest.php`

- [ ] **Step 1: Write the failing schema and permission test**

Create `tests/Feature/AiChatSchemaTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\AiChatSetting;
use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\RoleProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AiChatSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_chat_tables_exist(): void
    {
        foreach ([
            'ai_chat_settings',
            'ai_knowledge_sources',
            'ai_knowledge_chunks',
            'ai_chat_sessions',
            'ai_chat_messages',
            'ai_chat_leads',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table {$table}");
        }
    }

    public function test_ai_permissions_are_registered(): void
    {
        $permissions = RoleProfile::availablePermissions();

        $this->assertArrayHasKey('ai_chat.view', $permissions);
        $this->assertArrayHasKey('ai_chat.manage', $permissions);
        $this->assertArrayHasKey('ai_knowledge.view', $permissions);
        $this->assertArrayHasKey('ai_knowledge.manage', $permissions);
    }

    public function test_settings_defaults_are_available(): void
    {
        $settings = AiChatSetting::current();

        $this->assertFalse($settings->enabled);
        $this->assertSame('gpt-5.4-mini', $settings->model);
        $this->assertSame(5, $settings->max_sources);
    }

    public function test_source_has_chunks_relation(): void
    {
        $source = AiKnowledgeSource::create([
            'type' => 'manual',
            'title' => 'FAQ SMS',
            'locale' => 'fr',
            'country_code' => 'global',
            'status' => 'draft',
            'metadata' => [],
            'created_by_id' => null,
        ]);

        $chunk = AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Question SMS',
            'content' => 'Dream Digital accompagne les flux SMS A2P.',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'status' => 'published',
            'priority' => 10,
        ]);

        $this->assertTrue($source->chunks->contains($chunk));
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --filter=AiChatSchemaTest`

Expected: FAIL because the AI models and tables do not exist.

- [ ] **Step 3: Add the migration**

Create `database/migrations/2026_05_15_000007_create_ai_chat_tables.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_chat_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('model')->default('gpt-5.4-mini');
            $table->unsignedSmallInteger('max_sources')->default(5);
            $table->unsignedSmallInteger('max_message_chars')->default(1200);
            $table->string('provider')->default('openai');
            $table->string('fallback_contact_mode')->default('contact_form');
            $table->json('greetings')->nullable();
            $table->text('system_prompt')->nullable();
            $table->json('display_rules')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_knowledge_sources', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->string('original_filename')->nullable();
            $table->string('stored_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('locale', 2)->default('fr');
            $table->string('country_code', 12)->default('global');
            $table->string('status')->default('draft');
            $table->json('metadata')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['locale', 'country_code', 'status']);
        });

        Schema::create('ai_knowledge_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_knowledge_source_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('locale', 2)->default('fr');
            $table->string('country_code', 12)->default('global');
            $table->string('category')->nullable();
            $table->string('status')->default('draft');
            $table->integer('priority')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['locale', 'country_code', 'status', 'priority']);
        });

        Schema::create('ai_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('locale', 2)->default('fr');
            $table->string('country_code', 12)->default('global');
            $table->string('page_url')->nullable();
            $table->string('ip_hash')->nullable();
            $table->string('user_agent_hash')->nullable();
            $table->string('lead_status')->default('none');
            $table->timestamps();
        });

        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_chat_session_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->text('content');
            $table->json('source_chunk_ids')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_chat_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_chat_session_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('company')->nullable();
            $table->string('country_code', 12)->nullable();
            $table->text('need')->nullable();
            $table->boolean('consent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_leads');
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
        Schema::dropIfExists('ai_knowledge_chunks');
        Schema::dropIfExists('ai_knowledge_sources');
        Schema::dropIfExists('ai_chat_settings');
    }
};
```

- [ ] **Step 4: Add the models**

Create the six model files with these exact core definitions:

```php
// app/Models/AiChatSetting.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatSetting extends Model
{
    protected $fillable = ['enabled', 'model', 'max_sources', 'max_message_chars', 'provider', 'fallback_contact_mode', 'greetings', 'system_prompt', 'display_rules'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean', 'greetings' => 'array', 'display_rules' => 'array'];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'enabled' => false,
            'model' => 'gpt-5.4-mini',
            'max_sources' => 5,
            'max_message_chars' => 1200,
            'provider' => 'openai',
            'fallback_contact_mode' => 'contact_form',
            'greetings' => ['fr' => 'Bonjour, comment puis-je aider ?', 'en' => 'Hello, how can I help?'],
            'system_prompt' => self::defaultSystemPrompt(),
            'display_rules' => ['pages' => ['*']],
        ]);
    }

    public static function defaultSystemPrompt(): string
    {
        return "Tu es l'assistant Dream Digital. Reponds uniquement avec les informations presentes dans la base de connaissances fournie. Si l'information n'est pas disponible, dis que Dream Digital ne peut pas confirmer et propose de contacter l'equipe. Ne cherche pas sur internet. N'invente pas de prix, delais, pays couverts, conditions contractuelles ou coordonnees.";
    }
}
```

```php
// app/Models/AiKnowledgeSource.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiKnowledgeSource extends Model
{
    public const TYPE_MANUAL = 'manual';
    public const TYPE_MARKDOWN = 'markdown';
    public const TYPE_CSV = 'csv';
    public const TYPE_PDF = 'pdf';

    protected $fillable = ['type', 'title', 'original_filename', 'stored_path', 'mime_type', 'locale', 'country_code', 'status', 'metadata', 'created_by_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function chunks()
    {
        return $this->hasMany(AiKnowledgeChunk::class);
    }
}
```

```php
// app/Models/AiKnowledgeChunk.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AiKnowledgeChunk extends Model
{
    protected $fillable = ['ai_knowledge_source_id', 'title', 'content', 'locale', 'country_code', 'category', 'status', 'priority', 'expires_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }

    public function source()
    {
        return $this->belongsTo(AiKnowledgeSource::class, 'ai_knowledge_source_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn (Builder $inner) => $inner->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
```

```php
// app/Models/AiChatSession.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AiChatSession extends Model
{
    protected $fillable = ['public_id', 'locale', 'country_code', 'page_url', 'ip_hash', 'user_agent_hash', 'lead_status'];

    protected static function booted(): void
    {
        static::creating(fn (self $session) => $session->public_id ??= (string) Str::uuid());
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class);
    }

    public function lead()
    {
        return $this->hasOne(AiChatLead::class);
    }
}
```

```php
// app/Models/AiChatMessage.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    protected $fillable = ['ai_chat_session_id', 'role', 'content', 'source_chunk_ids', 'metadata'];

    protected function casts(): array
    {
        return ['source_chunk_ids' => 'array', 'metadata' => 'array'];
    }

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id');
    }
}
```

```php
// app/Models/AiChatLead.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatLead extends Model
{
    protected $fillable = ['ai_chat_session_id', 'name', 'email', 'phone', 'whatsapp', 'company', 'country_code', 'need', 'consent'];

    protected function casts(): array
    {
        return ['consent' => 'boolean'];
    }
}
```

- [ ] **Step 5: Register permissions and menu**

In `app/Models/RoleProfile.php`, add constants:

```php
public const PERMISSION_AI_CHAT_VIEW = 'ai_chat.view';
public const PERMISSION_AI_CHAT_MANAGE = 'ai_chat.manage';
public const PERMISSION_AI_KNOWLEDGE_VIEW = 'ai_knowledge.view';
public const PERMISSION_AI_KNOWLEDGE_MANAGE = 'ai_knowledge.manage';
```

Add them to `availablePermissions()`:

```php
self::PERMISSION_AI_CHAT_VIEW => 'Assistant IA - conversations',
self::PERMISSION_AI_CHAT_MANAGE => 'Assistant IA - parametres',
self::PERMISSION_AI_KNOWLEDGE_VIEW => 'Base IA - lecture',
self::PERMISSION_AI_KNOWLEDGE_MANAGE => 'Base IA - edition',
```

Owner receives all automatically. Add all four to admin/editor default permissions; add only `ai_chat.view` and `ai_knowledge.view` to viewer.

In `resources/menu/verticalMenu.json`, add after `Leads`:

```json
{
  "url": "admin/ai/conversations",
  "name": "Assistant IA",
  "icon": "dd-menu-icon icon-base bx bx-message-rounded-dots",
  "slug": "admin.ai",
  "permission": "ai_chat.view"
},
{
  "url": "admin/ai/knowledge",
  "name": "Base IA",
  "icon": "dd-menu-icon icon-base bx bx-brain",
  "slug": "admin.ai.knowledge",
  "permission": "ai_knowledge.view"
}
```

- [ ] **Step 6: Run the schema test**

Run: `php artisan test --filter=AiChatSchemaTest`

Expected: PASS.

- [ ] **Step 7: Commit**

Run:

```bash
git add database/migrations/2026_05_15_000007_create_ai_chat_tables.php app/Models/AiChatSetting.php app/Models/AiKnowledgeSource.php app/Models/AiKnowledgeChunk.php app/Models/AiChatSession.php app/Models/AiChatMessage.php app/Models/AiChatLead.php app/Models/RoleProfile.php database/seeders/RoleProfileSeeder.php resources/menu/verticalMenu.json tests/Feature/AiChatSchemaTest.php
git commit -m "feat: add ai chat data model"
```

---

### Task 2: Local Knowledge Importers

**Files:**
- Modify: `composer.json`
- Create: `app/Services/Ai/AiKnowledgeChunker.php`
- Create: `app/Services/Ai/AiKnowledgeImporter.php`
- Create: `tests/Feature/AiKnowledgeImporterTest.php`

- [ ] **Step 1: Add PDF parser dependency**

Run: `composer require smalot/pdfparser`

Expected: `composer.json` and `composer.lock` include `smalot/pdfparser`.

- [ ] **Step 2: Write failing importer tests**

Create `tests/Feature/AiKnowledgeImporterTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Services\Ai\AiKnowledgeImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiKnowledgeImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_markdown_import_creates_draft_chunks(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->createWithContent('faq.md', "# SMS A2P\nDream Digital gere les flux SMS A2P avec supervision.");

        $source = app(AiKnowledgeImporter::class)->import($file, [
            'title' => 'FAQ SMS',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'created_by_id' => null,
        ]);

        $this->assertSame(AiKnowledgeSource::TYPE_MARKDOWN, $source->type);
        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'ai_knowledge_source_id' => $source->id,
            'status' => 'draft',
            'locale' => 'fr',
            'country_code' => 'global',
        ]);
    }

    public function test_csv_import_maps_question_answer_rows(): void
    {
        Storage::fake('local');
        $csv = "question,answer,category,country,locale\nQuels pays?,RDC CI Congo,coverage,global,fr\n";
        $file = UploadedFile::fake()->createWithContent('faq.csv', $csv);

        $source = app(AiKnowledgeImporter::class)->import($file, [
            'title' => 'FAQ CSV',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'created_by_id' => null,
        ]);

        $chunk = $source->chunks()->firstOrFail();

        $this->assertStringContainsString('Quels pays?', $chunk->content);
        $this->assertStringContainsString('RDC CI Congo', $chunk->content);
        $this->assertSame('coverage', $chunk->category);
    }

    public function test_unsupported_file_type_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('notes.docx', 'bad');

        $this->expectException(\InvalidArgumentException::class);

        app(AiKnowledgeImporter::class)->import($file, [
            'title' => 'Bad',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'misc',
            'created_by_id' => null,
        ]);
    }
}
```

- [ ] **Step 3: Run importer tests to verify failure**

Run: `php artisan test --filter=AiKnowledgeImporterTest`

Expected: FAIL because importer services do not exist.

- [ ] **Step 4: Create the chunker**

Create `app/Services/Ai/AiKnowledgeChunker.php`:

```php
<?php

namespace App\Services\Ai;

use Illuminate\Support\Str;

class AiKnowledgeChunker
{
    public function chunks(string $text, int $maxChars = 1200): array
    {
        $clean = trim(preg_replace('/[ \t]+/', ' ', str_replace(["\r\n", "\r"], "\n", strip_tags($text))) ?? '');
        if ($clean === '') {
            return [];
        }

        $paragraphs = collect(preg_split('/\n{2,}/', $clean) ?: [])
            ->map(fn (string $paragraph) => trim($paragraph))
            ->filter()
            ->values();

        $chunks = [];
        $current = '';

        foreach ($paragraphs as $paragraph) {
            if (Str::length($current . "\n\n" . $paragraph) > $maxChars && $current !== '') {
                $chunks[] = trim($current);
                $current = '';
            }

            $current = trim($current === '' ? $paragraph : $current . "\n\n" . $paragraph);
        }

        if ($current !== '') {
            $chunks[] = $current;
        }

        return $chunks;
    }
}
```

- [ ] **Step 5: Create the importer**

Create `app/Services/Ai/AiKnowledgeImporter.php`:

```php
<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeSource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Smalot\PdfParser\Parser;

class AiKnowledgeImporter
{
    public function __construct(private readonly AiKnowledgeChunker $chunker)
    {
    }

    public function import(UploadedFile $file, array $metadata): AiKnowledgeSource
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $type = match ($extension) {
            'md', 'markdown' => AiKnowledgeSource::TYPE_MARKDOWN,
            'csv' => AiKnowledgeSource::TYPE_CSV,
            'pdf' => AiKnowledgeSource::TYPE_PDF,
            default => throw new InvalidArgumentException('Unsupported AI knowledge file type.'),
        };

        $path = $file->store('ai-knowledge-sources');

        $source = AiKnowledgeSource::create([
            'type' => $type,
            'title' => $metadata['title'],
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'mime_type' => $file->getMimeType(),
            'locale' => $metadata['locale'],
            'country_code' => $metadata['country_code'],
            'status' => 'draft',
            'metadata' => ['category' => $metadata['category'] ?? null],
            'created_by_id' => $metadata['created_by_id'] ?? null,
        ]);

        foreach ($this->extractChunks($type, Storage::path($path), $metadata) as $index => $chunk) {
            $source->chunks()->create([
                'title' => $chunk['title'] ?? ($metadata['title'] . ' #' . ($index + 1)),
                'content' => $chunk['content'],
                'locale' => $chunk['locale'] ?? $metadata['locale'],
                'country_code' => $chunk['country_code'] ?? $metadata['country_code'],
                'category' => $chunk['category'] ?? ($metadata['category'] ?? null),
                'status' => 'draft',
                'priority' => 0,
            ]);
        }

        return $source->load('chunks');
    }

    private function extractChunks(string $type, string $path, array $metadata): array
    {
        return match ($type) {
            AiKnowledgeSource::TYPE_MARKDOWN => $this->textChunks(file_get_contents($path) ?: '', $metadata['title']),
            AiKnowledgeSource::TYPE_CSV => $this->csvChunks($path, $metadata),
            AiKnowledgeSource::TYPE_PDF => $this->textChunks((new Parser())->parseFile($path)->getText(), $metadata['title']),
            default => [],
        };
    }

    private function textChunks(string $text, string $title): array
    {
        return collect($this->chunker->chunks($text))
            ->map(fn (string $content, int $index) => ['title' => $title . ' #' . ($index + 1), 'content' => $content])
            ->all();
    }

    private function csvChunks(string $path, array $metadata): array
    {
        $handle = fopen($path, 'rb');
        $headers = fgetcsv($handle) ?: [];
        $chunks = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row) ?: [];
            $question = trim((string) ($data['question'] ?? $data['title'] ?? ''));
            $answer = trim((string) ($data['answer'] ?? $data['content'] ?? ''));
            if ($question === '' && $answer === '') {
                continue;
            }

            $chunks[] = [
                'title' => $question !== '' ? $question : $metadata['title'],
                'content' => trim($question . "\n\n" . $answer),
                'category' => $data['category'] ?? ($metadata['category'] ?? null),
                'country_code' => $data['country'] ?? $metadata['country_code'],
                'locale' => $data['locale'] ?? $metadata['locale'],
            ];
        }

        fclose($handle);

        return $chunks;
    }
}
```

- [ ] **Step 6: Run importer tests**

Run: `php artisan test --filter=AiKnowledgeImporterTest`

Expected: PASS.

- [ ] **Step 7: Commit**

Run:

```bash
git add composer.json composer.lock app/Services/Ai/AiKnowledgeChunker.php app/Services/Ai/AiKnowledgeImporter.php tests/Feature/AiKnowledgeImporterTest.php
git commit -m "feat: add ai knowledge importers"
```

---

### Task 3: Admin Knowledge Base And Import Screens

**Files:**
- Create: `app/Http/Requests/Admin/AiKnowledgeRequest.php`
- Create: `app/Http/Requests/Admin/AiImportRequest.php`
- Create: `app/Http/Controllers/Admin/AiKnowledgeController.php`
- Create: `app/Http/Controllers/Admin/AiImportController.php`
- Create: `resources/views/admin/ai/knowledge-index.blade.php`
- Create: `resources/views/admin/ai/knowledge-edit.blade.php`
- Create: `resources/views/admin/ai/import.blade.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/AdminAiKnowledgeTest.php`

- [ ] **Step 1: Write failing admin tests**

Create `tests/Feature/AdminAiKnowledgeTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAiKnowledgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_manual_knowledge_entry(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_OWNER, 'is_active' => true]);

        $response = $this->actingAs($admin)->post('/admin/ai/knowledge', [
            'title' => 'WhatsApp Support',
            'content' => 'Le support WhatsApp est propose uniquement via le numero configure dans le CMS.',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'support',
            'status' => 'published',
            'priority' => 20,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'title' => 'WhatsApp Support',
            'status' => 'published',
            'locale' => 'fr',
        ]);
    }

    public function test_admin_can_import_markdown_file(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['role' => User::ROLE_OWNER, 'is_active' => true]);

        $response = $this->actingAs($admin)->post('/admin/ai/import', [
            'title' => 'FAQ Import',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'file' => UploadedFile::fake()->createWithContent('faq.md', 'Dream Digital couvre les besoins CPaaS.'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ai_knowledge_sources', ['title' => 'FAQ Import', 'type' => 'markdown']);
    }

    public function test_admin_can_publish_chunk(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_OWNER, 'is_active' => true]);
        $source = AiKnowledgeSource::create(['type' => 'manual', 'title' => 'Draft', 'locale' => 'fr', 'country_code' => 'global', 'status' => 'draft']);
        $chunk = AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Draft Chunk',
            'content' => 'Texte',
            'locale' => 'fr',
            'country_code' => 'global',
            'status' => 'draft',
        ]);

        $this->actingAs($admin)->put("/admin/ai/knowledge/{$chunk->id}", [
            'title' => 'Published Chunk',
            'content' => 'Texte publie',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'status' => 'published',
            'priority' => 5,
        ])->assertRedirect();

        $this->assertSame('published', $chunk->fresh()->status);
    }
}
```

- [ ] **Step 2: Run admin tests to verify failure**

Run: `php artisan test --filter=AdminAiKnowledgeTest`

Expected: FAIL because routes/controllers/views do not exist.

- [ ] **Step 3: Add request validation**

Create `app/Http/Requests/Admin/AiKnowledgeRequest.php`:

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiKnowledgeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'content' => ['required', 'string', 'max:12000'],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['required', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'category' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
```

Create `app/Http/Requests/Admin/AiImportRequest.php`:

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiImportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['required', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'category' => ['nullable', 'string', 'max:80'],
            'file' => ['required', 'file', 'max:10240', 'mimes:md,markdown,csv,pdf'],
        ];
    }
}
```

- [ ] **Step 4: Add controllers and routes**

Create controllers with CRUD actions. Add routes inside the existing authenticated admin group in `routes/web.php`:

```php
Route::prefix('admin/ai')->name('admin.ai.')->group(function () {
    Route::get('/knowledge', [AdminAiKnowledgeController::class, 'index'])
        ->middleware('admin.permission:ai_knowledge.view')
        ->name('knowledge.index');
    Route::post('/knowledge', [AdminAiKnowledgeController::class, 'store'])
        ->middleware('admin.permission:ai_knowledge.manage')
        ->name('knowledge.store');
    Route::get('/knowledge/{chunk}/edit', [AdminAiKnowledgeController::class, 'edit'])
        ->middleware('admin.permission:ai_knowledge.manage')
        ->name('knowledge.edit');
    Route::put('/knowledge/{chunk}', [AdminAiKnowledgeController::class, 'update'])
        ->middleware('admin.permission:ai_knowledge.manage')
        ->name('knowledge.update');
    Route::delete('/knowledge/{chunk}', [AdminAiKnowledgeController::class, 'destroy'])
        ->middleware('admin.permission:ai_knowledge.manage')
        ->name('knowledge.destroy');
    Route::get('/import', [AdminAiImportController::class, 'create'])
        ->middleware('admin.permission:ai_knowledge.manage')
        ->name('import.create');
    Route::post('/import', [AdminAiImportController::class, 'store'])
        ->middleware('admin.permission:ai_knowledge.manage')
        ->name('import.store');
});
```

Add the matching `use` statements:

```php
use App\Http\Controllers\Admin\AiImportController as AdminAiImportController;
use App\Http\Controllers\Admin\AiKnowledgeController as AdminAiKnowledgeController;
```

- [ ] **Step 5: Add minimal views**

Create views using the admin card/table style already used by `resources/views/admin/pages/index.blade.php`. Required controls:

- `knowledge-index.blade.php`: filters for locale/country/status, table of chunks, create form link, import link.
- `knowledge-edit.blade.php`: title/content/locale/country/category/status/priority/expires fields.
- `import.blade.php`: title/locale/country/category/file upload form.

The edit form posts to `admin.ai.knowledge.update`; the import form posts to `admin.ai.import.store`.

- [ ] **Step 6: Run admin tests**

Run: `php artisan test --filter=AdminAiKnowledgeTest`

Expected: PASS.

- [ ] **Step 7: Commit**

Run:

```bash
git add app/Http/Requests/Admin/AiKnowledgeRequest.php app/Http/Requests/Admin/AiImportRequest.php app/Http/Controllers/Admin/AiKnowledgeController.php app/Http/Controllers/Admin/AiImportController.php resources/views/admin/ai/knowledge-index.blade.php resources/views/admin/ai/knowledge-edit.blade.php resources/views/admin/ai/import.blade.php routes/web.php tests/Feature/AdminAiKnowledgeTest.php
git commit -m "feat: add ai knowledge admin"
```

---

### Task 4: Retrieval And Guarded Chat Backend

**Files:**
- Create: `app/Services/Ai/AiKnowledgeRetriever.php`
- Create: `app/Services/Ai/AiChatResponder.php`
- Create: `app/Http/Requests/Front/AiChatMessageRequest.php`
- Create: `app/Http/Controllers/Front/AiChatController.php`
- Modify: `config/services.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/AiChatResponderTest.php`

- [ ] **Step 1: Write failing responder tests**

Create `tests/Feature/AiChatResponderTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\AiChatSession;
use App\Models\AiChatSetting;
use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Services\Ai\AiChatResponder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiChatResponderTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_falls_back_without_published_knowledge(): void
    {
        AiChatSetting::current()->update(['enabled' => true]);
        $session = AiChatSession::create(['locale' => 'fr', 'country_code' => 'global']);

        $result = app(AiChatResponder::class)->reply($session, 'Quel est le prix WhatsApp ?');

        $this->assertFalse($result['answered']);
        $this->assertStringContainsString('ne peut pas confirmer', $result['message']);
        $this->assertDatabaseHas('ai_chat_messages', ['role' => 'assistant']);
    }

    public function test_chat_sends_only_retrieved_chunks_to_provider(): void
    {
        config(['services.openai.api_key' => 'test-key']);
        AiChatSetting::current()->update(['enabled' => true, 'model' => 'gpt-test']);
        $source = AiKnowledgeSource::create(['type' => 'manual', 'title' => 'Coverage', 'locale' => 'fr', 'country_code' => 'global', 'status' => 'published']);
        AiKnowledgeChunk::create([
            'ai_knowledge_source_id' => $source->id,
            'title' => 'Pays',
            'content' => 'Dream Digital opere en RDC, Cote d Ivoire et Congo.',
            'locale' => 'fr',
            'country_code' => 'global',
            'status' => 'published',
            'priority' => 20,
        ]);

        Http::fake([
            'api.openai.com/*' => Http::response([
                'output_text' => 'Dream Digital opere en RDC, Cote d Ivoire et Congo.',
            ], 200),
        ]);

        $session = AiChatSession::create(['locale' => 'fr', 'country_code' => 'cd']);
        $result = app(AiChatResponder::class)->reply($session, 'Quels pays couvrez-vous ?');

        $this->assertTrue($result['answered']);
        $this->assertStringContainsString('RDC', $result['message']);
        Http::assertSent(fn ($request) => str_contains($request->body(), 'Dream Digital opere en RDC'));
    }
}
```

- [ ] **Step 2: Run responder tests to verify failure**

Run: `php artisan test --filter=AiChatResponderTest`

Expected: FAIL because responder and retriever do not exist.

- [ ] **Step 3: Add retrieval service**

Create `app/Services/Ai/AiKnowledgeRetriever.php`:

```php
<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeChunk;
use Illuminate\Support\Collection;

class AiKnowledgeRetriever
{
    public function retrieve(string $message, string $locale, string $countryCode, int $limit = 5): Collection
    {
        $driver = config('database.default');

        return AiKnowledgeChunk::query()
            ->published()
            ->where('locale', $locale)
            ->whereIn('country_code', [$countryCode, 'global'])
            ->when($driver === 'pgsql', function ($query) use ($message) {
                $query->whereRaw("to_tsvector('simple', coalesce(title,'') || ' ' || coalesce(content,'')) @@ plainto_tsquery('simple', ?)", [$message])
                    ->orderByRaw("ts_rank(to_tsvector('simple', coalesce(title,'') || ' ' || coalesce(content,'')), plainto_tsquery('simple', ?)) DESC", [$message]);
            }, function ($query) use ($message) {
                foreach (preg_split('/\s+/', $message) ?: [] as $term) {
                    $term = trim($term);
                    if ($term !== '') {
                        $query->where(fn ($inner) => $inner->where('title', 'like', "%{$term}%")->orWhere('content', 'like', "%{$term}%"));
                    }
                }
            })
            ->orderByDesc('priority')
            ->limit($limit)
            ->get();
    }
}
```

- [ ] **Step 4: Add guarded responder**

Create `app/Services/Ai/AiChatResponder.php` with:

```php
<?php

namespace App\Services\Ai;

use App\Models\AiChatSession;
use App\Models\AiChatSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AiChatResponder
{
    public function __construct(private readonly AiKnowledgeRetriever $retriever)
    {
    }

    public function reply(AiChatSession $session, string $message): array
    {
        $settings = AiChatSetting::current();
        $session->messages()->create(['role' => 'user', 'content' => $message]);

        $chunks = $this->retriever->retrieve($message, $session->locale, $session->country_code, $settings->max_sources);

        if ($chunks->isEmpty()) {
            return $this->fallback($session);
        }

        try {
            $answer = $this->callProvider($settings, $message, $chunks->map(fn ($chunk) => [
                'id' => $chunk->id,
                'title' => $chunk->title,
                'content' => $chunk->content,
            ])->all());
        } catch (RuntimeException) {
            return $this->fallback($session);
        }

        $session->messages()->create([
            'role' => 'assistant',
            'content' => $answer,
            'source_chunk_ids' => $chunks->pluck('id')->all(),
        ]);

        return ['answered' => true, 'message' => $answer, 'sources' => $chunks->pluck('id')->all()];
    }

    private function fallback(AiChatSession $session): array
    {
        $message = $session->locale === 'en'
            ? 'Dream Digital cannot confirm this from the available knowledge base. Please contact the team through the contact form or WhatsApp.'
            : "Dream Digital ne peut pas confirmer cette information avec la base disponible. Merci de contacter l'equipe via le formulaire ou WhatsApp.";

        $session->messages()->create(['role' => 'assistant', 'content' => $message, 'source_chunk_ids' => []]);

        return ['answered' => false, 'message' => $message, 'sources' => []];
    }

    private function callProvider(AiChatSetting $settings, string $message, array $sources): string
    {
        if (blank(config('services.openai.api_key'))) {
            throw new RuntimeException('Missing OpenAI API key.');
        }

        $response = Http::acceptJson()
            ->withToken((string) config('services.openai.api_key'))
            ->timeout((int) config('services.openai.timeout', 45))
            ->post(rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/') . '/responses', [
                'model' => $settings->model,
                'instructions' => $settings->system_prompt ?: AiChatSetting::defaultSystemPrompt(),
                'input' => [[
                    'role' => 'user',
                    'content' => [[
                        'type' => 'input_text',
                        'text' => "Sources locales autorisees:\n" . json_encode($sources, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\nQuestion visiteur:\n{$message}",
                    ]],
                ]],
                'max_output_tokens' => 900,
            ]);

        if ($response->failed()) {
            throw new RuntimeException('AI provider failed.');
        }

        return trim((string) ($response->json('output_text') ?? ''));
    }
}
```

- [ ] **Step 5: Add public request, controller, and route**

Create `app/Http/Requests/Front/AiChatMessageRequest.php`:

```php
<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiChatMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'session_id' => ['nullable', 'uuid'],
            'message' => ['required', 'string', 'max:1200'],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'country_code' => ['nullable', Rule::in(['global', 'cd', 'ci', 'cg'])],
            'page_url' => ['nullable', 'string', 'max:500'],
        ];
    }
}
```

Create `app/Http/Controllers/Front/AiChatController.php`:

```php
<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AiChatMessageRequest;
use App\Models\AiChatSession;
use App\Services\Ai\AiChatResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AiChatController extends Controller
{
    public function message(AiChatMessageRequest $request, AiChatResponder $responder): JsonResponse
    {
        $data = $request->validated();
        $session = AiChatSession::query()->where('public_id', $data['session_id'] ?? '')->first()
            ?? AiChatSession::create([
                'locale' => $data['locale'],
                'country_code' => $data['country_code'] ?? 'global',
                'page_url' => $data['page_url'] ?? null,
                'ip_hash' => hash('sha256', (string) $request->ip()),
                'user_agent_hash' => hash('sha256', (string) $request->userAgent()),
            ]);

        $reply = $responder->reply($session, $data['message']);

        return response()->json([
            'session_id' => $session->public_id,
            'message' => $reply['message'],
            'answered' => $reply['answered'],
        ]);
    }
}
```

Add public route:

```php
Route::post('/ai-chat/message', [\App\Http\Controllers\Front\AiChatController::class, 'message'])
    ->middleware('throttle:12,1')
    ->name('front.ai-chat.message');
```

- [ ] **Step 6: Run responder tests**

Run: `php artisan test --filter=AiChatResponderTest`

Expected: PASS.

- [ ] **Step 7: Commit**

Run:

```bash
git add app/Services/Ai/AiKnowledgeRetriever.php app/Services/Ai/AiChatResponder.php app/Http/Requests/Front/AiChatMessageRequest.php app/Http/Controllers/Front/AiChatController.php config/services.php routes/web.php tests/Feature/AiChatResponderTest.php
git commit -m "feat: add guarded ai chat backend"
```

---

### Task 5: Public Chat Widget

**Files:**
- Create: `resources/views/front/components/ai-chat-widget.blade.php`
- Create: `resources/assets/js/dd-ai-chat-widget.js`
- Create: `resources/assets/css/dd-ai-chat-widget.css`
- Modify: `resources/views/layouts/commonMaster.blade.php`
- Modify: `vite.config.js` or existing Vite entry configuration if needed
- Test: `tests/Feature/AiChatWidgetTest.php`

- [ ] **Step 1: Write failing widget test**

Create `tests/Feature/AiChatWidgetTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\AiChatSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiChatWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_is_hidden_when_disabled(): void
    {
        AiChatSetting::current()->update(['enabled' => false]);

        $this->get('/fr/contact')
            ->assertOk()
            ->assertDontSee('dd-ai-chat-widget', false);
    }

    public function test_widget_is_visible_when_enabled(): void
    {
        AiChatSetting::current()->update(['enabled' => true]);

        $this->get('/fr/contact')
            ->assertOk()
            ->assertSee('dd-ai-chat-widget', false)
            ->assertSee('data-ai-chat-endpoint', false);
    }
}
```

- [ ] **Step 2: Run widget test to verify failure**

Run: `php artisan test --filter=AiChatWidgetTest`

Expected: FAIL because the widget is not included.

- [ ] **Step 3: Add Blade widget**

Create `resources/views/front/components/ai-chat-widget.blade.php`:

```blade
@php
  $locale = app()->getLocale() ?: 'fr';
  $settings = \App\Models\AiChatSetting::current();
  $greeting = data_get($settings->greetings, $locale, $locale === 'en' ? 'Hello, how can I help?' : 'Bonjour, comment puis-je aider ?');
@endphp

<div
  id="dd-ai-chat-widget"
  class="dd-ai-chat-widget"
  data-ai-chat-endpoint="{{ route('front.ai-chat.message') }}"
  data-locale="{{ $locale }}"
  data-country="{{ session('dd_country_code', 'global') }}"
  data-page-url="{{ url()->current() }}"
>
  <button type="button" class="dd-ai-chat-toggle" aria-expanded="false" aria-controls="dd-ai-chat-panel">
    <i class="icon-base bx bx-message-rounded-dots"></i>
    <span>Chat</span>
  </button>
  <section id="dd-ai-chat-panel" class="dd-ai-chat-panel" hidden>
    <header>
      <strong>Dream Digital</strong>
      <button type="button" class="dd-ai-chat-close" aria-label="Fermer">
        <i class="icon-base bx bx-x"></i>
      </button>
    </header>
    <div class="dd-ai-chat-messages" aria-live="polite">
      <p class="dd-ai-chat-message dd-ai-chat-message-assistant">{{ $greeting }}</p>
    </div>
    <form class="dd-ai-chat-form">
      <textarea name="message" rows="2" maxlength="1200" required placeholder="{{ $locale === 'en' ? 'Write your question...' : 'Ecrivez votre question...' }}"></textarea>
      <button type="submit">
        <i class="icon-base bx bx-send"></i>
      </button>
    </form>
  </section>
</div>
```

- [ ] **Step 4: Include widget and assets**

In `resources/views/layouts/commonMaster.blade.php`, before `</body>`, add:

```blade
@if(\Illuminate\Support\Facades\Schema::hasTable('ai_chat_settings') && \App\Models\AiChatSetting::current()->enabled)
  @include('front.components.ai-chat-widget')
  @vite(['resources/assets/css/dd-ai-chat-widget.css', 'resources/assets/js/dd-ai-chat-widget.js'])
@endif
```

- [ ] **Step 5: Add JavaScript and CSS**

Create `resources/assets/js/dd-ai-chat-widget.js`:

```js
document.querySelectorAll('#dd-ai-chat-widget').forEach((widget) => {
  const toggle = widget.querySelector('.dd-ai-chat-toggle');
  const panel = widget.querySelector('.dd-ai-chat-panel');
  const close = widget.querySelector('.dd-ai-chat-close');
  const form = widget.querySelector('.dd-ai-chat-form');
  const textarea = form.querySelector('textarea');
  const messages = widget.querySelector('.dd-ai-chat-messages');
  let sessionId = window.localStorage.getItem('dd_ai_chat_session_id');

  const append = (role, text) => {
    const node = document.createElement('p');
    node.className = `dd-ai-chat-message dd-ai-chat-message-${role}`;
    node.textContent = text;
    messages.appendChild(node);
    messages.scrollTop = messages.scrollHeight;
  };

  toggle.addEventListener('click', () => {
    panel.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    textarea.focus();
  });

  close.addEventListener('click', () => {
    panel.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = textarea.value.trim();
    if (!message) return;
    textarea.value = '';
    append('user', message);

    const response = await fetch(widget.dataset.aiChatEndpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        session_id: sessionId,
        message,
        locale: widget.dataset.locale || 'fr',
        country_code: widget.dataset.country || 'global',
        page_url: widget.dataset.pageUrl || window.location.href,
      }),
    });

    const payload = await response.json();
    if (payload.session_id) {
      sessionId = payload.session_id;
      window.localStorage.setItem('dd_ai_chat_session_id', sessionId);
    }
    append('assistant', payload.message || 'Service indisponible pour le moment.');
  });
});
```

Create compact, fixed-position CSS in `resources/assets/css/dd-ai-chat-widget.css` with stable dimensions:

```css
.dd-ai-chat-widget{position:fixed;right:1rem;bottom:1rem;z-index:1080;font-family:inherit}
.dd-ai-chat-toggle{display:flex;align-items:center;gap:.5rem;border:0;border-radius:8px;background:#0f766e;color:#fff;padding:.75rem 1rem;box-shadow:0 12px 28px rgba(15,23,42,.18)}
.dd-ai-chat-panel{position:absolute;right:0;bottom:3.75rem;width:min(360px,calc(100vw - 2rem));height:520px;background:#fff;border:1px solid #d7dee8;border-radius:8px;box-shadow:0 18px 50px rgba(15,23,42,.2);overflow:hidden}
.dd-ai-chat-panel header{height:56px;display:flex;align-items:center;justify-content:space-between;padding:0 1rem;border-bottom:1px solid #e6ebf1}
.dd-ai-chat-close{border:0;background:transparent;font-size:1.25rem}
.dd-ai-chat-messages{height:384px;overflow:auto;padding:1rem;background:#f8fafc}
.dd-ai-chat-message{max-width:88%;padding:.65rem .75rem;border-radius:8px;font-size:.92rem;line-height:1.45}
.dd-ai-chat-message-user{margin-left:auto;background:#0f766e;color:#fff}
.dd-ai-chat-message-assistant{margin-right:auto;background:#fff;border:1px solid #e2e8f0;color:#1f2937}
.dd-ai-chat-form{display:grid;grid-template-columns:1fr 44px;gap:.5rem;padding:.75rem;border-top:1px solid #e6ebf1}
.dd-ai-chat-form textarea{resize:none;border:1px solid #cbd5e1;border-radius:8px;padding:.65rem}
.dd-ai-chat-form button{border:0;border-radius:8px;background:#2563eb;color:#fff}
```

- [ ] **Step 6: Run widget tests and build**

Run:

```bash
php artisan test --filter=AiChatWidgetTest
npm run build
```

Expected: tests PASS and Vite build succeeds.

- [ ] **Step 7: Commit**

Run:

```bash
git add resources/views/front/components/ai-chat-widget.blade.php resources/assets/js/dd-ai-chat-widget.js resources/assets/css/dd-ai-chat-widget.css resources/views/layouts/commonMaster.blade.php tests/Feature/AiChatWidgetTest.php
git commit -m "feat: add public ai chat widget"
```

---

### Task 6: Admin Conversations, Settings, And Lead Capture

**Files:**
- Create: `app/Http/Requests/Admin/AiChatSettingsRequest.php`
- Create: `app/Http/Controllers/Admin/AiConversationsController.php`
- Create: `app/Http/Controllers/Admin/AiChatSettingsController.php`
- Create: `resources/views/admin/ai/conversations-index.blade.php`
- Create: `resources/views/admin/ai/conversation-show.blade.php`
- Create: `resources/views/admin/ai/settings.blade.php`
- Modify: `app/Http/Controllers/Front/AiChatController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/AdminAiChatSettingsTest.php`

- [ ] **Step 1: Write failing settings and conversation tests**

Create `tests/Feature/AdminAiChatSettingsTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\AiChatSession;
use App\Models\AiChatSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAiChatSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_ai_chat_settings(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER, 'is_active' => true]);

        $this->actingAs($owner)->put('/admin/ai/settings', [
            'enabled' => '1',
            'model' => 'gpt-5.4-mini',
            'max_sources' => 4,
            'max_message_chars' => 900,
            'fallback_contact_mode' => 'whatsapp',
            'greetings' => ['fr' => 'Bonjour', 'en' => 'Hello'],
            'system_prompt' => AiChatSetting::defaultSystemPrompt(),
        ])->assertRedirect();

        $this->assertTrue(AiChatSetting::current()->enabled);
        $this->assertSame(4, AiChatSetting::current()->max_sources);
    }

    public function test_admin_can_view_conversation(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER, 'is_active' => true]);
        $session = AiChatSession::create(['locale' => 'fr', 'country_code' => 'global']);
        $session->messages()->create(['role' => 'user', 'content' => 'Bonjour']);
        $session->messages()->create(['role' => 'assistant', 'content' => 'Bonjour, comment aider ?']);

        $this->actingAs($owner)->get("/admin/ai/conversations/{$session->id}")
            ->assertOk()
            ->assertSee('Bonjour');
    }
}
```

- [ ] **Step 2: Run tests to verify failure**

Run: `php artisan test --filter=AdminAiChatSettingsTest`

Expected: FAIL because settings/conversation routes do not exist.

- [ ] **Step 3: Add settings request and controllers**

Create `app/Http/Requests/Admin/AiChatSettingsRequest.php`:

```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AiChatSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'enabled' => ['nullable', 'boolean'],
            'model' => ['required', 'string', 'max:80'],
            'max_sources' => ['required', 'integer', 'min:1', 'max:10'],
            'max_message_chars' => ['required', 'integer', 'min:200', 'max:2000'],
            'fallback_contact_mode' => ['required', Rule::in(['contact_form', 'whatsapp'])],
            'greetings.fr' => ['required', 'string', 'max:240'],
            'greetings.en' => ['required', 'string', 'max:240'],
            'system_prompt' => ['required', 'string', 'max:4000'],
        ];
    }
}
```

Create controllers:

```php
// AiChatSettingsController methods
public function edit(): View
{
    return view('admin.ai.settings', ['settings' => AiChatSetting::current()]);
}

public function update(AiChatSettingsRequest $request): RedirectResponse
{
    $data = $request->validated();
    AiChatSetting::current()->update([
        'enabled' => $request->boolean('enabled'),
        'model' => $data['model'],
        'max_sources' => $data['max_sources'],
        'max_message_chars' => $data['max_message_chars'],
        'fallback_contact_mode' => $data['fallback_contact_mode'],
        'greetings' => $data['greetings'],
        'system_prompt' => $data['system_prompt'],
    ]);

    return redirect()->route('admin.ai.settings.edit')->with('status', 'Parametres Assistant IA mis a jour.');
}
```

```php
// AiConversationsController methods
public function index(): View
{
    return view('admin.ai.conversations-index', [
        'sessions' => AiChatSession::withCount('messages')->latest()->paginate(25),
    ]);
}

public function show(AiChatSession $session): View
{
    return view('admin.ai.conversation-show', [
        'session' => $session->load(['messages', 'lead']),
    ]);
}
```

- [ ] **Step 4: Add admin routes**

Inside the existing `admin/ai` group:

```php
Route::get('/conversations', [AdminAiConversationsController::class, 'index'])
    ->middleware('admin.permission:ai_chat.view')
    ->name('conversations.index');
Route::get('/conversations/{session}', [AdminAiConversationsController::class, 'show'])
    ->middleware('admin.permission:ai_chat.view')
    ->name('conversations.show');
Route::get('/settings', [AdminAiChatSettingsController::class, 'edit'])
    ->middleware('admin.permission:ai_chat.manage')
    ->name('settings.edit');
Route::put('/settings', [AdminAiChatSettingsController::class, 'update'])
    ->middleware('admin.permission:ai_chat.manage')
    ->name('settings.update');
```

Add matching `use` statements for `AdminAiConversationsController` and `AdminAiChatSettingsController`.

- [ ] **Step 5: Add admin views**

Create:

- `conversations-index.blade.php`: table with `public_id`, locale, country, messages count, lead status, updated date, show link.
- `conversation-show.blade.php`: transcript grouped by role and lead details if present.
- `settings.blade.php`: form for enabled, model, max sources, max message chars, fallback mode, FR/EN greetings, system prompt.

Use the existing admin layout, `card`, `table`, `btn`, `form-control`, and flash status patterns from `resources/views/admin/role-profiles/edit.blade.php`.

- [ ] **Step 6: Run settings tests**

Run: `php artisan test --filter=AdminAiChatSettingsTest`

Expected: PASS.

- [ ] **Step 7: Commit**

Run:

```bash
git add app/Http/Requests/Admin/AiChatSettingsRequest.php app/Http/Controllers/Admin/AiConversationsController.php app/Http/Controllers/Admin/AiChatSettingsController.php resources/views/admin/ai/conversations-index.blade.php resources/views/admin/ai/conversation-show.blade.php resources/views/admin/ai/settings.blade.php routes/web.php tests/Feature/AdminAiChatSettingsTest.php
git commit -m "feat: add ai chat admin settings"
```

---

### Task 7: End-To-End Verification And Launch Prep

**Files:**
- Modify: `docs/LAUNCH_READINESS_2026-05-13.md` if AI chat launch notes should be documented.
- Test: relevant feature suites and production build.

- [ ] **Step 1: Run AI feature tests**

Run:

```bash
php artisan test --filter=AiChatSchemaTest
php artisan test --filter=AiKnowledgeImporterTest
php artisan test --filter=AdminAiKnowledgeTest
php artisan test --filter=AiChatResponderTest
php artisan test --filter=AiChatWidgetTest
php artisan test --filter=AdminAiChatSettingsTest
```

Expected: all PASS.

- [ ] **Step 2: Run broader regression tests**

Run:

```bash
php artisan test --filter=LaunchReadinessCommandTest
php artisan test --filter=SeoIndexableTest
php artisan test --filter=AdminRoleProfilesTest
```

Expected: all PASS. If `AdminRoleProfilesTest` does not exist, run `php artisan test --filter=RoleProfiles`.

- [ ] **Step 3: Run build**

Run: `npm run build`

Expected: Vite build completes without errors and emits the chat widget assets.

- [ ] **Step 4: Manual local smoke test**

Run: `php artisan serve --host=127.0.0.1 --port=8000`

Check:

- `/admin/ai/settings` is protected by login and owner permission.
- `/admin/ai/import` accepts a Markdown file and creates draft chunks.
- Publishing a chunk makes it available to the responder.
- `/fr/contact` shows the widget only after settings enable it.
- Asking about content not present in chunks returns the fallback.

- [ ] **Step 5: Deploy to VPS**

Run:

```bash
git push origin master
ssh dreamdigital "set -e; cd /var/www/dream-digital; git pull --ff-only origin master; DD_DEPLOY_MODE=testing APP_DIR=/var/www/dream-digital BRANCH=master bash scripts/deploy-production.sh"
```

Expected: deploy completes, migrations run, launch check remains OK in testing mode.

- [ ] **Step 6: Production smoke test**

Run:

```bash
curl -4 -s -o /dev/null -D - https://dream-digital.info/fr/contact
curl -4 -s -o /dev/null -D - https://dream-digital.info/admin/ai/settings
ssh dreamdigital "cd /var/www/dream-digital && php artisan route:list --path=admin/ai && php artisan route:list --path=ai-chat"
```

Expected:

- Public contact page returns `200`.
- Admin settings redirects to login when unauthenticated.
- AI admin and public chat routes are registered.

- [ ] **Step 7: Commit any launch notes**

If docs changed:

```bash
git add docs/LAUNCH_READINESS_2026-05-13.md
git commit -m "docs: add ai chat launch notes"
```

---

## Self-Review

- Spec coverage: local-only knowledge, Markdown/CSV/PDF import, admin review/publish workflow, chat widget, conversations, settings, fallback behavior, permissions, and tests are covered.
- Scope control: voice, crawling, automatic publication, and vector search are not part of the first implementation.
- Type consistency: models use `Ai*` prefixes, routes use `admin.ai.*` and `front.ai-chat.message`, and permissions match `RoleProfile` constants.
- Deployment coverage: migrations, Vite build, route checks, and VPS deployment are included.
