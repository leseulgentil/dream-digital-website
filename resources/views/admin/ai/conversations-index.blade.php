@extends('layouts/layoutMaster')

@section('title', 'Conversations IA -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Conversations IA</h1>
            <p class="mb-0 text-muted">Sessions recentes de l assistant Dream Digital.</p>
          </div>
          @if($canManageAiChat)
            <a href="{{ route('admin.ai.settings.edit') }}" class="btn btn-outline-primary align-self-start">
              <i class="bx bx-cog me-1"></i> Parametres
            </a>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="table-responsive text-nowrap">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Public ID</th>
                <th>Locale</th>
                <th>Pays</th>
                <th>Messages</th>
                <th>Lead</th>
                <th>Mis a jour</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($sessions as $session)
                <tr>
                  <td>{{ $session->public_id }}</td>
                  <td><span class="badge bg-label-secondary">{{ strtoupper($session->locale) }}</span></td>
                  <td><span class="text-muted">{{ $session->country_code }}</span></td>
                  <td>{{ $session->messages_count }}</td>
                  <td><span class="badge bg-label-primary">{{ $session->lead_status }}</span></td>
                  <td class="small text-muted">{{ $session->updated_at?->format('Y-m-d H:i') }}</td>
                  <td class="text-end">
                    <a href="{{ route('admin.ai.conversations.show', $session) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Voir">
                      <i class="bx bx-show"></i>
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">Aucune conversation.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($sessions->hasPages())
          <div class="card-footer">{{ $sessions->links() }}</div>
        @endif
      </div>
    </div>
  </div>
@endsection
