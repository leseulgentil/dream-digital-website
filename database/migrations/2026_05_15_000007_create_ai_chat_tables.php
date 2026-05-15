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
            $table->unsignedInteger('max_sources')->default(5);
            $table->unsignedInteger('max_message_chars')->default(1200);
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
            $table->string('country_code')->default('global');
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
            $table->string('country_code')->default('global');
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
            $table->string('country_code')->default('global');
            $table->string('page_url')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent_hash', 64)->nullable();
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
            $table->string('country_code')->nullable();
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
