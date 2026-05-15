@extends('layouts/layoutMaster')

@section('title', 'Importer base IA -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Importer dans la base IA</h1>
            <p class="mb-0 text-muted">Fichiers Markdown, CSV ou PDF jusqu'a 10 Mo.</p>
          </div>
          <a href="{{ route('admin.ai.knowledge.index') }}" class="btn btn-outline-secondary align-self-start">
            <i class="bx bx-arrow-back me-1"></i> Retour
          </a>
        </div>
      </div>
    </div>

    <div class="col-12">
      <form method="POST" action="{{ route('admin.ai.import.store') }}" enctype="multipart/form-data" class="card">
        @csrf
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label" for="title">Titre</label>
            <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-2">
            <label class="form-label" for="locale">Locale</label>
            <select class="form-select" id="locale" name="locale" required>
              <option value="fr" @selected(old('locale', 'fr') === 'fr')>FR</option>
              <option value="en" @selected(old('locale') === 'en')>EN</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="country_code">Pays</label>
            <select class="form-select" id="country_code" name="country_code" required>
              @foreach(['global' => 'Global', 'cd' => 'RDC', 'ci' => 'CI', 'cg' => 'CG'] as $code => $label)
                <option value="{{ $code }}" @selected(old('country_code', 'global') === $code)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="category">Categorie</label>
            <input class="form-control" id="category" name="category" value="{{ old('category') }}">
          </div>
          <div class="col-12">
            <label class="form-label" for="file">Fichier</label>
            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".md,.markdown,.csv,.pdf" required>
            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-upload me-1"></i> Importer
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
