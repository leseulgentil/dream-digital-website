@extends('layouts/layoutMaster')

@section('title', 'Company Profile -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Company Profile</h1>
            <p class="mb-0 text-muted">Informations business utilisees par le site public, le SEO, security.txt et le readiness check.</p>
          </div>
          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary align-self-start">
            <i class="bx bx-arrow-back me-1"></i> Dashboard
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
      <form method="POST" action="{{ route('admin.company-profile.update') }}" class="card" novalidate>
        @csrf
        @method('PUT')

        <div class="card-body">
          <ul class="nav nav-pills mb-4" role="tablist">
            @foreach($locales as $locale)
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab" data-bs-target="#company-profile-{{ $locale }}" role="tab">
                  {{ strtoupper($locale) }}
                </button>
              </li>
            @endforeach
          </ul>

          <div class="tab-content p-0">
            @foreach($locales as $locale)
              @php($profile = $profiles[$locale])
              <div class="tab-pane fade @if($loop->first) show active @endif" id="company-profile-{{ $locale }}" role="tabpanel">
                <input type="hidden" name="profiles[{{ $locale }}][locale]" value="{{ $locale }}">

                <div class="row g-4">
                  <div class="col-12">
                    <h2 class="h5 mb-1">Profil {{ strtoupper($locale) }}</h2>
                    <p class="text-muted mb-0">Les champs peuvent diverger par langue si une page legale ou SEO doit afficher une variante localisee.</p>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="company_name_{{ $locale }}">Nom public <span class="text-danger">*</span></label>
                    <input type="text" id="company_name_{{ $locale }}" name="profiles[{{ $locale }}][company_name]" maxlength="120" class="form-control @error("profiles.$locale.company_name") is-invalid @enderror" value="{{ old("profiles.$locale.company_name", $profile->company_name) }}" required>
                    @error("profiles.$locale.company_name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="legal_name_{{ $locale }}">Raison sociale / legal name</label>
                    <input type="text" id="legal_name_{{ $locale }}" name="profiles[{{ $locale }}][legal_name]" maxlength="160" class="form-control @error("profiles.$locale.legal_name") is-invalid @enderror" value="{{ old("profiles.$locale.legal_name", $profile->legal_name) }}" placeholder="DREAM DIGITAL">
                    @error("profiles.$locale.legal_name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-4">
                    <label class="form-label" for="public_phone_{{ $locale }}">Telephone public</label>
                    <input type="text" id="public_phone_{{ $locale }}" name="profiles[{{ $locale }}][public_phone]" maxlength="60" class="form-control @error("profiles.$locale.public_phone") is-invalid @enderror" value="{{ old("profiles.$locale.public_phone", $profile->public_phone) }}" placeholder="+243...">
                    @error("profiles.$locale.public_phone")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-4">
                    <label class="form-label" for="email_sales_{{ $locale }}">Email sales</label>
                    <input type="email" id="email_sales_{{ $locale }}" name="profiles[{{ $locale }}][email_sales]" maxlength="160" class="form-control @error("profiles.$locale.email_sales") is-invalid @enderror" value="{{ old("profiles.$locale.email_sales", $profile->email_sales) }}">
                    @error("profiles.$locale.email_sales")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-4">
                    <label class="form-label" for="email_support_{{ $locale }}">Email support</label>
                    <input type="email" id="email_support_{{ $locale }}" name="profiles[{{ $locale }}][email_support]" maxlength="160" class="form-control @error("profiles.$locale.email_support") is-invalid @enderror" value="{{ old("profiles.$locale.email_support", $profile->email_support) }}">
                    @error("profiles.$locale.email_support")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="email_security_{{ $locale }}">Email security.txt</label>
                    <input type="email" id="email_security_{{ $locale }}" name="profiles[{{ $locale }}][email_security]" maxlength="160" class="form-control @error("profiles.$locale.email_security") is-invalid @enderror" value="{{ old("profiles.$locale.email_security", $profile->email_security) }}">
                    @error("profiles.$locale.email_security")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="email_privacy_{{ $locale }}">Email privacy/RGPD</label>
                    <input type="email" id="email_privacy_{{ $locale }}" name="profiles[{{ $locale }}][email_privacy]" maxlength="160" class="form-control @error("profiles.$locale.email_privacy") is-invalid @enderror" value="{{ old("profiles.$locale.email_privacy", $profile->email_privacy) }}">
                    @error("profiles.$locale.email_privacy")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-4">
                    <label class="form-label" for="social_linkedin_{{ $locale }}">LinkedIn</label>
                    <input type="url" id="social_linkedin_{{ $locale }}" name="profiles[{{ $locale }}][social_linkedin]" maxlength="500" class="form-control @error("profiles.$locale.social_linkedin") is-invalid @enderror" value="{{ old("profiles.$locale.social_linkedin", $profile->social_linkedin) }}">
                    @error("profiles.$locale.social_linkedin")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-4">
                    <label class="form-label" for="social_twitter_{{ $locale }}">X / Twitter</label>
                    <input type="url" id="social_twitter_{{ $locale }}" name="profiles[{{ $locale }}][social_twitter]" maxlength="500" class="form-control @error("profiles.$locale.social_twitter") is-invalid @enderror" value="{{ old("profiles.$locale.social_twitter", $profile->social_twitter) }}">
                    @error("profiles.$locale.social_twitter")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-4">
                    <label class="form-label" for="social_github_{{ $locale }}">GitHub</label>
                    <input type="url" id="social_github_{{ $locale }}" name="profiles[{{ $locale }}][social_github]" maxlength="500" class="form-control @error("profiles.$locale.social_github") is-invalid @enderror" value="{{ old("profiles.$locale.social_github", $profile->social_github) }}">
                    @error("profiles.$locale.social_github")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="og_image_path_{{ $locale }}">Image OpenGraph par defaut</label>
                    <input type="text" id="og_image_path_{{ $locale }}" name="profiles[{{ $locale }}][og_image_path]" maxlength="500" class="form-control @error("profiles.$locale.og_image_path") is-invalid @enderror" value="{{ old("profiles.$locale.og_image_path", $profile->og_image_path) }}" placeholder="/img/brand/logo-dd-horizontal.png">
                    @error("profiles.$locale.og_image_path")<div class="invalid-feedback">{{ $message }}</div>@enderror
                  </div>

                  <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                      <input type="hidden" name="profiles[{{ $locale }}][legal_validated]" value="0">
                      <input class="form-check-input" type="checkbox" id="legal_validated_{{ $locale }}" name="profiles[{{ $locale }}][legal_validated]" value="1" @checked(old("profiles.$locale.legal_validated", $profile->legal_validated))>
                      <label class="form-check-label" for="legal_validated_{{ $locale }}">Legal valide</label>
                    </div>
                  </div>

                  <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                      <input type="hidden" name="profiles[{{ $locale }}][admin_password_rotated]" value="0">
                      <input class="form-check-input" type="checkbox" id="admin_password_rotated_{{ $locale }}" name="profiles[{{ $locale }}][admin_password_rotated]" value="1" @checked(old("profiles.$locale.admin_password_rotated", $profile->admin_password_rotated))>
                      <label class="form-check-label" for="admin_password_rotated_{{ $locale }}">Admin password rotate</label>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Annuler</a>
          <button type="submit" class="btn btn-primary">
            <i class="bx bx-save me-1"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
