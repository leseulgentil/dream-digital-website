@extends('layouts/layoutMaster')

@section('title', 'Company Profile -- Admin Dream Digital')

@section('content')
  <div class="row g-6">
    <div class="col-12">
      <div class="card">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between gap-4">
          <div>
            <h1 class="h3 mb-2">Company Profile</h1>
            <p class="mb-0 text-muted">Entites Dream Digital par pays, donnees bilingues, contacts publics et coordonnees GPS pour les cartes.</p>
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
            @foreach($entityCountries as $countryCode => $country)
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab" data-bs-target="#company-country-{{ $countryCode }}" role="tab">
                  {{ strtoupper($countryCode) }} · {{ $country['label'] }}
                </button>
              </li>
            @endforeach
          </ul>

          <div class="tab-content p-0">
            @foreach($entityCountries as $countryCode => $country)
              <div class="tab-pane fade @if($loop->first) show active @endif" id="company-country-{{ $countryCode }}" role="tabpanel">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                  <div>
                    <h2 class="h5 mb-1">{{ $country['label'] }} · {{ $country['city'] }}</h2>
                    <p class="text-muted mb-0">Chaque entite pays dispose de ses informations FR/EN, y compris telephone, WhatsApp, adresse et GPS.</p>
                  </div>
                  <span class="badge bg-label-primary align-self-start">{{ strtoupper($countryCode) }}</span>
                </div>

                <ul class="nav nav-tabs mb-4" role="tablist">
                  @foreach($locales as $locale)
                    <li class="nav-item" role="presentation">
                      <button type="button" class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab" data-bs-target="#company-profile-{{ $countryCode }}-{{ $locale }}" role="tab">
                        Profil {{ strtoupper($locale) }}
                      </button>
                    </li>
                  @endforeach
                </ul>

                <div class="tab-content p-0">
                  @foreach($locales as $locale)
                    @php($profile = $profiles[$countryCode][$locale])
                    @php($fieldPrefix = "profiles[$countryCode][$locale]")
                    @php($errorPrefix = "profiles.$countryCode.$locale")
                    <div class="tab-pane fade @if($loop->first) show active @endif" id="company-profile-{{ $countryCode }}-{{ $locale }}" role="tabpanel">
                      <input type="hidden" name="{{ $fieldPrefix }}[country_code]" value="{{ $countryCode }}">
                      <input type="hidden" name="{{ $fieldPrefix }}[locale]" value="{{ $locale }}">

                      <div class="row g-4">
                        <div class="col-md-6">
                          <label class="form-label" for="company_name_{{ $countryCode }}_{{ $locale }}">Nom public <span class="text-danger">*</span></label>
                          <input type="text" id="company_name_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[company_name]" maxlength="120" class="form-control @error("$errorPrefix.company_name") is-invalid @enderror" value="{{ old("$errorPrefix.company_name", $profile->company_name) }}" required>
                          @error("$errorPrefix.company_name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                          <label class="form-label" for="legal_name_{{ $countryCode }}_{{ $locale }}">Raison sociale / legal name</label>
                          <input type="text" id="legal_name_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[legal_name]" maxlength="160" class="form-control @error("$errorPrefix.legal_name") is-invalid @enderror" value="{{ old("$errorPrefix.legal_name", $profile->legal_name) }}" placeholder="DREAM DIGITAL">
                          @error("$errorPrefix.legal_name")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="public_phone_{{ $countryCode }}_{{ $locale }}">Telephone public</label>
                          <input type="text" id="public_phone_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[public_phone]" maxlength="60" class="form-control @error("$errorPrefix.public_phone") is-invalid @enderror" value="{{ old("$errorPrefix.public_phone", $profile->public_phone) }}" placeholder="+243...">
                          @error("$errorPrefix.public_phone")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="whatsapp_number_{{ $countryCode }}_{{ $locale }}">WhatsApp</label>
                          <input type="text" id="whatsapp_number_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[whatsapp_number]" maxlength="60" class="form-control @error("$errorPrefix.whatsapp_number") is-invalid @enderror" value="{{ old("$errorPrefix.whatsapp_number", $profile->whatsapp_number) }}" placeholder="+243...">
                          @error("$errorPrefix.whatsapp_number")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="support_hours_{{ $countryCode }}_{{ $locale }}">Horaires support</label>
                          <input type="text" id="support_hours_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[support_hours]" maxlength="160" class="form-control @error("$errorPrefix.support_hours") is-invalid @enderror" value="{{ old("$errorPrefix.support_hours", $profile->support_hours) }}" placeholder="Lundi-vendredi, 09:00-18:00 CAT">
                          @error("$errorPrefix.support_hours")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                          <label class="form-label" for="address_line_{{ $countryCode }}_{{ $locale }}">Adresse publique</label>
                          <input type="text" id="address_line_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[address_line]" maxlength="255" class="form-control @error("$errorPrefix.address_line") is-invalid @enderror" value="{{ old("$errorPrefix.address_line", $profile->address_line) }}">
                          @error("$errorPrefix.address_line")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                          <label class="form-label" for="city_{{ $countryCode }}_{{ $locale }}">Ville</label>
                          <input type="text" id="city_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[city]" maxlength="120" class="form-control @error("$errorPrefix.city") is-invalid @enderror" value="{{ old("$errorPrefix.city", $profile->city) }}">
                          @error("$errorPrefix.city")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                          <label class="form-label" for="country_label_{{ $countryCode }}_{{ $locale }}">Pays</label>
                          <input type="text" id="country_label_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[country_label]" maxlength="120" class="form-control @error("$errorPrefix.country_label") is-invalid @enderror" value="{{ old("$errorPrefix.country_label", $profile->country_label) }}">
                          @error("$errorPrefix.country_label")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                          <label class="form-label" for="latitude_{{ $countryCode }}_{{ $locale }}">Latitude</label>
                          <input type="text" id="latitude_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[latitude]" maxlength="32" class="form-control @error("$errorPrefix.latitude") is-invalid @enderror" value="{{ old("$errorPrefix.latitude", $profile->latitude) }}" placeholder="-4.3250">
                          @error("$errorPrefix.latitude")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                          <label class="form-label" for="longitude_{{ $countryCode }}_{{ $locale }}">Longitude</label>
                          <input type="text" id="longitude_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[longitude]" maxlength="32" class="form-control @error("$errorPrefix.longitude") is-invalid @enderror" value="{{ old("$errorPrefix.longitude", $profile->longitude) }}" placeholder="15.3222">
                          @error("$errorPrefix.longitude")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                          <label class="form-label" for="registration_number_{{ $countryCode }}_{{ $locale }}">RCCM / registration number</label>
                          <input type="text" id="registration_number_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[registration_number]" maxlength="160" class="form-control @error("$errorPrefix.registration_number") is-invalid @enderror" value="{{ old("$errorPrefix.registration_number", $profile->registration_number) }}">
                          @error("$errorPrefix.registration_number")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                          <label class="form-label" for="tax_id_{{ $countryCode }}_{{ $locale }}">ID fiscal / tax ID</label>
                          <input type="text" id="tax_id_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[tax_id]" maxlength="160" class="form-control @error("$errorPrefix.tax_id") is-invalid @enderror" value="{{ old("$errorPrefix.tax_id", $profile->tax_id) }}">
                          @error("$errorPrefix.tax_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="email_sales_{{ $countryCode }}_{{ $locale }}">Email sales</label>
                          <input type="email" id="email_sales_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[email_sales]" maxlength="160" class="form-control @error("$errorPrefix.email_sales") is-invalid @enderror" value="{{ old("$errorPrefix.email_sales", $profile->email_sales) }}">
                          @error("$errorPrefix.email_sales")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="email_support_{{ $countryCode }}_{{ $locale }}">Email support</label>
                          <input type="email" id="email_support_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[email_support]" maxlength="160" class="form-control @error("$errorPrefix.email_support") is-invalid @enderror" value="{{ old("$errorPrefix.email_support", $profile->email_support) }}">
                          @error("$errorPrefix.email_support")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="email_security_{{ $countryCode }}_{{ $locale }}">Email security.txt</label>
                          <input type="email" id="email_security_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[email_security]" maxlength="160" class="form-control @error("$errorPrefix.email_security") is-invalid @enderror" value="{{ old("$errorPrefix.email_security", $profile->email_security) }}">
                          @error("$errorPrefix.email_security")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="email_privacy_{{ $countryCode }}_{{ $locale }}">Email privacy/RGPD</label>
                          <input type="email" id="email_privacy_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[email_privacy]" maxlength="160" class="form-control @error("$errorPrefix.email_privacy") is-invalid @enderror" value="{{ old("$errorPrefix.email_privacy", $profile->email_privacy) }}">
                          @error("$errorPrefix.email_privacy")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="social_linkedin_{{ $countryCode }}_{{ $locale }}">LinkedIn</label>
                          <input type="url" id="social_linkedin_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[social_linkedin]" maxlength="500" class="form-control @error("$errorPrefix.social_linkedin") is-invalid @enderror" value="{{ old("$errorPrefix.social_linkedin", $profile->social_linkedin) }}">
                          @error("$errorPrefix.social_linkedin")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="social_twitter_{{ $countryCode }}_{{ $locale }}">X / Twitter</label>
                          <input type="url" id="social_twitter_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[social_twitter]" maxlength="500" class="form-control @error("$errorPrefix.social_twitter") is-invalid @enderror" value="{{ old("$errorPrefix.social_twitter", $profile->social_twitter) }}">
                          @error("$errorPrefix.social_twitter")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                          <label class="form-label" for="social_github_{{ $countryCode }}_{{ $locale }}">GitHub</label>
                          <input type="url" id="social_github_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[social_github]" maxlength="500" class="form-control @error("$errorPrefix.social_github") is-invalid @enderror" value="{{ old("$errorPrefix.social_github", $profile->social_github) }}">
                          @error("$errorPrefix.social_github")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                          <label class="form-label" for="og_image_path_{{ $countryCode }}_{{ $locale }}">Image OpenGraph par defaut</label>
                          <input type="text" id="og_image_path_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[og_image_path]" maxlength="500" class="form-control @error("$errorPrefix.og_image_path") is-invalid @enderror" value="{{ old("$errorPrefix.og_image_path", $profile->og_image_path) }}" placeholder="/img/brand/logo-dd-horizontal.png">
                          @error("$errorPrefix.og_image_path")<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                          <div class="form-check form-switch">
                            <input type="hidden" name="{{ $fieldPrefix }}[legal_validated]" value="0">
                            <input class="form-check-input" type="checkbox" id="legal_validated_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[legal_validated]" value="1" @checked(old("$errorPrefix.legal_validated", $profile->legal_validated))>
                            <label class="form-check-label" for="legal_validated_{{ $countryCode }}_{{ $locale }}">Legal valide</label>
                          </div>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                          <div class="form-check form-switch">
                            <input type="hidden" name="{{ $fieldPrefix }}[admin_password_rotated]" value="0">
                            <input class="form-check-input" type="checkbox" id="admin_password_rotated_{{ $countryCode }}_{{ $locale }}" name="{{ $fieldPrefix }}[admin_password_rotated]" value="1" @checked(old("$errorPrefix.admin_password_rotated", $profile->admin_password_rotated))>
                            <label class="form-check-label" for="admin_password_rotated_{{ $countryCode }}_{{ $locale }}">Admin password rotate</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
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
