<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('countries')) {
            return;
        }

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('name_fr');
            $table->string('name_en');
            $table->string('default_currency_code', 3);
            $table->string('secondary_currency_code', 3)->nullable();
            $table->boolean('show_dual_currency')->default(false);
            $table->string('default_locale', 2);
            $table->json('available_locales');
            $table->string('phone_prefix', 8);
            $table->string('sales_email');
            $table->string('sales_phone')->nullable();
            $table->text('office_address')->nullable();
            $table->string('flag_emoji', 16);
            $table->boolean('is_global')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
