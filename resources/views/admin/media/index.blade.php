@extends('layouts/layoutMaster')

@section('title', 'Media -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Media CMS</h1>
            <p class="mb-0 text-muted">Images locales, metadonnees SEO et pages qui les utilisent.</p>
          </div>
          <a href="{{ route('admin.pages.create') }}" class="btn btn-primary align-self-start">
            <i class="bx bx-upload me-1"></i> Uploader via une page
          </a>
        </div>
      </div>
    </div>

    @if(session('status'))
      <div class="col-12">
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
      </div>
    @endif
    @if(session('error'))
      <div class="col-12">
        <div class="alert alert-warning" role="alert">{{ session('error') }}</div>
      </div>
    @endif

    <div class="col-12">
      <div class="card">
        <div class="card-body">
          @if($media->isEmpty())
            <p class="text-muted mb-0">Aucun media local pour le moment.</p>
          @else
            <div class="row g-4">
              @foreach($media as $item)
                @php($asset = $item['asset'])
                @php($usage = $item['usage'])
                <div class="col-xl-6">
                  <article class="border rounded p-3 h-100">
                    <div class="row g-3">
                      <div class="col-md-5">
                        <img src="{{ asset(ltrim($asset->path, '/')) }}" alt="{{ $asset->alt_text ?? '' }}" class="img-fluid rounded mb-3" loading="lazy">
                        <h2 class="h6 mb-1 text-truncate">{{ $asset->filename }}</h2>
                        <code class="small d-block text-break">{{ $asset->path }}</code>
                        <p class="small text-muted mb-0 mt-2">
                          {{ $item['size_kb'] ? $item['size_kb'] . ' KB' : 'Taille inconnue' }}
                          @if($asset->width && $asset->height)
                            · {{ $asset->width }}×{{ $asset->height }}
                          @endif
                        </p>
                      </div>
                      <div class="col-md-7">
                        <form method="POST" action="{{ route('admin.media.update', $asset) }}" class="d-flex flex-column gap-3">
                          @csrf
                          @method('PUT')
                          <div>
                            <label class="form-label" for="alt_text_{{ $asset->id }}">Texte alternatif</label>
                            <input type="text" id="alt_text_{{ $asset->id }}" name="alt_text" maxlength="220" class="form-control" value="{{ old('alt_text', $asset->alt_text) }}">
                          </div>
                          <div>
                            <label class="form-label" for="credit_{{ $asset->id }}">Credit</label>
                            <input type="text" id="credit_{{ $asset->id }}" name="credit" maxlength="220" class="form-control" value="{{ old('credit', $asset->credit) }}">
                          </div>
                          <div>
                            <label class="form-label" for="source_url_{{ $asset->id }}">URL source</label>
                            <input type="url" id="source_url_{{ $asset->id }}" name="source_url" maxlength="500" class="form-control" value="{{ old('source_url', $asset->source_url) }}">
                          </div>
                          <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                              <i class="bx bx-save me-1"></i> Enregistrer
                            </button>
                          </div>
                        </form>

                        <div class="mt-3">
                          <h3 class="h6 mb-2">Utilisation</h3>
                          @if($usage->isEmpty())
                            <p class="small text-muted mb-2">Aucune page ne reference cette image.</p>
                            <form method="POST" action="{{ route('admin.media.destroy', $asset) }}" onsubmit="return confirm('Supprimer ce media du disque ?');">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bx bx-trash me-1"></i> Supprimer
                              </button>
                            </form>
                          @else
                            <ul class="small mb-0 ps-3">
                              @foreach($usage as $page)
                                <li>
                                  <a href="{{ route('admin.pages.edit', $page) }}">{{ $page->title }}</a>
                                  <span class="text-muted">({{ $page->section }}/{{ strtoupper($page->locale) }})</span>
                                </li>
                              @endforeach
                            </ul>
                          @endif
                        </div>
                      </div>
                    </div>
                  </article>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
