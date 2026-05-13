@extends('layouts/layoutMaster')

@section('title', 'Editer page -- Admin Pages')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/quill/typography.scss', 'resources/assets/vendor/libs/quill/editor.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/quill/quill.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/dd-admin-pages.js'])
@endsection

@section('content')
  @php
    $publicUrl = match ($page->section) {
      'blog' => url("/{$page->locale}/blog/{$page->slug}"),
      'legal' => url("/{$page->locale}/legal/{$page->slug}"),
      'marketing' => url("/{$page->locale}/{$page->slug}"),
      default => null,
    };
    $targetLocale = $page->locale === 'fr' ? 'en' : 'fr';
  @endphp

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
              @if($page->is_published && $publicUrl)
                <a href="{{ $publicUrl }}" target="_blank" rel="noopener">
                  <i class="bx bx-link-external"></i> Voir page publique
                </a>
              @endif
            </p>
          </div>
          <div class="d-flex flex-wrap gap-2 align-self-start">
            <a href="{{ route('admin.pages.preview', $page) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
              <i class="bx bx-show me-1"></i> Preview
            </a>
            <form method="POST" action="{{ route('admin.pages.duplicate-locale', $page) }}">
              @csrf
              <input type="hidden" name="target_locale" value="{{ $targetLocale }}">
              <button type="submit" class="btn btn-outline-secondary">
                <i class="bx bx-copy me-1"></i> Dupliquer {{ strtoupper($targetLocale) }}
              </button>
            </form>
            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Supprimer cette page ?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger">
                <i class="bx bx-trash me-1"></i> Supprimer
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        @include('admin.pages._form')
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h2 class="h5 mb-0">Revisions recentes</h2>
        </div>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Action</th>
                <th>Auteur</th>
                <th>Titre snapshot</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              @forelse($revisions as $revision)
                <tr>
                  <td class="small text-muted">{{ $revision->created_at?->format('Y-m-d H:i') }}</td>
                  <td><span class="badge bg-label-secondary">{{ $revision->action }}</span></td>
                  <td>{{ $revision->user?->name ?? 'Systeme' }}</td>
                  <td>{{ \Illuminate\Support\Str::limit($revision->title, 80) }}</td>
                  <td>
                    @if($revision->is_published)
                      <span class="badge bg-label-success">Publie</span>
                    @else
                      <span class="badge bg-label-warning">Brouillon</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-muted">Aucune revision enregistree pour le moment.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
