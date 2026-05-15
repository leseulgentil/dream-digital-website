<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->orderBy('name');

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($request->has('active') && $request->input('active') !== '') {
            $query->where('is_active', $request->boolean('active'));
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'roles' => User::ROLES,
            'filters' => [
                'role' => $role,
                'active' => $request->input('active', ''),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'adminUser' => new User(['role' => User::ROLE_EDITOR, 'is_active' => true]),
            'roles' => User::ROLES,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($this->payload($request));

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Utilisateur cree : {$user->email}");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'adminUser' => $user,
            'roles' => User::ROLES,
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $payload = $this->payload($request, $user);

        if ($request->user()?->is($user)) {
            $payload['role'] = $user->role;
            $payload['is_active'] = true;
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Utilisateur mis a jour : {$user->email}");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()?->is($user), 403);

        $user->forceFill(['is_active' => false])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Utilisateur desactive : {$user->email}");
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()?->canManageUsers(), 403);

        $temporaryPassword = Str::random(18) . '!';
        $user->forceFill(['password' => $temporaryPassword])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Mot de passe temporaire genere pour {$user->email}.")
            ->with('temporary_password', $temporaryPassword)
            ->with('temporary_password_email', $user->email);
    }

    private function payload(UserRequest $request, ?User $user = null): array
    {
        $validated = $request->validated();

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        return $payload;
    }
}
