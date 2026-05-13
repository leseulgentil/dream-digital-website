@php
  $isEdit = $adminUser->exists;
  $formAction = $isEdit ? route('admin.users.update', $adminUser) : route('admin.users.store');
  $isSelf = $isEdit && auth()->user()?->is($adminUser);
@endphp

<form method="POST" action="{{ $formAction }}" class="card-body row g-4" novalidate>
  @csrf
  @if($isEdit)
    @method('PUT')
  @endif

  <div class="col-md-6">
    <label class="form-label" for="name">Nom <span class="text-danger">*</span></label>
    <input type="text" id="name" name="name" maxlength="150" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $adminUser->name) }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6">
    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
    <input type="email" id="email" name="email" maxlength="190" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $adminUser->email) }}" required>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6">
    <label class="form-label" for="role">Role <span class="text-danger">*</span></label>
    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" @disabled($isSelf) required>
      @foreach($roles as $value => $label)
        <option value="{{ $value }}" @selected(old('role', $adminUser->role) === $value)>{{ $label }}</option>
      @endforeach
    </select>
    @if($isSelf)
      <input type="hidden" name="role" value="{{ $adminUser->role }}">
      <small class="text-muted">Votre propre role ne peut pas etre modifie ici.</small>
    @endif
    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6 d-flex align-items-end">
    <div class="form-check form-switch w-100">
      <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $adminUser->is_active ?? true)) @disabled($isSelf)>
      <label class="form-check-label" for="is_active">Compte actif</label>
      @if($isSelf)
        <input type="hidden" name="is_active" value="1">
      @endif
    </div>
  </div>

  <div class="col-12"><hr class="m-0"></div>

  <div class="col-md-6">
    <label class="form-label" for="password">Mot de passe {{ $isEdit ? '' : '*' }}</label>
    <input type="password" id="password" name="password" minlength="12" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" @required(!$isEdit)>
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">{{ $isEdit ? 'Laisser vide pour conserver le mot de passe actuel.' : 'Minimum 12 caracteres.' }}</small>
  </div>

  <div class="col-md-6">
    <label class="form-label" for="password_confirmation">Confirmation</label>
    <input type="password" id="password_confirmation" name="password_confirmation" minlength="12" class="form-control" autocomplete="new-password" @required(!$isEdit)>
  </div>

  <div class="col-12 d-flex justify-content-end gap-2 pt-2">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Annuler</a>
    <button type="submit" class="btn btn-primary">
      <i class="bx {{ $isEdit ? 'bx-save' : 'bx-plus' }} me-1"></i>
      {{ $isEdit ? 'Enregistrer' : 'Creer l utilisateur' }}
    </button>
  </div>
</form>
