<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_knowledge_chunks', function (Blueprint $table): void {
            if (! Schema::hasColumn('ai_knowledge_chunks', 'embedding')) {
                $table->json('embedding')->nullable()->after('priority');
            }

            if (! Schema::hasColumn('ai_knowledge_chunks', 'embedding_model')) {
                $table->string('embedding_model', 120)->nullable()->after('embedding');
            }

            if (! Schema::hasColumn('ai_knowledge_chunks', 'embedding_hash')) {
                $table->string('embedding_hash', 64)->nullable()->after('embedding_model');
            }

            if (! Schema::hasColumn('ai_knowledge_chunks', 'embedded_at')) {
                $table->timestamp('embedded_at')->nullable()->after('embedding_hash');
            }

            $table->index(['embedding_model', 'embedding_hash'], 'ai_knowledge_chunks_embedding_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::table('ai_knowledge_chunks', function (Blueprint $table): void {
            $table->dropIndex('ai_knowledge_chunks_embedding_lookup_index');
            $table->dropColumn(['embedding', 'embedding_model', 'embedding_hash', 'embedded_at']);
        });
    }
};
