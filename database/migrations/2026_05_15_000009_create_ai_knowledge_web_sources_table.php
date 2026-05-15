<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_knowledge_web_sources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 24);
            $table->string('url', 2048);
            $table->string('locale', 2)->default('fr');
            $table->string('country_code', 12)->default('global');
            $table->string('category')->nullable();
            $table->string('frequency', 24)->default('manual');
            $table->string('import_status', 24)->default('draft');
            $table->string('status', 24)->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('next_sync_at')->nullable();
            $table->text('last_error')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'frequency', 'next_sync_at']);
            $table->index(['type', 'locale', 'country_code']);
        });

        Schema::table('ai_knowledge_sources', function (Blueprint $table) {
            $table->unsignedBigInteger('ai_knowledge_web_source_id')
                ->nullable()
                ->after('id');
            $table->string('source_url', 2048)->nullable()->after('mime_type');
            $table->string('content_hash', 64)->nullable()->after('source_url');
            $table->timestamp('fetched_at')->nullable()->after('content_hash');

            $table->index('ai_knowledge_web_source_id');
            $table->index('content_hash');
        });

        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('ai_knowledge_sources', function (Blueprint $table) {
                $table->foreign('ai_knowledge_web_source_id')
                    ->references('id')
                    ->on('ai_knowledge_web_sources')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('ai_knowledge_sources', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['ai_knowledge_web_source_id']);
            }

            $table->dropIndex(['ai_knowledge_web_source_id']);
            $table->dropIndex(['content_hash']);
            $table->dropColumn(['ai_knowledge_web_source_id', 'source_url', 'content_hash', 'fetched_at']);
        });

        Schema::dropIfExists('ai_knowledge_web_sources');
    }
};
