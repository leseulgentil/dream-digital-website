<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pages')) {
            return;
        }

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('section');
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->string('locale', 2);
            $table->string('title');
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_image_path')->nullable();
            $table->json('content_blocks')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'section', 'country_id', 'locale'], 'pages_uniqueness');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
