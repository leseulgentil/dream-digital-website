@extends('layouts/layoutMaster')

@section('title', 'Nouvelle page -- Admin Pages')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <nav class="mb-2 small" aria-label="breadcrumb">
            <a href="{{ route('admin.pages.index') }}">Pages</a> / <span class="text-muted">Nouvelle</span>
          </nav>
          <h1 class="h3 mb-0">Nouvelle page</h1>
          <p class="text-muted mb-0">Ajoute une entree dans la table <code>pages</code>. La cle d'unicite est (slug, section, country_id, locale).</p>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        @include('admin.pages._form')
      </div>
    </div>
  </div>
@endsection
