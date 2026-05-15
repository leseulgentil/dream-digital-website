<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleProfilesController extends Controller
{
    public function edit(): View
    {
        $profiles = $this->profiles();

        return view('admin.role-profiles.edit', [
            'profiles' => $profiles,
            'roles' => User::ROLES,
            'permissions' => RoleProfile::availablePermissions(),
            'lockedOwnerPermissions' => RoleProfile::defaultPermissionsFor(User::ROLE_OWNER),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $availablePermissions = array_keys(RoleProfile::availablePermissions());
        $rules = ['profiles' => ['required', 'array']];

        foreach (array_keys(User::ROLES) as $role) {
            $rules["profiles.{$role}.permissions"] = ['nullable', 'array'];
            $rules["profiles.{$role}.permissions.*"] = ['string', Rule::in($availablePermissions)];
        }

        $validated = $request->validate($rules);

        foreach (User::ROLES as $role => $label) {
            $permissions = (array) data_get($validated, "profiles.{$role}.permissions", []);
            if ($role === User::ROLE_OWNER) {
                $permissions = $availablePermissions;
            }

            RoleProfile::query()->updateOrCreate(
                ['role' => $role],
                [
                    'label' => $label,
                    'permissions' => array_values(array_unique($permissions)),
                ],
            );
        }

        return redirect()
            ->route('admin.role-profiles.edit')
            ->with('status', 'Profils et acces mis a jour.');
    }

    private function profiles()
    {
        return collect(User::ROLES)
            ->mapWithKeys(function (string $label, string $role): array {
                $profile = RoleProfile::query()->firstOrCreate(
                    ['role' => $role],
                    [
                        'label' => $label,
                        'permissions' => RoleProfile::defaultPermissionsFor($role),
                    ],
                );

                return [$role => $profile];
            });
    }
}
