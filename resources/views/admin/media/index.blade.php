@extends('layouts/layoutMaster')

@section('title', 'Media -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Media CMS</h1>
            <p class="mb-0 text-muted">Images locales uploadees depuis l'admin Pages. Utilisez le chemin public dans le champ Image OG / Blog.</p>
          </div>
          <a href="{{ route('admin.pages.create') }}" class="btn btn-primary align-self-start">
            <i class="bx bx-upload me-1"></i> Uploader via une page
          </a>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-body">
          @if($media->isEmpty())
            <p class="text-muted mb-0">Aucun media local pour le moment.</p>
          @else
            <div class="row g-4">
              @foreach($media as $item)
                <div class="col-md-6 col-xl-4">
                  <article class="border rounded p-3 h-100">
                    <img src="{{ asset(ltrim($item['path'], '/')) }}" alt="" class="img-fluid rounded mb-3" loading="lazy">
                    <h2 class="h6 mb-1 text-truncate">{{ $item['name'] }}</h2>
                    <code class="small d-block text-break">{{ $item['path'] }}</code>
                    <p class="small text-muted mb-0 mt-2">{{ $item['size_kb'] }} KB - {{ $item['modified_at'] }}</p>
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
