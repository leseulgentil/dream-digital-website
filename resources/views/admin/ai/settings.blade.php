@extends('layouts/layoutMaster')

@section('title', 'Parametres assistant IA -- Admin Dream Digital')

@section('content')
  @php
    $displayRulesJson = old(
        'display_rules_json',
        json_encode($settings->display_rules ?: ['pages' => ['*']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
  @endphp

  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Parametres assistant IA</h1>
            <p class="mb-0 text-muted">Configuration du widget et des reponses de l assistant.</p>
          </div>
          <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-secondary align-self-start">
            <i class="bx bx-message-square-detail me-1"></i> Conversations
          </a>
        </div>
      </div>
    </div>

    @if(session('status'))
      <div class="col-12">
        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
      </div>
    @endif

    <div class="col-12">
      <form method="POST" action="{{ route('admin.ai.settings.update') }}" class="card">
        @csrf
        @method('PUT')
        <div class="card-body row g-3">
          <div class="col-12">
            <div class="form-check form-switch">
              <input type="hidden" name="enabled" value="0">
              <input class="form-check-input @error('enabled') is-invalid @enderror" type="checkbox" id="enabled" name="enabled" value="1" @checked(old('enabled', $settings->enabled))>
              <label class="form-check-label" for="enabled">Activer le widget</label>
              @error('enabled')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="model">Modele</label>
            <input class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model', $settings->model) }}" required>
            @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label" for="max_sources">Sources max</label>
            <input type="number" min="1" max="10" class="form-control @error('max_sources') is-invalid @enderror" id="max_sources" name="max_sources" value="{{ old('max_sources', $settings->max_sources) }}" required>
            @error('max_sources')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-3">
            <label class="form-label" for="max_message_chars">Caracteres max</label>
            <input type="number" min="200" max="2000" class="form-control @error('max_message_chars') is-invalid @enderror" id="max_message_chars" name="max_message_chars" value="{{ old('max_message_chars', $settings->max_message_chars) }}" required>
            @error('max_message_chars')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-4">
            <label class="form-label" for="fallback_contact_mode">Fallback contact</label>
            <select class="form-select @error('fallback_contact_mode') is-invalid @enderror" id="fallback_contact_mode" name="fallback_contact_mode" required>
              <option value="contact_form" @selected(old('fallback_contact_mode', $settings->fallback_contact_mode) === 'contact_form')>Formulaire contact</option>
              <option value="whatsapp" @selected(old('fallback_contact_mode', $settings->fallback_contact_mode) === 'whatsapp')>WhatsApp</option>
            </select>
            @error('fallback_contact_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label" for="greetings_fr">Accueil FR</label>
            <input class="form-control @error('greetings.fr') is-invalid @enderror" id="greetings_fr" name="greetings[fr]" value="{{ old('greetings.fr', $settings->greetings['fr'] ?? '') }}" required>
            @error('greetings.fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label" for="greetings_en">Accueil EN</label>
            <input class="form-control @error('greetings.en') is-invalid @enderror" id="greetings_en" name="greetings[en]" value="{{ old('greetings.en', $settings->greetings['en'] ?? '') }}" required>
            @error('greetings.en')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-12">
            <label class="form-label" for="display_rules_json">Regles d affichage widget</label>
            <textarea class="form-control font-monospace small @error('display_rules_json') is-invalid @enderror" id="display_rules_json" name="display_rules_json" rows="6">{{ $displayRulesJson }}</textarea>
            @error('display_rules_json')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted d-block mt-1">
              Exemple publication: <code>{"pages":["*"],"countries":["*"],"locales":["fr","en"]}</code>. Utilisez <code>*</code> pour tout autoriser.
            </small>
          </div>

          <div class="col-12">
            <label class="form-label" for="system_prompt">System prompt</label>
            <textarea class="form-control @error('system_prompt') is-invalid @enderror" id="system_prompt" name="system_prompt" rows="9" required>{{ old('system_prompt', $settings->system_prompt) }}</textarea>
            @error('system_prompt')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
