@extends('layouts/layoutMaster')

@section('title', 'Nouvel utilisateur -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h1 class="h3 mb-2">Nouvel utilisateur</h1>
          <p class="mb-0 text-muted">Créer un compte interne et lui affecter un role admin.</p>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        @include('admin.users._form')
      </div>
    </div>
  </div>
@endsection
