@extends('layouts/layoutMaster')

@section('title', 'Editer page -- Admin Pages')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-3">
          <div>
            <nav class="mb-2 small" aria-label="breadcrumb">
              <a href="{{ route('admin.pages.index') }}">Pages</a> / <span class="text-muted">Editer #{{ $page->id }}</span>
            </nav>
            <h1 class="h3 mb-0">{{ $page->title }}</h1>
            <p class="text-muted mb-0">
              <code>{{ $page->section }}</code> / <code>{{ $page->slug }}</code> /
              <code>{{ strtoupper($page->locale) }}</code>
              @if($page->country_id) / <code>{{ $page->country->code ?? '?' }}</code> @else / global @endif
            </p>
            <p class="text-muted small mb-0 mt-2">
              Maj le {{ $page->updated_at?->format('Y-m-d H:i') }}.
              @if($page->is_published && $page->section === 'legal')
                <a href="{{ url("/{$page->locale}/legal/{$page->slug}") }}" target="_blank" rel="noopener">
                  <i class="bx bx-link-external"></i> Voir page publique
                </a>
              @endif
            </p>
          </div>
          <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="align-self-start" onsubmit="return confirm('Supprimer cette page ?');">
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
        @include('admin.pages._form')
      </div>
    </div>
  </div>
@endsection
