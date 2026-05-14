@php
  $isEdit = $price->exists;
  $formAction = $isEdit ? route('admin.pricing.update', $price) : route('admin.pricing.store');
  $localCurrencyHint = $countries->pluck('default_currency_code', 'id')->filter()->toJson();
@endphp

<form method="POST" action="{{ $formAction }}" class="card-body row g-4" novalidate>
  @csrf
  @if($isEdit)
    @method('PUT')
  @endif

  <div class="col-md-6">
    <label class="form-label" for="service_id">Service <span class="text-danger">*</span></label>
    <select id="service_id" name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
      <option value="">--</option>
      @foreach($services as $service)
        <option value="{{ $service->id }}" @selected(old('service_id', $price->service_id) == $service->id)>{{ $service->name_fr }} ({{ $service->slug }})</option>
      @endforeach
    </select>
    @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6">
    <label class="form-label" for="country_id">Pays origine <span class="text-danger">*</span></label>
    <select id="country_id" name="country_id" class="form-select @error('country_id') is-invalid @enderror" required data-currency-map='{{ $localCurrencyHint }}'>
      <option value="">--</option>
      @foreach($countries as $country)
        <option value="{{ $country->id }}" @selected(old('country_id', $price->country_id) == $country->id)>{{ $country->flag_emoji }} {{ $country->name_fr }} ({{ $country->code }})</option>
      @endforeach
    </select>
    @error('country_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="destination_country">Code pays destination <small class="text-muted">(ISO 2)</small></label>
    <input type="text" id="destination_country" name="destination_country" maxlength="2" class="form-control text-uppercase @error('destination_country') is-invalid @enderror" value="{{ old('destination_country', $price->destination_country) }}" placeholder="ex. CD">
    @error('destination_country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">Vide = corridor depuis le pays origine.</small>
  </div>

  <div class="col-md-4">
    <label class="form-label" for="unit">Unite <span class="text-danger">*</span></label>
    <input type="text" id="unit" name="unit" maxlength="20" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $price->unit) }}" placeholder="SMS / minute / GB / DID" required>
    @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4 d-flex align-items-end">
    <div class="form-check form-switch w-100">
      <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $price->is_published))>
      <label class="form-check-label" for="is_published">Publie (visible sur le site public)</label>
    </div>
  </div>

  <div class="col-md-6">
    <label class="form-label" for="label_fr">Libelle FR <span class="text-danger">*</span></label>
    <input type="text" id="label_fr" name="label_fr" maxlength="200" class="form-control @error('label_fr') is-invalid @enderror" value="{{ old('label_fr', $price->label_fr) }}" required>
    @error('label_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-6">
    <label class="form-label" for="label_en">Libelle EN <span class="text-danger">*</span></label>
    <input type="text" id="label_en" name="label_en" maxlength="200" class="form-control @error('label_en') is-invalid @enderror" value="{{ old('label_en', $price->label_en) }}" required>
    @error('label_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12"><hr class="m-0"></div>

  <div class="col-md-4">
    <label class="form-label" for="price_usd">Prix USD <span class="text-danger">*</span></label>
    <div class="input-group">
      <span class="input-group-text">$</span>
      <input type="number" step="0.000001" min="0" id="price_usd" name="price_usd" class="form-control @error('price_usd') is-invalid @enderror" value="{{ old('price_usd', $price->price_usd) }}" required>
      @error('price_usd')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <small class="text-muted">Source de verite (devise pivot).</small>
  </div>

  <div class="col-md-4">
    <label class="form-label" for="price_local">Prix local <small class="text-muted">(optionnel)</small></label>
    <input type="number" step="0.000001" min="0" id="price_local" name="price_local" class="form-control @error('price_local') is-invalid @enderror" value="{{ old('price_local', $price->price_local) }}">
    @error('price_local')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <small class="text-muted">Si vide ou si <code>use_manual_local=off</code>, calcule automatiquement par CurrencyConverter.</small>
  </div>

  <div class="col-md-4">
    <label class="form-label" for="local_currency">Devise locale <small class="text-muted">(ISO 3)</small></label>
    <input type="text" id="local_currency" name="local_currency" maxlength="3" class="form-control text-uppercase @error('local_currency') is-invalid @enderror" value="{{ old('local_currency', $price->local_currency) }}" placeholder="USD / EUR / XAF...">
    @error('local_currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="use_manual_local" name="use_manual_local" value="1" @checked(old('use_manual_local', $price->use_manual_local))>
      <label class="form-check-label" for="use_manual_local">
        Verrouiller le prix local <small class="text-muted">(ignorer la conversion auto, utiliser la valeur saisie ci-dessus)</small>
      </label>
    </div>
  </div>

  <div class="col-12"><hr class="m-0"><h5 class="mt-3 mb-0">Affichage corridor (page publique)</h5><p class="text-muted mb-0">Champs utilises par /fr/pricing et /fr/coverage pour la card corridor.</p></div>

  <div class="col-md-4">
    <label class="form-label" for="quality">Qualite de route <small class="text-muted">(1-5 etoiles)</small></label>
    <select id="quality" name="quality" class="form-select @error('quality') is-invalid @enderror">
      @for($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}" @selected(old('quality', $price->quality ?? 3) == $i)>{{ str_repeat('*', $i) }} ({{ $i }})</option>
      @endfor
    </select>
    @error('quality')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="status_fr">Status FR <small class="text-muted">(ex: "Route prioritaire")</small></label>
    <input type="text" id="status_fr" name="status_fr" maxlength="100" class="form-control @error('status_fr') is-invalid @enderror" value="{{ old('status_fr', $price->status_fr) }}">
    @error('status_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-md-4">
    <label class="form-label" for="status_en">Status EN <small class="text-muted">(ex: "Priority route")</small></label>
    <input type="text" id="status_en" name="status_en" maxlength="100" class="form-control @error('status_en') is-invalid @enderror" value="{{ old('status_en', $price->status_en) }}">
    @error('status_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>

  <div class="col-12 d-flex justify-content-end gap-2 pt-2">
    <a href="{{ route('admin.pricing.index') }}" class="btn btn-outline-secondary">Annuler</a>
    <button type="submit" class="btn btn-primary">
      <i class="bx {{ $isEdit ? 'bx-save' : 'bx-plus' }} me-1"></i>
      {{ $isEdit ? 'Enregistrer' : 'Creer le tarif' }}
    </button>
  </div>
</form>

<script>
(function () {
  const countrySelect = document.getElementById('country_id');
  const localCurrencyInput = document.getElementById('local_currency');
  if (!countrySelect || !localCurrencyInput) return;
  const map = JSON.parse(countrySelect.dataset.currencyMap || '{}');
  countrySelect.addEventListener('change', function () {
    if (!localCurrencyInput.value && map[this.value]) {
      localCurrencyInput.value = map[this.value];
    }
  });
})();
</script>
