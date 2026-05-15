@extends('layouts/layoutMaster')

@section('title', 'Conversation IA -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Conversation IA</h1>
            <p class="mb-0 text-muted">{{ $session->public_id }}</p>
          </div>
          <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-secondary align-self-start">
            <i class="bx bx-arrow-back me-1"></i> Retour
          </a>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h2 class="h5 mb-0">Transcription</h2>
        </div>
        <div class="card-body">
          @forelse($session->messages as $message)
            <div class="mb-4">
              <div class="d-flex justify-content-between gap-3 mb-1">
                <span class="badge bg-label-{{ $message->role === 'assistant' ? 'info' : 'secondary' }}">{{ $message->role }}</span>
                <span class="small text-muted">{{ $message->created_at?->format('Y-m-d H:i') }}</span>
              </div>
              <div class="border rounded p-3">{!! nl2br(e($message->content)) !!}</div>
            </div>
          @empty
            <p class="text-muted mb-0">Aucun message.</p>
          @endforelse
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h2 class="h5 mb-0">Details</h2>
        </div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-5">Locale</dt>
            <dd class="col-7">{{ strtoupper($session->locale) }}</dd>
            <dt class="col-5">Pays</dt>
            <dd class="col-7">{{ $session->country_code }}</dd>
            <dt class="col-5">Lead</dt>
            <dd class="col-7">{{ $session->lead_status }}</dd>
            <dt class="col-5">Page</dt>
            <dd class="col-7">{{ $session->page_url ?: '-' }}</dd>
          </dl>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">
          <h2 class="h5 mb-0">Lead capture</h2>
        </div>
        <div class="card-body">
          @if($session->lead)
            <dl class="row mb-0">
              <dt class="col-5">Nom</dt>
              <dd class="col-7">{{ $session->lead->name ?: '-' }}</dd>
              <dt class="col-5">Email</dt>
              <dd class="col-7">{{ $session->lead->email ?: '-' }}</dd>
              <dt class="col-5">Telephone</dt>
              <dd class="col-7">{{ $session->lead->phone ?: '-' }}</dd>
              <dt class="col-5">WhatsApp</dt>
              <dd class="col-7">{{ $session->lead->whatsapp ?: '-' }}</dd>
              <dt class="col-5">Societe</dt>
              <dd class="col-7">{{ $session->lead->company ?: '-' }}</dd>
              <dt class="col-5">Besoin</dt>
              <dd class="col-7">{{ $session->lead->need ?: '-' }}</dd>
            </dl>
          @else
            <p class="text-muted mb-0">Aucun lead capture.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
