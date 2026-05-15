<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropUnique('company_profiles_locale_unique');
            $table->string('country_code', 2)->default('cd')->after('id');
            $table->string('latitude', 32)->nullable()->after('support_hours');
            $table->string('longitude', 32)->nullable()->after('latitude');
            $table->unique(['country_code', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropUnique(['country_code', 'locale']);
            $table->dropColumn(['country_code', 'latitude', 'longitude']);
            $table->unique('locale');
        });
    }
};
