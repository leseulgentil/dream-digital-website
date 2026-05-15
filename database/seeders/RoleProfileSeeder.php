<?php

namespace Database\Seeders;

use App\Models\RoleProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleProfileSeeder extends Seeder
{
    public function run(): void
    {
        foreach (User::ROLES as $role => $label) {
            RoleProfile::query()->updateOrCreate(
                ['role' => $role],
                [
                    'label' => $label,
                    'permissions' => RoleProfile::defaultPermissionsFor($role),
                ],
            );
        }
    }
}
