<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500)->unique();
            $table->string('filename', 255);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text', 220)->nullable();
            $table->string('credit', 220)->nullable();
            $table->string('source_url', 500)->nullable();
            $table->foreignId('uploaded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('editorial_status', 40)->default('draft')->after('is_published');
            $table->text('review_notes')->nullable()->after('editorial_status');
            $table->foreignId('updated_by_id')->nullable()->after('review_notes')->constrained('users')->nullOnDelete();
        });

        DB::table('pages')
            ->where('is_published', true)
            ->update(['editorial_status' => 'published']);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('updated_by_id');
            $table->dropColumn(['editorial_status', 'review_notes']);
        });

        Schema::dropIfExists('media_assets');
    }
};
