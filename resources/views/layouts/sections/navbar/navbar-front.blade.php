@php
  $salesEmail = config('dream-digital.site.contact.email_sales', 'sales@dream-digital.info');
  $locale = request()->route('locale') ?? session()->get('locale', 'fr');
  $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
  $pageUrl = fn ($page) => url("/{$locale}/{$page}");
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
        <i class="icon-base bx bx-menu icon-lg align-middle text-heading fw-medium"></i>
      </button>

      <div class="collapse navbar-collapse landing-nav-menu" id="ddFrontNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item"><a class="nav-link fw-medium" href="{{ $pageUrl('products') }}">Produits</a></li>
          <li class="nav-item"><a class="nav-link fw-medium" href="{{ $pageUrl('developers') }}">Developers</a></li>
          <li class="nav-item"><a class="nav-link fw-medium" href="{{ $pageUrl('solutions') }}">Solutions</a></li>
          <li class="nav-item"><a class="nav-link fw-medium" href="{{ $pageUrl('coverage') }}">Coverage</a></li>
          <li class="nav-item"><a class="nav-link fw-medium" href="{{ $pageUrl('pricing') }}">Pricing</a></li>
          <li class="nav-item"><a class="nav-link fw-medium" href="{{ $pageUrl('company') }}">Societe</a></li>
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
