<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class RoleProfile extends Model
{
    public const PERMISSION_ADMIN_ACCESS = 'admin.access';
    public const PERMISSION_DASHBOARD_VIEW = 'dashboard.view';
    public const PERMISSION_PAGES_VIEW = 'pages.view';
    public const PERMISSION_PAGES_MANAGE = 'pages.manage';
    public const PERMISSION_NAVIGATION_MANAGE = 'navigation.manage';
    public const PERMISSION_COMPANY_PROFILE_MANAGE = 'company_profile.manage';
    public const PERMISSION_MEDIA_MANAGE = 'media.manage';
    public const PERMISSION_PRICING_VIEW = 'pricing.view';
    public const PERMISSION_PRICING_MANAGE = 'pricing.manage';
    public const PERMISSION_CONTACT_LEADS_VIEW = 'contact_leads.view';
    public const PERMISSION_USERS_MANAGE = 'users.manage';
    public const PERMISSION_PROFILES_MANAGE = 'profiles.manage';

    protected $fillable = [
        'role',
        'label',
        'description',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public static function availablePermissions(): array
    {
        return [
            self::PERMISSION_ADMIN_ACCESS => 'Acces au back-office',
            self::PERMISSION_DASHBOARD_VIEW => 'Tableau de bord',
            self::PERMISSION_PAGES_VIEW => 'Pages CMS - lecture',
            self::PERMISSION_PAGES_MANAGE => 'Pages CMS - edition',
            self::PERMISSION_NAVIGATION_MANAGE => 'Navigation',
            self::PERMISSION_COMPANY_PROFILE_MANAGE => 'Company Profile',
            self::PERMISSION_MEDIA_MANAGE => 'Media CMS',
            self::PERMISSION_PRICING_VIEW => 'Tarification - lecture',
            self::PERMISSION_PRICING_MANAGE => 'Tarification - edition',
            self::PERMISSION_CONTACT_LEADS_VIEW => 'Leads',
            self::PERMISSION_USERS_MANAGE => 'Utilisateurs',
            self::PERMISSION_PROFILES_MANAGE => 'Profils & acces',
        ];
    }

    public static function defaultPermissionsFor(string $role): array
    {
        return match ($role) {
            User::ROLE_OWNER => array_keys(self::availablePermissions()),
            User::ROLE_ADMIN => [
                self::PERMISSION_ADMIN_ACCESS,
                self::PERMISSION_DASHBOARD_VIEW,
                self::PERMISSION_PAGES_VIEW,
                self::PERMISSION_PAGES_MANAGE,
                self::PERMISSION_NAVIGATION_MANAGE,
                self::PERMISSION_COMPANY_PROFILE_MANAGE,
                self::PERMISSION_MEDIA_MANAGE,
                self::PERMISSION_PRICING_VIEW,
                self::PERMISSION_PRICING_MANAGE,
                self::PERMISSION_CONTACT_LEADS_VIEW,
                self::PERMISSION_USERS_MANAGE,
            ],
            User::ROLE_EDITOR => [
                self::PERMISSION_ADMIN_ACCESS,
                self::PERMISSION_DASHBOARD_VIEW,
                self::PERMISSION_PAGES_VIEW,
                self::PERMISSION_PAGES_MANAGE,
                self::PERMISSION_NAVIGATION_MANAGE,
                self::PERMISSION_MEDIA_MANAGE,
                self::PERMISSION_PRICING_VIEW,
                self::PERMISSION_PRICING_MANAGE,
                self::PERMISSION_CONTACT_LEADS_VIEW,
            ],
            User::ROLE_VIEWER => [
                self::PERMISSION_ADMIN_ACCESS,
                self::PERMISSION_DASHBOARD_VIEW,
                self::PERMISSION_PAGES_VIEW,
                self::PERMISSION_PRICING_VIEW,
            ],
            default => [],
        };
    }

    public static function roleHasPermission(?string $role, string $permission): bool
    {
        if (! $role || ! array_key_exists($role, User::ROLES)) {
            return false;
        }

        if (! Schema::hasTable('role_profiles')) {
            return in_array($permission, self::defaultPermissionsFor($role), true);
        }

        $profile = self::query()->where('role', $role)->first();
        $permissions = $profile?->permissions ?: self::defaultPermissionsFor($role);

        return in_array($permission, $permissions, true);
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? [], true);
    }
}
