@extends('layouts/layoutMaster')

@section('title', 'Pages -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Pages</h1>
            <p class="mb-0 text-muted">CMS Eloquent : pages publiques editables (legales, marketing, etc.). Le frontend lit la DB en priorite, fallback config si vide.</p>
          </div>
          @if(auth()->user()?->canManageContent())
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary align-self-start">
              <i class="bx bx-plus me-1"></i> Nouvelle page
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
        <form method="GET" action="{{ route('admin.pages.index') }}" class="card-body row g-3">
          <div class="col-md-4">
            <label class="form-label" for="section">Section</label>
            <select class="form-select" id="section" name="section">
              <option value="">Toutes</option>
              @foreach($sections as $sec)
                <option value="{{ $sec }}" @selected($filters['section'] === $sec)>{{ $sectionLabels[$sec] ?? ucfirst($sec) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="locale">Locale</label>
            <select class="form-select" id="locale" name="locale">
              <option value="">Toutes</option>
              <option value="fr" @selected($filters['locale'] === 'fr')>FR</option>
              <option value="en" @selected($filters['locale'] === 'en')>EN</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="published">Publication</label>
            <select class="form-select" id="published" name="published">
              <option value="" @selected($filters['published'] === '')>Toutes</option>
              <option value="1" @selected($filters['published'] === '1')>Publiees</option>
              <option value="0" @selected($filters['published'] === '0')>Brouillons</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="editorial_status">Workflow</label>
            <select class="form-select" id="editorial_status" name="editorial_status">
              <option value="">Tous</option>
              @foreach($editorialStatuses as $status => $label)
                <option value="{{ $status }}" @selected($filters['editorial_status'] === $status)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
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
                <th>Section</th>
                <th>Slug</th>
                <th>Locale</th>
                <th>Pays</th>
                <th>Titre</th>
                <th>Statut</th>
                <th>Revisions</th>
                <th>Mis a jour</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pages as $page)
                <tr>
                  <td><span class="badge bg-label-info">{{ $sectionLabels[$page->section] ?? $page->section }}</span></td>
                  <td><code>{{ $page->slug }}</code></td>
                  <td><span class="badge bg-label-secondary">{{ strtoupper($page->locale) }}</span></td>
                  <td>
                    @if($page->country_id)
                      {{ $page->country->flag_emoji ?? '' }} {{ $page->country->code ?? '--' }}
                    @else
                      <span class="text-muted">global</span>
                    @endif
                  </td>
                  <td>{{ \Illuminate\Support\Str::limit($page->title, 60) }}</td>
                  <td>
                    @if($page->is_published)
                      <span class="badge bg-label-success">Publie</span>
                    @else
                      <span class="badge bg-label-warning">{{ $page->editorialStatusLabel() }}</span>
                    @endif
                    @if($page->updatedBy)
                      <span class="d-block small text-muted mt-1">{{ $page->updatedBy->name }}</span>
                    @endif
                  </td>
                  <td><span class="badge bg-label-secondary">{{ $page->revisions_count }}</span></td>
                  <td class="small text-muted">{{ $page->updated_at?->format('Y-m-d H:i') }}</td>
                  <td class="text-end">
                    @if(auth()->user()?->canManageContent())
                      <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" title="Editer">
                        <i class="bx bx-pencil"></i>
                      </a>
                      <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="d-inline" onsubmit="return confirm('Supprimer cette page ?');">
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
                  <td colspan="9" class="text-center text-muted py-4">
                    Aucune page.
                    @if(auth()->user()?->canManageContent())
                      <a href="{{ route('admin.pages.create') }}">Ajouter la premiere</a>.
                    @endif
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($pages->hasPages())
          <div class="card-footer">{{ $pages->links() }}</div>
        @endif
      </div>
    </div>
  </div>
@endsection
