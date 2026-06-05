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
          <div class="col-md-2">
            <label class="form-label" for="locale">Locale</label>
            <select class="form-select" id="locale" name="locale">
              <option value="">Toutes</option>
              <option value="fr" @selected($filters['locale'] === 'fr')>FR</option>
              <option value="en" @selected($filters['locale'] === 'en')>EN</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label" for="country_code">Audience</label>
            <select class="form-select" id="country_code" name="country_code">
              <option value="">Tous</option>
              @foreach(['global' => 'Global', 'cd' => 'RDC', 'ci' => 'Cote d Ivoire', 'cg' => 'Congo'] as $code => $label)
                <option value="{{ $code }}" @selected($filters['country_code'] === $code)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label" for="destination_country">Destination ISO3</label>
            <input
              class="form-control text-uppercase"
              id="destination_country"
              name="destination_country"
              value="{{ $filters['destination_country'] }}"
              maxlength="3"
              placeholder="COD"
            >
          </div>
          <div class="col-md-2">
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
        <div class="card">
          <div class="card-header d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
              <h2 class="h5 mb-1">Sources web</h2>
              <p class="mb-0 text-muted">URL, sitemap ou futur endpoint JSON synchronises dans la base IA.</p>
            </div>
            <span class="badge bg-label-info align-self-start">Import en brouillon conseille</span>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('admin.ai.web-sources.store') }}" class="row g-3">
              @csrf
              <div class="col-md-3">
                <label class="form-label" for="web_title">Nom de la source</label>
                <input class="form-control @error('title') is-invalid @enderror" id="web_title" name="title" value="{{ old('title') }}" placeholder="eSIM Zone">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-2">
                <label class="form-label" for="web_type">Type</label>
                <select class="form-select @error('type') is-invalid @enderror" id="web_type" name="type">
                  <option value="url" @selected(old('type') === 'url')>URL simple</option>
                  <option value="sitemap" @selected(old('type', 'sitemap') === 'sitemap')>Sitemap</option>
                  <option value="endpoint_json" @selected(old('type') === 'endpoint_json')>Endpoint JSON</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-7">
                <label class="form-label" for="web_url">URL</label>
                <input type="url" class="form-control @error('url') is-invalid @enderror" id="web_url" name="url" value="{{ old('url') }}" placeholder="https://esimzone.fr/sitemap.xml">
                @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-2">
                <label class="form-label" for="web_locale">Locale</label>
                <select class="form-select" id="web_locale" name="locale">
                  <option value="fr" @selected(old('locale', 'fr') === 'fr')>FR</option>
                  <option value="en" @selected(old('locale') === 'en')>EN</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label" for="web_country_code">Pays</label>
                <select class="form-select" id="web_country_code" name="country_code">
                  @foreach(['global' => 'Global', 'cd' => 'RDC', 'ci' => 'CI', 'cg' => 'CG'] as $code => $label)
                    <option value="{{ $code }}" @selected(old('country_code', 'global') === $code)>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label" for="web_category">Categorie</label>
                <input class="form-control" id="web_category" name="category" value="{{ old('category') }}" placeholder="esim">
              </div>
              <div class="col-md-3">
                <label class="form-label" for="web_auth_token">Token Bearer optionnel</label>
                <input type="password" class="form-control @error('auth_token') is-invalid @enderror" id="web_auth_token" name="auth_token" autocomplete="new-password" placeholder="Endpoint protege">
                @error('auth_token')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-2">
                <label class="form-label" for="web_frequency">Frequence</label>
                <select class="form-select" id="web_frequency" name="frequency">
                  <option value="manual" @selected(old('frequency') === 'manual')>Manuel</option>
                  <option value="daily" @selected(old('frequency') === 'daily')>Quotidien</option>
                  <option value="weekly" @selected(old('frequency', 'weekly') === 'weekly')>Hebdomadaire</option>
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label" for="web_import_status">Statut import</label>
                <select class="form-select" id="web_import_status" name="import_status">
                  <option value="draft" @selected(old('import_status', 'draft') === 'draft')>Brouillon</option>
                  <option value="published" @selected(old('import_status') === 'published')>Publie</option>
                </select>
              </div>
              <div class="col-md-1 d-flex align-items-end">
                <div class="form-check form-switch mb-2">
                  <input type="hidden" name="sync_now" value="0">
                  <input class="form-check-input" type="checkbox" id="sync_now" name="sync_now" value="1" @checked(old('sync_now', '1') === '1')>
                  <label class="form-check-label" for="sync_now">Importer maintenant</label>
                </div>
              </div>
              <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bx bx-cloud-download me-1"></i> Enregistrer la source
                </button>
              </div>
            </form>
          </div>

          <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Source</th>
                  <th>Type</th>
                  <th>Frequence</th>
                  <th>Dernier import</th>
                  <th>Prochain import</th>
                  <th>Pages</th>
                  <th>Statut</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($webSources as $webSource)
                  <tr>
                    <td>
                      <div class="fw-medium">{{ $webSource->title }}</div>
                      <div class="small text-muted text-truncate" style="max-width: 360px;">{{ $webSource->url }}</div>
                      @if($webSource->last_error)
                        <div class="small text-danger text-truncate" style="max-width: 360px;">{{ $webSource->last_error }}</div>
                      @endif
                    </td>
                    <td><span class="badge bg-label-info">{{ $webSource->type }}</span></td>
                    <td>{{ $webSource->frequency }}</td>
                    <td class="small text-muted">{{ $webSource->last_synced_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td class="small text-muted">{{ $webSource->next_sync_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $webSource->sources_count }}</td>
                    <td><span class="badge bg-label-{{ $webSource->status === 'active' ? 'success' : 'secondary' }}">{{ $webSource->status }}</span></td>
                    <td class="text-end">
                      <form method="POST" action="{{ route('admin.ai.web-sources.sync', $webSource) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-primary" title="Synchroniser">
                          <i class="bx bx-refresh"></i>
                        </button>
                      </form>
                      <form method="POST" action="{{ route('admin.ai.web-sources.destroy', $webSource) }}" class="d-inline" onsubmit="return confirm('Supprimer cette source web et ses segments ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer">
                          <i class="bx bx-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">Aucune source web.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

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
                <th>Audience</th>
                <th>Destination</th>
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
                @php
                  $sourceMetadata = $chunk->source?->metadata ?? [];
                  $audienceCountry = data_get($sourceMetadata, 'audience_country') ?: $chunk->country_code;
                  $destinationCountry = data_get($sourceMetadata, 'destination_country') ?: '-';
                @endphp
                <tr>
                  <td>{{ \Illuminate\Support\Str::limit($chunk->title, 60) }}</td>
                  <td><span class="badge bg-label-secondary">{{ strtoupper($chunk->locale) }}</span></td>
                  <td><span class="text-muted">{{ $audienceCountry }}</span></td>
                  <td><span class="badge bg-label-primary">{{ $destinationCountry }}</span></td>
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
                  <td colspan="{{ $canManageAiKnowledge ? 10 : 9 }}" class="text-center text-muted py-4">Aucun segment.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($chunks->hasPages())
          <div class="card-footer d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div class="small text-muted">
              Affichage {{ $chunks->firstItem() }}-{{ $chunks->lastItem() }} sur {{ $chunks->total() }} segments.
            </div>
            {{ $chunks->onEachSide(1)->links('vendor.pagination.admin-bootstrap') }}
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
