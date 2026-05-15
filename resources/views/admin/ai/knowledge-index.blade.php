@extends('layouts/layoutMaster')

@section('title', 'Base IA -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Base IA</h1>
            <p class="mb-0 text-muted">Segments utilises par l'assistant Dream Digital.</p>
          </div>
          @if($canManageAiKnowledge)
            <a href="{{ route('admin.ai.import.create') }}" class="btn btn-primary align-self-start">
              <i class="bx bx-upload me-1"></i> Importer
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
        <form method="GET" action="{{ route('admin.ai.knowledge.index') }}" class="card-body row g-3">
          <div class="col-md-3">
            <label class="form-label" for="locale">Locale</label>
            <select class="form-select" id="locale" name="locale">
              <option value="">Toutes</option>
              <option value="fr" @selected($filters['locale'] === 'fr')>FR</option>
              <option value="en" @selected($filters['locale'] === 'en')>EN</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="country_code">Pays</label>
            <select class="form-select" id="country_code" name="country_code">
              <option value="">Tous</option>
              @foreach(['global' => 'Global', 'cd' => 'RDC', 'ci' => 'Cote d Ivoire', 'cg' => 'Congo'] as $code => $label)
                <option value="{{ $code }}" @selected($filters['country_code'] === $code)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="status">Statut</label>
            <select class="form-select" id="status" name="status">
              <option value="">Tous</option>
              @foreach(['draft' => 'Brouillon', 'published' => 'Publie', 'archived' => 'Archive'] as $value => $label)
                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-secondary w-100" type="submit">Filtrer</button>
          </div>
        </form>
      </div>
    </div>

    @if($canManageAiKnowledge)
      <div class="col-12">
        <form method="POST" action="{{ route('admin.ai.knowledge.store') }}" class="card">
          @csrf
          <div class="card-header">
            <h2 class="h5 mb-0">Nouvelle entree</h2>
          </div>
          <div class="card-body row g-3">
            <div class="col-md-6">
              <label class="form-label" for="title">Titre</label>
              <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
              @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
              <label class="form-label" for="manual_locale">Locale</label>
              <select class="form-select" id="manual_locale" name="locale" required>
                <option value="fr" @selected(old('locale', 'fr') === 'fr')>FR</option>
                <option value="en" @selected(old('locale') === 'en')>EN</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label" for="manual_country_code">Pays</label>
              <select class="form-select" id="manual_country_code" name="country_code" required>
                @foreach(['global' => 'Global', 'cd' => 'RDC', 'ci' => 'CI', 'cg' => 'CG'] as $code => $label)
                  <option value="{{ $code }}" @selected(old('country_code', 'global') === $code)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label" for="priority">Priorite</label>
              <input type="number" min="0" max="100" class="form-control" id="priority" name="priority" value="{{ old('priority', 0) }}">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="category">Categorie</label>
              <input class="form-control" id="category" name="category" value="{{ old('category') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="manual_status">Statut</label>
              <select class="form-select" id="manual_status" name="status" required>
                @foreach(['draft' => 'Brouillon', 'published' => 'Publie', 'archived' => 'Archive'] as $value => $label)
                  <option value="{{ $value }}" @selected(old('status', 'draft') === $value)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="expires_at">Expiration</label>
              <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
            </div>
            <div class="col-12">
              <label class="form-label" for="content">Contenu</label>
              <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
              @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-plus me-1"></i> Ajouter
            </button>
          </div>
        </form>
      </div>
    @endif

    <div class="col-12">
      <div class="card">
        <div class="table-responsive text-nowrap">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Locale</th>
                <th>Pays</th>
                <th>Categorie</th>
                <th>Statut</th>
                <th>Priorite</th>
                <th>Source</th>
                <th>Mis a jour</th>
                @if($canManageAiKnowledge)
                  <th class="text-end">Actions</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @forelse($chunks as $chunk)
                <tr>
                  <td>{{ \Illuminate\Support\Str::limit($chunk->title, 60) }}</td>
                  <td><span class="badge bg-label-secondary">{{ strtoupper($chunk->locale) }}</span></td>
                  <td><span class="text-muted">{{ $chunk->country_code }}</span></td>
                  <td>{{ $chunk->category ?: '-' }}</td>
                  <td><span class="badge bg-label-{{ $chunk->status === 'published' ? 'success' : ($chunk->status === 'archived' ? 'secondary' : 'warning') }}">{{ $chunk->status }}</span></td>
                  <td>{{ $chunk->priority }}</td>
                  <td><span class="badge bg-label-info">{{ $chunk->source?->type ?? '-' }}</span></td>
                  <td class="small text-muted">{{ $chunk->updated_at?->format('Y-m-d H:i') }}</td>
                  @if($canManageAiKnowledge)
                    <td class="text-end">
                      <a href="{{ route('admin.ai.knowledge.edit', $chunk) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" title="Editer">
                        <i class="bx bx-pencil"></i>
                      </a>
                      <form method="POST" action="{{ route('admin.ai.knowledge.destroy', $chunk) }}" class="d-inline" onsubmit="return confirm('Supprimer ce segment ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer">
                          <i class="bx bx-trash"></i>
                        </button>
                      </form>
                    </td>
                  @endif
                </tr>
              @empty
                <tr>
                  <td colspan="{{ $canManageAiKnowledge ? 9 : 8 }}" class="text-center text-muted py-4">Aucun segment.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($chunks->hasPages())
          <div class="card-footer">{{ $chunks->links() }}</div>
        @endif
      </div>
    </div>
  </div>
@endsection
