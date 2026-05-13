@extends('layouts/layoutMaster')

@section('title', 'Pricing -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Pricing</h1>
            <p class="mb-0 text-muted">CRUD service_prices : gerer les tarifs par service x pays (corridor). Bascule de publication, support double devise locale, audit `updated_by` automatique.</p>
          </div>
          @if(auth()->user()?->canManageContent())
            <a href="{{ route('admin.pricing.create') }}" class="btn btn-primary align-self-start">
              <i class="bx bx-plus me-1"></i> Nouveau tarif
            </a>
          @endif
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
        <form method="GET" action="{{ route('admin.pricing.index') }}" class="card-body row g-3">
          <div class="col-md-4">
            <label class="form-label" for="service_id">Service</label>
            <select class="form-select" id="service_id" name="service_id">
              <option value="">Tous</option>
              @foreach($services as $service)
                <option value="{{ $service->id }}" @selected($filters['service_id'] === $service->id)>{{ $service->name_fr }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label" for="country_id">Pays</label>
            <select class="form-select" id="country_id" name="country_id">
              <option value="">Tous</option>
              @foreach($countries as $country)
                <option value="{{ $country->id }}" @selected($filters['country_id'] === $country->id)>{{ $country->flag_emoji }} {{ $country->name_fr }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="published">Publication</label>
            <select class="form-select" id="published" name="published">
              <option value="" @selected($filters['published'] === '')>Tous</option>
              <option value="1" @selected($filters['published'] === '1')>Publies</option>
              <option value="0" @selected($filters['published'] === '0')>Brouillons</option>
            </select>
          </div>
          <div class="col-md-1 d-flex align-items-end">
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
                <th>Service</th>
                <th>Pays origine</th>
                <th>Destination</th>
                <th>Label</th>
                <th class="text-end">Prix USD</th>
                <th class="text-end">Prix local</th>
                <th>Unite</th>
                <th>Statut</th>
                <th>Modifie</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($prices as $price)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <i class="icon-base bx {{ $price->service->icon ?? 'bx-radio-circle' }} text-primary"></i>
                      <strong>{{ $price->service->name_fr }}</strong>
                    </div>
                  </td>
                  <td>{{ $price->country->flag_emoji }} {{ $price->country->code }}</td>
                  <td>
                    @if($price->destination_country)
                      <code>{{ $price->destination_country }}</code>
                    @else
                      <span class="text-muted">--</span>
                    @endif
                  </td>
                  <td>{{ $price->label_fr }}</td>
                  <td class="text-end"><code>${{ number_format((float) $price->price_usd, 4, '.', ' ') }}</code></td>
                  <td class="text-end">
                    @if($price->price_local && $price->local_currency)
                      <code>{{ number_format((float) $price->price_local, 2, '.', ' ') }} {{ $price->local_currency }}</code>
                      @if($price->use_manual_local)<i class="bx bx-lock-alt text-warning ms-1" title="Manuel"></i>@endif
                    @else
                      <span class="text-muted">--</span>
                    @endif
                  </td>
                  <td><code>{{ $price->unit }}</code></td>
                  <td>
                    @if($price->is_published)
                      <span class="badge bg-label-success">Publie</span>
                    @else
                      <span class="badge bg-label-secondary">Brouillon</span>
                    @endif
                  </td>
                  <td class="small text-muted">
                    {{ $price->updated_at?->format('Y-m-d H:i') }}
                    @if($price->updatedBy)
                      <br><span class="text-truncate">{{ $price->updatedBy->name }}</span>
                    @endif
                  </td>
                  <td class="text-end">
                    @if(auth()->user()?->canManageContent())
                      <a href="{{ route('admin.pricing.edit', $price) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" title="Editer">
                        <i class="bx bx-pencil"></i>
                      </a>
                      <form method="POST" action="{{ route('admin.pricing.destroy', $price) }}" class="d-inline" onsubmit="return confirm('Supprimer ce tarif ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer">
                          <i class="bx bx-trash"></i>
                        </button>
                      </form>
                    @else
                      <span class="text-muted">Lecture</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center text-muted py-4">
                    Aucun tarif.
                    @if(auth()->user()?->canManageContent())
                      <a href="{{ route('admin.pricing.create') }}">Ajouter le premier</a>.
                    @endif
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($prices->hasPages())
          <div class="card-footer">
            {{ $prices->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
