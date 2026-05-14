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
        if (! Schema::hasTable('service_prices')) {
            return;
        }

        Schema::table('service_prices', function (Blueprint $table) {
            if (! Schema::hasColumn('service_prices', 'quality')) {
                $table->unsignedTinyInteger('quality')->default(3)->after('use_manual_local');
            }
            if (! Schema::hasColumn('service_prices', 'status_fr')) {
                $table->string('status_fr', 100)->nullable()->after('quality');
            }
            if (! Schema::hasColumn('service_prices', 'status_en')) {
                $table->string('status_en', 100)->nullable()->after('status_fr');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('service_prices')) {
            return;
        }

        Schema::table('service_prices', function (Blueprint $table) {
            $columns = collect(['quality', 'status_fr', 'status_en'])
                ->filter(fn (string $column) => Schema::hasColumn('service_prices', $column))
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
