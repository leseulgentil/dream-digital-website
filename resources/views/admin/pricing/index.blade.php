@extends('layouts/layoutMaster')

@section('title', 'Pricing Admin V0')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Pricing Admin V0</h1>
            <p class="mb-0 text-muted">Surface de preparation du futur module ServicePrice: services, corridors, publication par pays et validation business avant exposition publique.</p>
          </div>
          <span class="badge bg-label-warning align-self-start">Config-driven</span>
        </div>
      </div>
    </div>

    <div class="col-xl-7">
      <div class="card">
        <div class="table-responsive text-nowrap">
          <table class="table">
            <thead>
              <tr>
                <th>Service</th>
                <th>Slug</th>
                <th>Statut</th>
                <th>Action publique</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($services as $service)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <i class="icon-base bx {{ $service['icon'] ?? 'bx-radio-circle' }} text-primary"></i>
                      <strong>{{ $service['name']['fr'] ?? $service['id'] }}</strong>
                    </div>
                  </td>
                  <td><code>{{ $service['slug'] ?? $service['id'] }}</code></td>
                  <td><span class="badge bg-label-success">Actif</span></td>
                  <td><a href="{{ url('/fr/products/' . ($service['slug'] ?? $service['id'])) }}">Voir page</a></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-5">
      <div class="card h-100">
        <div class="card-header">
          <h2 class="h5 mb-0">Corridors suivis</h2>
        </div>
        <div class="card-body">
          @foreach ($corridors as $corridor)
            <div class="border rounded p-3 mb-3">
              <div class="d-flex justify-content-between gap-3">
                <strong>{{ $corridor['title']['fr'] ?? '' }}</strong>
                <code>{{ $corridor['from'] ?? '' }} -> {{ $corridor['to'] ?? '' }}</code>
              </div>
              <p class="mb-0 text-muted">{{ $corridor['status']['fr'] ?? '' }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection
