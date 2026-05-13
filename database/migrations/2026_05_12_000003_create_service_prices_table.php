<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('service_prices')) {
            return;
        }

        Schema::create('service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('destination_country', 3)->nullable();
            $table->string('label_fr');
            $table->string('label_en');
            $table->decimal('price_usd', 12, 6);
            $table->decimal('price_local', 12, 6)->nullable();
            $table->string('local_currency', 3)->nullable();
            $table->string('unit', 20);
            $table->boolean('use_manual_local')->default(false);
            $table->boolean('is_published')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['service_id', 'country_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
