<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30)->unique();
            $table->string('label', 80);
            $table->text('description')->nullable();
            $table->json('permissions');
            $table->timestamps();
        });

        $now = now();
        foreach (User::ROLES as $role => $label) {
            DB::table('role_profiles')->insert([
                'role' => $role,
                'label' => $label,
                'description' => null,
                'permissions' => json_encode(\App\Models\RoleProfile::defaultPermissionsFor($role)),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_profiles');
    }
};
