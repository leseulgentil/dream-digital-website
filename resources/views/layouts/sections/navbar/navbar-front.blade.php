@php
  $salesEmail = config('dream-digital.site.contact.email_sales', 'sales@dream-digital.info');
  $locale = request()->route('locale') ?? session()->get('locale', 'fr');
  $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
  $pageUrl = fn ($page) => url("/{$locale}/{$page}");
  $isActive = fn ($segment) => request()->is("{$locale}/{$segment}*") ? 'is-active' : '';
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : ($value ?? '');
  $services = collect(config('dream-digital.services.items', []))->where('active', true)->sortBy('order')->values();
  $industries = collect(config('dream-digital.industries.items', []))->where('active', true)->sortBy('order')->values();
@endphp

<nav class="dd-layout-navbar dd-front-navbar shadow-none py-0" aria-label="Navigation principale">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-6">
      <a href="{{ url('/') }}" class="dd-app-brand-link navbar-brand d-flex align-items-center py-0 me-4">
        <span class="dd-app-brand-logo demo">@include('_partials.macros')</span>
        <span class="dd-app-brand-text demo dd-menu-text fw-bold ms-2 ps-1">{{ config('dream-digital.site.company.name', 'Dream Digital') }}</span>
      </a>

      <button class="navbar-toggler border-0 px-0" type="button" data-bs-toggle="collapse"
        data-bs-target="#ddFrontNav" aria-controls="ddFrontNav" aria-expanded="false" aria-label="Ouvrir la navigation">
        <i class="icon-base bx bx-menu icon-lg align-middle fw-medium"></i>
      </button>

      <div class="collapse navbar-collapse landing-nav-menu" id="ddFrontNav">
        <ul class="navbar-nav mx-auto dd-megamenu">

          {{-- Produits : 6 services depuis config('dream-digital.services') --}}
          <li class="nav-item dropdown dd-megamenu-item">
            <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('products') }}"
              data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
              Produits
            </button>
            <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--wide" role="menu" aria-label="Produits">
              <div class="dd-megamenu-grid dd-megamenu-grid--3col">
                @foreach ($services as $service)
                  <a class="dd-megamenu-card" href="{{ url("/{$locale}/products/" . ($service['slug'] ?? $service['id'])) }}" role="menuitem">
                    <span class="dd-megamenu-card__icon"><i class="bx {{ $service['icon'] ?? 'bx-radio-circle' }}" aria-hidden="true"></i></span>
                    <span class="dd-megamenu-card__body">
                      <span class="dd-megamenu-card__name">{{ $t($service['name'] ?? '') }}</span>
                      <span class="dd-megamenu-card__tagline">{{ $t($service['tagline'] ?? '') }}</span>
                    </span>
                  </a>
                @endforeach
              </div>
              <a class="dd-megamenu-footer" href="{{ $pageUrl('products') }}" role="menuitem">
                {{ $locale === 'fr' ? 'Voir tous les produits' : 'See all products' }} <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
              </a>
            </div>
          </li>

          {{-- Developers : 4 colonnes --}}
          <li class="nav-item dropdown dd-megamenu-item">
            <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('developers') }}"
              data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
              Developers
            </button>
            <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--medium" role="menu" aria-label="Developers">
              <div class="dd-megamenu-grid dd-megamenu-grid--2col">
                <a class="dd-megamenu-card" href="{{ $pageUrl('developers') }}#docs" role="menuitem">
                  <span class="dd-megamenu-card__icon"><i class="bx bx-book-content" aria-hidden="true"></i></span>
                  <span class="dd-megamenu-card__body">
                    <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Documentation API' : 'API Documentation' }}</span>
                    <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Bientot publiee.' : 'Coming soon.' }}</span>
                  </span>
                </a>
                <a class="dd-megamenu-card" href="{{ $pageUrl('developers') }}#webhooks" role="menuitem">
                  <span class="dd-megamenu-card__icon"><i class="bx bx-broadcast" aria-hidden="true"></i></span>
                  <span class="dd-megamenu-card__body">
                    <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Webhooks DLR' : 'DLR Webhooks' }}</span>
                    <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Notifications signees, retry policy.' : 'Signed events, retry policy.' }}</span>
                  </span>
                </a>
                <a class="dd-megamenu-card" href="{{ $pageUrl('developers') }}#sandbox" role="menuitem">
                  <span class="dd-megamenu-card__icon"><i class="bx bx-code-block" aria-hidden="true"></i></span>
                  <span class="dd-megamenu-card__body">
                    <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Sandbox isole' : 'Isolated Sandbox' }}</span>
                    <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Cles de test, aucun frais.' : 'Test keys, no charge.' }}</span>
                  </span>
                </a>
                <a class="dd-megamenu-card" href="{{ $pageUrl('pricing') }}" role="menuitem">
                  <span class="dd-megamenu-card__icon"><i class="bx bx-dollar-circle" aria-hidden="true"></i></span>
                  <span class="dd-megamenu-card__body">
                    <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Pricing API' : 'API Pricing' }}</span>
                    <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Tarifs et corridors negocies.' : 'Rates and negotiated routes.' }}</span>
                  </span>
                </a>
              </div>
            </div>
          </li>

          {{-- Solutions : 4 industries depuis config('dream-digital.industries') --}}
          <li class="nav-item dropdown dd-megamenu-item">
            <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('solutions') }}"
              data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
              Solutions
            </button>
            <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--medium" role="menu" aria-label="Solutions">
              <div class="dd-megamenu-grid dd-megamenu-grid--2col">
                @foreach ($industries as $industry)
                  <a class="dd-megamenu-card" href="{{ $pageUrl('solutions') }}#{{ $industry['id'] }}" role="menuitem">
                    <span class="dd-megamenu-card__icon"><i class="bx {{ $industry['icon'] ?? 'bx-grid-alt' }}" aria-hidden="true"></i></span>
                    <span class="dd-megamenu-card__body">
                      <span class="dd-megamenu-card__name">{{ $t($industry['name'] ?? '') }}</span>
                      <span class="dd-megamenu-card__tagline">{{ $t($industry['description'] ?? '') }}</span>
                    </span>
                  </a>
                @endforeach
              </div>
              <a class="dd-megamenu-footer" href="{{ $pageUrl('solutions') }}" role="menuitem">
                {{ $locale === 'fr' ? 'Voir toutes les solutions' : 'See all solutions' }} <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
              </a>
            </div>
          </li>

          {{-- Coverage et Pricing : liens simples --}}
          <li class="nav-item"><a class="nav-link fw-medium {{ $isActive('coverage') }}" href="{{ $pageUrl('coverage') }}">Coverage</a></li>
          <li class="nav-item"><a class="nav-link fw-medium {{ $isActive('pricing') }}" href="{{ $pageUrl('pricing') }}">Pricing</a></li>
          <li class="nav-item"><a class="nav-link fw-medium {{ $isActive('blog') }}" href="{{ $pageUrl('blog') }}">Blog</a></li>

          {{-- Societe : 2 liens --}}
          <li class="nav-item dropdown dd-megamenu-item dd-megamenu-item--narrow">
            <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('company') }}"
              data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
              Societe
            </button>
            <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--narrow" role="menu" aria-label="Societe">
              <div class="dd-megamenu-grid dd-megamenu-grid--1col">
                <a class="dd-megamenu-card" href="{{ $pageUrl('company') }}" role="menuitem">
                  <span class="dd-megamenu-card__icon"><i class="bx bx-buildings" aria-hidden="true"></i></span>
                  <span class="dd-megamenu-card__body">
                    <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'A propos' : 'About' }}</span>
                    <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Equipe, bureaux, mission.' : 'Team, offices, mission.' }}</span>
                  </span>
                </a>
                <a class="dd-megamenu-card" href="{{ $pageUrl('contact') }}" role="menuitem">
                  <span class="dd-megamenu-card__icon"><i class="bx bx-conversation" aria-hidden="true"></i></span>
                  <span class="dd-megamenu-card__body">
                    <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Contact' : 'Contact' }}</span>
                    <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Sales, support, presse.' : 'Sales, support, press.' }}</span>
                  </span>
                </a>
              </div>
            </div>
          </li>

        </ul>
      </div>

      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <li class="nav-item dropdown-style-switcher dropdown me-2">
          <button type="button" class="nav-link dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-label="Theme" aria-expanded="false">
            <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
            <li><button type="button" class="dropdown-item" data-theme="light"><i class="icon-base bx bx-sun icon-md me-2"></i>Light</button></li>
            <li><button type="button" class="dropdown-item" data-theme="dark"><i class="icon-base bx bx-moon icon-md me-2"></i>Dark</button></li>
            <li><button type="button" class="dropdown-item" data-theme="system"><i class="icon-base bx bx-desktop icon-md me-2"></i>System</button></li>
          </ul>
        </li>
        <li class="nav-item d-none d-lg-flex me-2">
          @include('front.components.country-language-switcher', ['locale' => $locale])
        </li>
        <li class="d-none d-md-block">
          <a href="{{ $pageUrl('contact') }}" class="btn btn-primary">
            <span class="icon-base bx bx-conversation scaleX-n1-rtl me-1"></span>
            <span>Contact</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
