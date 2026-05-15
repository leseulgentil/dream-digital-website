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

            $profile->permissions = array_values(array_unique([
                ...($profile->permissions ?? []),
                ...$this->aiPermissionsFor($role),
            ]));
            $profile->save();
        }
    }

    /**
     * @return list<string>
     */
    private function aiPermissionsFor(string $role): array
    {
        $managePermissions = [
            RoleProfile::PERMISSION_AI_CHAT_VIEW,
            RoleProfile::PERMISSION_AI_CHAT_MANAGE,
            RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW,
            RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE,
        ];

        return match ($role) {
            User::ROLE_OWNER, User::ROLE_ADMIN, User::ROLE_EDITOR => $managePermissions,
            User::ROLE_VIEWER => [
                RoleProfile::PERMISSION_AI_CHAT_VIEW,
                RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW,
            ],
            default => [],
        };
    }
}
