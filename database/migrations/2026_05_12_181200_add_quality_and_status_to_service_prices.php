<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les metadonnees corridor (quality 1-5, status bilingue)
     * au modele ServicePrice afin que la page publique /fr/pricing et
     * /fr/coverage puissent afficher les corridor-cards depuis la DB
     * au lieu du config dream-digital.pages.corridors.
     */
    public function up(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->unsignedTinyInteger('quality')->default(3)->after('use_manual_local');
            $table->string('status_fr', 100)->nullable()->after('quality');
            $table->string('status_en', 100)->nullable()->after('status_fr');
        });
    }

    public function down(): void
    {
        Schema::table('service_prices', function (Blueprint $table) {
            $table->dropColumn(['quality', 'status_fr', 'status_en']);
        });
    }
};
