@php
  $isEdit = $item->exists;
  $formAction = $isEdit ? route('admin.navigation.update', $item) : route('admin.navigation.store');
  $settings = $item->settings ?? [];
@endphp

<form method="POST" action="{{ $formAction }}" class="card-body row g-4" novalidate>
  @csrf
  @if($isEdit)
    @method('PUT')
  @endif

  <input type="hidden" name="menu_area" value="main">

  <div class="col-md-4">
    <label class="form-label" for="label_fr">Libelle FR <span class="text-danger">*</span></label>
    <input type="text" id="label_fr" name="label_fr" maxlength="120" class="form-control @error('label_fr') is-invalid @enderror" value="{{ old('label_fr', $item->label_fr) }}" required>
    @error('label_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="label_en">Libelle EN</label>
    <input type="text" id="label_en" name="label_en" maxlength="120" class="form-control @error('label_en') is-invalid @enderror" value="{{ old('label_en', $item->label_en) }}">
    @error('label_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="type">Type</label>
    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror">
      @foreach($types as $value => $label)
        <option value="{{ $value }}" @selected(old('type', $item->type) === $value)>{{ $label }}</option>
      @endforeach
    </select>
    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-8">
    <label class="form-label" for="url">Adresse associee</label>
    <input type="text" id="url" name="url" list="navigation-url-suggestions" maxlength="500" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $item->url) }}" placeholder="/{locale}/blog">
    <datalist id="navigation-url-suggestions">
      @foreach($suggestions as $suggestion)
        <option value="{{ $suggestion['url'] }}">{{ $suggestion['label'] }}</option>
      @endforeach
    </datalist>
    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">Utilise <code>{locale}</code> pour generer automatiquement <code>/fr/...</code> et <code>/en/...</code>.</small>
  </div>

  <div class="col-md-4">
    <label class="form-label" for="parent_id">Parent</label>
    <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
      <option value="">Lien principal</option>
      @foreach($parents as $parent)
        <option value="{{ $parent->id }}" @selected(old('parent_id', $item->parent_id) == $parent->id)>{{ $parent->label_fr }}</option>
      @endforeach
    </select>
    @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3">
    <label class="form-label" for="sort_order">Ordre</label>
    <input type="number" min="0" max="9999" id="sort_order" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $item->sort_order ?? 0) }}">
    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3">
    <label class="form-label" for="settings_icon">Icone Boxicons</label>
    <input type="text" id="settings_icon" name="settings_icon" maxlength="80" class="form-control @error('settings_icon') is-invalid @enderror" value="{{ old('settings_icon', $settings['icon'] ?? '') }}" placeholder="bx-link-alt">
    @error('settings_icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-3 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))>
      <label class="form-check-label" for="is_active">Visible</label>
    </div>
  </div>

  <div class="col-md-3 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="opens_new_tab" name="opens_new_tab" value="1" @checked(old('opens_new_tab', $item->opens_new_tab))>
      <label class="form-check-label" for="opens_new_tab">Nouvel onglet</label>
    </div>
  </div>

  <div class="col-md-6">
    <label class="form-label" for="settings_description_fr">Description FR</label>
    <input type="text" id="settings_description_fr" name="settings_description_fr" maxlength="220" class="form-control @error('settings_description_fr') is-invalid @enderror" value="{{ old('settings_description_fr', $settings['description_fr'] ?? '') }}">
    @error('settings_description_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6">
    <label class="form-label" for="settings_description_en">Description EN</label>
    <input type="text" id="settings_description_en" name="settings_description_en" maxlength="220" class="form-control @error('settings_description_en') is-invalid @enderror" value="{{ old('settings_description_en', $settings['description_en'] ?? '') }}">
    @error('settings_description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12">
    <div class="alert alert-info mb-0">
      <strong>Adresses proposees</strong>
      <div class="row g-2 mt-2">
        @foreach($suggestions as $suggestion)
          <div class="col-md-4">
            <button type="button" class="btn btn-sm btn-outline-secondary w-100 text-start" data-dd-fill-url="{{ $suggestion['url'] }}" data-dd-fill-type="{{ $suggestion['type'] }}">
              <span class="d-block fw-medium">{{ $suggestion['label'] }}</span>
              <code class="small">{{ $suggestion['url'] }}</code>
            </button>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <div class="col-12 d-flex justify-content-end gap-2 pt-2">
    <a href="{{ route('admin.navigation.index') }}" class="btn btn-outline-secondary">Annuler</a>
    <button type="submit" class="btn btn-primary">
      <i class="bx {{ $isEdit ? 'bx-save' : 'bx-plus' }} me-1"></i>
      {{ $isEdit ? 'Enregistrer' : 'Creer le lien' }}
    </button>
  </div>
</form>

@section('page-script')
  <script>
    document.querySelectorAll('[data-dd-fill-url]').forEach(function (button) {
      button.addEventListener('click', function () {
        document.getElementById('url').value = button.dataset.ddFillUrl || '';
        document.getElementById('type').value = button.dataset.ddFillType || 'link';
      });
    });
  </script>
@endsection
