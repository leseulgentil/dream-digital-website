<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_OWNER = 'owner';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_VIEWER = 'viewer';

    public const ROLES = [
        self::ROLE_OWNER => 'Owner',
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_EDITOR => 'Editor',
        self::ROLE_VIEWER => 'Viewer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canViewAdmin(): bool
    {
        return $this->hasPermission(RoleProfile::PERMISSION_ADMIN_ACCESS);
    }

    public function canManageContent(): bool
    {
        return $this->hasAnyPermission([
            RoleProfile::PERMISSION_PAGES_MANAGE,
            RoleProfile::PERMISSION_NAVIGATION_MANAGE,
            RoleProfile::PERMISSION_MEDIA_MANAGE,
            RoleProfile::PERMISSION_PRICING_MANAGE,
        ]);
    }

    public function canManageUsers(): bool
    {
        return $this->hasPermission(RoleProfile::PERMISSION_USERS_MANAGE);
    }

    public function roleProfile(): HasOne
    {
        return $this->hasOne(RoleProfile::class, 'role', 'role');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->is_active && RoleProfile::roleHasPermission($this->role, $permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst((string) $this->role);
    }
}
