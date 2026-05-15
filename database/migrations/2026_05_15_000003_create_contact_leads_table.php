<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_leads', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 2)->default('fr');
            $table->string('country_code', 2)->nullable()->index();
            $table->string('status', 40)->default('new')->index();
            $table->string('full_name', 160);
            $table->string('company_name', 190)->nullable();
            $table->string('email', 190);
            $table->string('phone', 80)->nullable();
            $table->string('service_interest', 80)->nullable()->index();
            $table->string('monthly_volume', 80)->nullable();
            $table->text('message');
            $table->string('source_page', 255)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_leads');
    }
};
