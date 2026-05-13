@extends('layouts/layoutMaster')

@section('title', 'Dream Digital Admin')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Dream Digital Admin</h1>
            <p class="mb-0 text-muted">Backoffice V0 pour aligner le projet avec le cahier des charges: pricing multi-pays, RBAC, utilisateurs et publication de contenu.</p>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-success">Internal</span>
            <span class="badge bg-label-primary">Sprint 1 ready</span>
          </div>
        </div>
      </div>
    </div>

    @foreach ($stats as $stat)
      <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
          <div class="card-body">
            <span class="text-muted small">{{ $stat['label']['fr'] ?? $stat['id'] }}</span>
            <h2 class="mb-1">{{ $stat['prefix'] ?? '' }}{{ $stat['value'] ?? '' }}{{ $stat['suffix'] ?? '' }}</h2>
            <p class="mb-0 text-muted">{{ $stat['caption']['fr'] ?? '' }}</p>
          </div>
        </div>
      </div>
    @endforeach

    <div class="col-xl-7">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="h5 mb-0">Modules metier prioritaires</h2>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            @foreach ($features as $feature)
              <div class="list-group-item px-0 d-flex gap-3">
                <i class="icon-base bx {{ $feature['icon'] ?? 'bx-check-shield' }} icon-md text-primary"></i>
                <div>
                  <h3 class="h6 mb-1">{{ $feature['title']['fr'] ?? '' }}</h3>
                  <p class="mb-0 text-muted">{{ $feature['body']['fr'] ?? '' }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-5">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="h5 mb-0">Raccourcis</h2>
        </div>
        <div class="card-body d-grid gap-3">
          <a class="btn btn-primary" href="{{ url('/admin/pricing') }}">Pricing</a>
          <a class="btn btn-primary" href="{{ url('/admin/pages') }}">Pages (CMS)</a>
          <a class="btn btn-outline-primary" href="{{ url('/fr') }}" target="_blank" rel="noopener">Voir le site public</a>
        </div>
      </div>
    </div>
  </div>
@endsection
