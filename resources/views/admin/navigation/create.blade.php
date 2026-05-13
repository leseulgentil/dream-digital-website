@extends('layouts/layoutMaster')

@section('title', 'Nouveau lien -- Navigation')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <nav class="mb-2 small" aria-label="breadcrumb">
            <a href="{{ route('admin.navigation.index') }}">Navigation</a> / <span class="text-muted">Nouveau</span>
          </nav>
          <h1 class="h3 mb-0">Nouveau lien de menu</h1>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        @include('admin.navigation._form')
      </div>
    </div>
  </div>
@endsection
