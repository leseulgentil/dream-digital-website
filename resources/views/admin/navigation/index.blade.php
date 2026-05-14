@extends('layouts/layoutMaster')

@section('title', 'Navigation -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Navigation principale</h1>
            <p class="mb-0 text-muted">Menu public editable : liens simples, sous-menus et mega menus Dream Digital.</p>
          </div>
          <a href="{{ route('admin.navigation.create') }}" class="btn btn-primary align-self-start">
            <i class="bx bx-plus me-1"></i> Nouveau lien
          </a>
        </div>
      </div>
    </div>

    @if(session('status'))
      <div class="col-12">
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
      </div>
    @endif

    <div class="col-xl-8">
      <div class="card">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Ordre</th>
                <th>Libelle</th>
                <th>Type</th>
                <th>URL</th>
                <th>Statut</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items as $item)
                <tr>
                  <td>{{ $item->sort_order }}</td>
                  <td>
                    <strong>{{ $item->label_fr }}</strong>
                    @if($item->label_en)
                      <span class="text-muted small d-block">{{ $item->label_en }}</span>
                    @endif
                  </td>
                  <td><span class="badge bg-label-info">{{ \App\Models\NavigationItem::TYPES[$item->type] ?? $item->type }}</span></td>
                  <td class="small"><code>{{ $item->url ?: '#' }}</code></td>
                  <td>
                    @if($item->is_active)
                      <span class="badge bg-label-success">Visible</span>
                    @else
                      <span class="badge bg-label-secondary">Masque</span>
                    @endif
                    @if($item->children->isNotEmpty())
                      <span class="badge bg-label-primary">{{ $item->children->count() }} sous-liens</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <a href="{{ route('admin.navigation.edit', $item) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" title="Editer">
                      <i class="bx bx-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.navigation.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Supprimer ce lien de menu ?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer">
                        <i class="bx bx-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                @foreach($item->children as $child)
                  <tr>
                    <td class="text-muted ps-5">{{ $child->sort_order }}</td>
                    <td class="ps-5"><span class="text-muted">└</span> {{ $child->label_fr }}</td>
                    <td><span class="badge bg-label-secondary">{{ \App\Models\NavigationItem::TYPES[$child->type] ?? $child->type }}</span></td>
                    <td class="small"><code>{{ $child->url ?: '#' }}</code></td>
                    <td>
                      @if($child->is_active)
                        <span class="badge bg-label-success">Visible</span>
                      @else
                        <span class="badge bg-label-secondary">Masque</span>
                      @endif
                    </td>
                    <td class="text-end">
                      <a href="{{ route('admin.navigation.edit', $child) }}" class="btn btn-sm btn-icon btn-outline-primary me-1" title="Editer">
                        <i class="bx bx-pencil"></i>
                      </a>
                      <form method="POST" action="{{ route('admin.navigation.destroy', $child) }}" class="d-inline" onsubmit="return confirm('Supprimer ce sous-lien ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer">
                          <i class="bx bx-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">Aucun lien configure.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-4">
      <div class="card">
        <div class="card-header">
          <h2 class="h5 mb-0">Adresses disponibles</h2>
        </div>
        <div class="card-body">
          <div class="list-group list-group-flush">
            @foreach($suggestions as $suggestion)
              <div class="list-group-item px-0">
                <strong class="d-block">{{ $suggestion['label'] }}</strong>
                <code class="small">{{ $suggestion['url'] }}</code>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
