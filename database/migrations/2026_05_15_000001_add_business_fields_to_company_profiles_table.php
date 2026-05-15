<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->string('whatsapp_number', 60)->nullable()->after('public_phone');
            $table->string('address_line', 255)->nullable()->after('whatsapp_number');
            $table->string('city', 120)->nullable()->after('address_line');
            $table->string('country_label', 120)->nullable()->after('city');
            $table->string('registration_number', 160)->nullable()->after('country_label');
            $table->string('tax_id', 160)->nullable()->after('registration_number');
            $table->string('support_hours', 160)->nullable()->after('tax_id');
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_number',
                'address_line',
                'city',
                'country_label',
                'registration_number',
                'tax_id',
                'support_hours',
            ]);
        });
    }
};
