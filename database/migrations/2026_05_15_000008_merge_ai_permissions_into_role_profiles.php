<?php

use App\Models\RoleProfile;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $aiPermissions = [
        RoleProfile::PERMISSION_AI_CHAT_VIEW,
        RoleProfile::PERMISSION_AI_CHAT_MANAGE,
        RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW,
        RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE,
    ];

    public function up(): void
    {
        if (! Schema::hasTable('role_profiles')) {
            return;
        }

        foreach ($this->permissionsByRole() as $role => $permissionsToAdd) {
            $profile = DB::table('role_profiles')->where('role', $role)->first();

            if (! $profile) {
                continue;
            }

            $permissions = $this->decodePermissions($profile->permissions);
            $merged = array_values(array_unique([...$permissions, ...$permissionsToAdd]));

            if ($merged === $permissions) {
                continue;
            }

            DB::table('role_profiles')
                ->where('id', $profile->id)
                ->update([
                    'permissions' => json_encode($merged),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('role_profiles')) {
            return;
        }

        DB::table('role_profiles')
            ->orderBy('id')
            ->each(function (object $profile): void {
                $permissions = array_values(array_diff(
                    $this->decodePermissions($profile->permissions),
                    $this->aiPermissions,
                ));

                DB::table('role_profiles')
                    ->where('id', $profile->id)
                    ->update([
                        'permissions' => json_encode($permissions),
                        'updated_at' => now(),
                    ]);
            });
    }

    /**
     * @return array<string, list<string>>
     */
    private function permissionsByRole(): array
    {
        return [
            User::ROLE_OWNER => $this->aiPermissions,
            User::ROLE_ADMIN => $this->aiPermissions,
            User::ROLE_EDITOR => $this->aiPermissions,
            User::ROLE_VIEWER => [
                RoleProfile::PERMISSION_AI_KNOWLEDGE_VIEW,
            ],
        ];
    }

    /**
     * @return list<string>
     */
    private function decodePermissions(?string $permissions): array
    {
        $decoded = json_decode($permissions ?: '[]', true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, 'is_string'));
    }
};
