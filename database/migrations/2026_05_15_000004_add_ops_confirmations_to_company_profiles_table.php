<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->boolean('public_basic_auth_disabled')->default(false)->after('admin_password_rotated');
            $table->boolean('backups_configured')->default(false)->after('public_basic_auth_disabled');
            $table->boolean('env_backed_up')->default(false)->after('backups_configured');
            $table->boolean('deployment_runbook_reviewed')->default(false)->after('env_backed_up');
        });
    }

    public function down(): void
    {
        Schema::table('company_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'public_basic_auth_disabled',
                'backups_configured',
                'env_backed_up',
                'deployment_runbook_reviewed',
            ]);
        });
    }
};
