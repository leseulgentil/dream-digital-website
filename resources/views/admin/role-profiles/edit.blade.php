@extends('layouts/layoutMaster')

@section('title', 'Profils & acces -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h1 class="h3 mb-2">Profils & acces</h1>
          <p class="mb-0 text-muted">Selectionnez les fonctionnalites disponibles pour chaque profil admin.</p>
        </div>
      </div>
    </div>

    @if(session('status'))
      <div class="col-12">
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
      </div>
    @endif

    <div class="col-12">
      <form method="POST" action="{{ route('admin.role-profiles.update') }}" class="card">
        @csrf
        @method('PUT')

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th style="min-width: 220px;">Fonctionnalite</th>
                @foreach($roles as $role => $label)
                  <th class="text-center">{{ $label }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($permissions as $permission => $label)
                <tr>
                  <td>
                    <strong>{{ $label }}</strong>
                    <div class="small text-muted">{{ $permission }}</div>
                  </td>
                  @foreach($roles as $role => $roleLabel)
                    @php
                      $isOwner = $role === \App\Models\User::ROLE_OWNER;
                      $isChecked = $isOwner
                        ? in_array($permission, $lockedOwnerPermissions, true)
                        : $profiles[$role]->hasPermission($permission);
                    @endphp
                    <td class="text-center">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        name="profiles[{{ $role }}][permissions][]"
                        value="{{ $permission }}"
                        @checked($isChecked)
                        @disabled($isOwner)
                        aria-label="{{ $label }} - {{ $roleLabel }}">
                      @if($isOwner)
                        <input type="hidden" name="profiles[{{ $role }}][permissions][]" value="{{ $permission }}">
                      @endif
                    </td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <div class="card-footer d-flex justify-content-end">
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Enregistrer les profils
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
