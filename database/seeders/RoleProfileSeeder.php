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
            $profile = RoleProfile::query()->firstOrNew(['role' => $role]);
            $profile->label = $label;

            if (! $profile->exists) {
                $profile->permissions = RoleProfile::defaultPermissionsFor($role);
                $profile->save();

                continue;
            }

            $profile->save();
        }
    }
}
