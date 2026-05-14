<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 2)->unique();
            $table->string('company_name', 120)->default('Dream Digital');
            $table->string('legal_name', 160)->nullable();
            $table->string('public_phone', 60)->nullable();
            $table->string('email_sales', 160)->nullable();
            $table->string('email_support', 160)->nullable();
            $table->string('email_security', 160)->nullable();
            $table->string('email_privacy', 160)->nullable();
            $table->string('social_linkedin', 500)->nullable();
            $table->string('social_twitter', 500)->nullable();
            $table->string('social_github', 500)->nullable();
            $table->string('og_image_path', 500)->nullable();
            $table->boolean('legal_validated')->default(false);
            $table->boolean('admin_password_rotated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
