@extends('layouts/layoutMaster')

@section('title', 'Nouveau tarif -- Admin Pricing')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <nav class="mb-2 small" aria-label="breadcrumb">
            <a href="{{ route('admin.pricing.index') }}">Pricing</a> / <span class="text-muted">Nouveau</span>
          </nav>
          <h1 class="h3 mb-0">Nouveau tarif</h1>
          <p class="text-muted mb-0">Cree une entree dans <code>service_prices</code> pour un corridor service x pays.</p>
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
