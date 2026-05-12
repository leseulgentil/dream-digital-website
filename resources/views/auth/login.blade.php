@extends('layouts/layoutMaster')

@section('title', 'Connexion -- Admin Dream Digital')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <div class="card px-sm-6 px-0">
          <div class="card-body">
            <div class="dd-app-brand justify-content-center mb-4">
              <a href="{{ url('/') }}" class="dd-app-brand-link gap-2">
                <span class="dd-app-brand-logo demo">@include('_partials.macros')</span>
                <span class="dd-app-brand-text demo text-heading fw-bold">Dream Digital</span>
              </a>
            </div>

            <h4 class="mb-1 text-center">Admin Dream Digital</h4>
            <p class="mb-6 text-center text-muted">Connectez-vous pour acceder au back-office.</p>

            @if(session('status'))
              <div class="alert alert-info" role="alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mb-2" novalidate>
              @csrf

              <div class="mb-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" required autofocus autocomplete="username"
                  class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email') }}" placeholder="vous@dream-digital.info">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password">Mot de passe</label>
                <div class="input-group input-group-merge">
                  <input type="password" id="password" name="password" required autocomplete="current-password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                  <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                </div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              </div>

              <div class="mb-7">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                  <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
              </div>

              <button type="submit" class="btn btn-primary d-grid w-100 mb-2">Se connecter</button>
            </form>

            <p class="text-center mt-4 small text-muted mb-0">
              Acces admin uniquement. Pas de creation de compte publique.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
