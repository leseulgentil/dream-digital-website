@extends('layouts/layoutMaster')

@section('title', 'Utilisateurs -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Utilisateurs</h1>
            <p class="mb-0 text-muted">Gestion des comptes admin Dream Digital : roles, statut actif et acces au back-office.</p>
          </div>
          <a href="{{ route('admin.users.create') }}" class="btn btn-primary align-self-start">
            <i class="bx bx-plus me-1"></i> Nouvel utilisateur
          </a>
        </div>
      </div>
    </div>

    @if(session('status'))
      <div class="col-12">
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
      </div>
    @endif

    <div class="col-12">
      <div class="card">
        <form method="GET" action="{{ route('admin.users.index') }}" class="card-body row g-3">
          <div class="col-md-5">
            <label class="form-label" for="role">Role</label>
            <select class="form-select" id="role" name="role">
              <option value="">Tous</option>
              @foreach($roles as $value => $label)
                <option value="{{ $value }}" @selected($filters['role'] === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-5">
            <label class="form-label" for="active">Statut</label>
            <select class="form-select" id="active" name="active">
              <option value="" @selected($filters['active'] === '')>Tous</option>
              <option value="1" @selected($filters['active'] === '1')>Actifs</option>
              <option value="0" @selected($filters['active'] === '0')>Inactifs</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-secondary w-100" type="submit">Filtrer</button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="table-responsive text-nowrap">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Role</th>
                <th>Statut</th>
                <th>Derniere connexion</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $adminUser)
                <tr>
                  <td><strong>{{ $adminUser->name }}</strong></td>
                  <td>{{ $adminUser->email }}</td>
                  <td><span class="badge bg-label-primary">{{ $adminUser->roleLabel() }}</span></td>
                  <td>
                    @if($adminUser->is_active)
                      <span class="badge bg-label-success">Actif</span>
                    @else
                      <span class="badge bg-label-secondary">Inactif</span>
                    @endif
                  </td>
                  <td class="small text-muted">{{ $adminUser->last_login_at?->format('Y-m-d H:i') ?? '--' }}</td>
                  <td class="text-end">
                    <a href="{{ route('admin.users.edit', $adminUser) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" title="Editer">
                      <i class="bx bx-pencil"></i>
                    </a>
                    @if(!auth()->user()->is($adminUser) && $adminUser->is_active)
                      <form method="POST" action="{{ route('admin.users.destroy', $adminUser) }}" class="d-inline" onsubmit="return confirm('Desactiver cet utilisateur ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Desactiver">
                          <i class="bx bx-user-x"></i>
                        </button>
                      </form>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">Aucun utilisateur.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($users->hasPages())
          <div class="card-footer">{{ $users->links() }}</div>
        @endif
      </div>
    </div>
  </div>
@endsection
