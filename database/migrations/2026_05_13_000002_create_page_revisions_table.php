<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('page_revisions')) {
            return;
        }

        Schema::create('page_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 40)->default('updated');
            $table->string('slug', 120);
            $table->string('section', 60);
            $table->string('locale', 2);
            $table->string('title', 200);
            $table->text('meta_description')->nullable();
            $table->string('meta_image_path', 500)->nullable();
            $table->json('content_blocks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['page_id', 'created_at']);
            $table->index(['section', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_revisions');
    }
};
