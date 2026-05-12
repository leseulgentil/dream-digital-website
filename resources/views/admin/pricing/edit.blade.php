@extends('layouts/layoutMaster')

@section('title', 'Editer tarif -- Admin Pricing')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
          <div>
            <nav class="mb-2 small" aria-label="breadcrumb">
              <a href="{{ route('admin.pricing.index') }}">Pricing</a> / <span class="text-muted">Editer #{{ $price->id }}</span>
            </nav>
            <h1 class="h3 mb-0">{{ $price->label_fr }}</h1>
            <p class="text-muted mb-0">
              Service : <strong>{{ $price->service->name_fr }}</strong> ·
              Pays : <strong>{{ $price->country->flag_emoji }} {{ $price->country->name_fr }}</strong>
              @if($price->destination_country) · Destination : <code>{{ $price->destination_country }}</code>@endif
            </p>
            @if($price->updatedBy)
              <p class="text-muted small mb-0 mt-2">Dernier changement par <strong>{{ $price->updatedBy->name }}</strong> le {{ $price->updated_at?->format('Y-m-d H:i') }}.</p>
            @endif
          </div>
          <form method="POST" action="{{ route('admin.pricing.destroy', $price) }}" class="align-self-start" onsubmit="return confirm('Supprimer ce tarif ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
              <i class="bx bx-trash me-1"></i> Supprimer
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        @include('admin.pricing._form')
      </div>
    </div>
  </div>
@endsection
