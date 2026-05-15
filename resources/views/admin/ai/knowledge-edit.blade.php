@extends('layouts/layoutMaster')

@section('title', 'Editer un segment IA -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Editer un segment IA</h1>
            <p class="mb-0 text-muted">{{ $chunk->source?->title ?? 'Source manuelle' }}</p>
          </div>
          <a href="{{ route('admin.ai.knowledge.index') }}" class="btn btn-outline-secondary align-self-start">
            <i class="bx bx-arrow-back me-1"></i> Retour
          </a>
        </div>
      </div>
    </div>

    <div class="col-12">
      <form method="POST" action="{{ route('admin.ai.knowledge.update', $chunk) }}" class="card">
        @csrf
        @method('PUT')
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label" for="title">Titre</label>
            <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $chunk->title) }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-2">
            <label class="form-label" for="locale">Locale</label>
            <select class="form-select" id="locale" name="locale" required>
              <option value="fr" @selected(old('locale', $chunk->locale) === 'fr')>FR</option>
              <option value="en" @selected(old('locale', $chunk->locale) === 'en')>EN</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="country_code">Pays</label>
            <select class="form-select" id="country_code" name="country_code" required>
              @foreach(['global' => 'Global', 'cd' => 'RDC', 'ci' => 'CI', 'cg' => 'CG'] as $code => $label)
                <option value="{{ $code }}" @selected(old('country_code', $chunk->country_code) === $code)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="priority">Priorite</label>
            <input type="number" min="0" max="100" class="form-control" id="priority" name="priority" value="{{ old('priority', $chunk->priority) }}">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="category">Categorie</label>
            <input class="form-control" id="category" name="category" value="{{ old('category', $chunk->category) }}">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="status">Statut</label>
            <select class="form-select" id="status" name="status" required>
              @foreach(['draft' => 'Brouillon', 'published' => 'Publie', 'archived' => 'Archive'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $chunk->status) === $value)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label" for="expires_at">Expiration</label>
            <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at', $chunk->expires_at?->format('Y-m-d\TH:i')) }}">
          </div>
          <div class="col-12">
            <label class="form-label" for="content">Contenu</label>
            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="8" required>{{ old('content', $chunk->content) }}</textarea>
            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
          <a href="{{ route('admin.ai.knowledge.index') }}" class="btn btn-outline-secondary">Annuler</a>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
