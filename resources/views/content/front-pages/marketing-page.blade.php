@extends('layouts/layoutMaster')

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
  $title = $t($pageData['title'] ?? config('dream-digital.site.meta.title_default', 'Dream Digital'));
  $pageLead = $t($pageData['lead'] ?? '');
  $pageEyebrow = $t($pageData['eyebrow'] ?? '');
  $ogTitle = trim($pageEyebrow . ($pageEyebrow ? ' — ' : '') . $title) . ' | Dream Digital';
  $ogDescription = $pageLead !== '' ? mb_substr($pageLead, 0, 280) : ($t(config('dream-digital.site.meta.description_default', '')) ?: '');
@endphp

@section('title', $title . ' | Dream Digital')
@section('page-description', $ogDescription)
@section('og-title', $ogTitle)
@section('og-description', $ogDescription)

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/front-page-landing.js'])
@endsection

@section('content')
  <main class="dd-home dd-public-page dd-public-page--{{ $page }}">
    @if(in_array($page, ['pricing', 'contact'], true))
      @include('front.components.hero-banner', ['pageData' => $pageData, 'locale' => $locale])
    @else
      @include('front.components.hero-simple', ['pageData' => $pageData, 'locale' => $locale])
    @endif

    @switch($page)
      @case('products')
        @include('front.components.service-grid', ['services' => $services, 'locale' => $locale])
        @include('front.components.stats-strip', ['stats' => $stats, 'locale' => $locale])
        @include('front.components.cta-banner', ['site' => $site, 'locale' => $locale])
        @break

      @case('product')
        <section class="dd-section">
          <div class="dd-front-container dd-product-detail">
            <div class="dd-product-detail__summary">
              <span class="dd-service-card__icon"><i class="bx {{ $service['icon'] ?? 'bx-radio-circle' }}" aria-hidden="true"></i></span>
              <h2>{{ $t($service['name'] ?? '') }}</h2>
              <p>{{ $t($service['description'] ?? $service['tagline'] ?? '') }}</p>
            </div>
            @include('front.components.feature-list', ['features' => $features['developers'] ?? [], 'locale' => $locale])
          </div>
        </section>
        @include('front.components.cta-banner', ['site' => $site, 'locale' => $locale])
        @break

      @case('developers')
        <section class="dd-section dd-developer dd-developer--page">
          <div class="dd-front-container dd-developer__grid">
            <div>
              <p class="dd-eyebrow">API</p>
              <h2>{{ $locale === 'fr' ? 'Un parcours technique clair du test au live' : 'A clear technical path from test to live' }}</h2>
              <p>{{ $locale === 'fr' ? 'Les composants de cette page preparent la future documentation publique, sans attendre que le portail client soit termine.' : 'These components prepare the future public documentation without waiting for the client portal to be finished.' }}</p>
              @include('front.components.feature-list', ['features' => $features['developers'] ?? [], 'locale' => $locale])
            </div>
            <div class="dd-developer__console">
              @include('front.components.code-preview', ['locale' => $locale])
              @include('front.components.live-feed', ['items' => $liveFeed, 'locale' => $locale])
            </div>
          </div>
        </section>
        @include('front.components.faq-accordion', ['items' => $home['faq'] ?? [], 'locale' => $locale])
        @break

      @case('solutions')
        @include('front.components.industry-grid', ['industries' => $industries, 'locale' => $locale])
        @include('front.components.stats-strip', ['stats' => $stats, 'locale' => $locale])
        @include('front.components.cta-banner', ['site' => $site, 'locale' => $locale])
        @break

      @case('coverage')
        @include('front.components.coverage-map', ['coverage' => $coverage, 'locale' => $locale])
        <section class="dd-section dd-corridors">
          <div class="dd-front-container">
            <div class="dd-section-heading">
              <p class="dd-eyebrow">Corridors</p>
              <h2>{{ $locale === 'fr' ? 'Routes suivies de pres' : 'Closely monitored routes' }}</h2>
            </div>
            <div class="dd-corridors__grid">
              @foreach ($corridors as $corridor)
                @include('front.components.corridor-card', ['corridor' => $corridor, 'locale' => $locale])
              @endforeach
            </div>
          </div>
        </section>
        @break

      @case('pricing')
        @include('front.components.pricing-cards', ['plans' => $home['pricing'] ?? [], 'locale' => $locale])
        <section class="dd-section dd-corridors">
          <div class="dd-front-container">
            <div class="dd-section-heading">
              <p class="dd-eyebrow">Routes</p>
              <h2>{{ $locale === 'fr' ? 'Le pricing depend du corridor' : 'Pricing depends on the corridor' }}</h2>
              <p>{{ $locale === 'fr' ? 'Ces cartes preparent le futur module admin de pricing multi-pays prevu au cahier des charges.' : 'These cards prepare the future multi-country pricing admin module from the specifications.' }}</p>
            </div>
            <div class="dd-corridors__grid">
              @foreach ($corridors as $corridor)
                @include('front.components.corridor-card', ['corridor' => $corridor, 'locale' => $locale])
              @endforeach
            </div>
          </div>
        </section>
        @include('front.components.faq-accordion', ['items' => $home['faq'] ?? [], 'locale' => $locale])
        @break

      @case('company')
        @include('front.components.stats-strip', ['stats' => $stats, 'locale' => $locale])
        @include('front.components.coverage-map', ['coverage' => $coverage, 'locale' => $locale])
        <section class="dd-section">
          <div class="dd-front-container">
            @include('front.components.feature-list', ['features' => $features['admin'] ?? [], 'locale' => $locale])
          </div>
        </section>
        @break

      @case('contact')
        <section class="dd-section dd-contact-page">
          <div class="dd-front-container dd-contact-page__grid">
            <article>
              <i class="bx bx-envelope" aria-hidden="true"></i>
              <h2>Sales</h2>
              <p>{{ $locale === 'fr' ? 'Pour pricing, routes, volume, SMS, voice ou eSIM.' : 'For pricing, routes, volume, SMS, voice or eSIM.' }}</p>
              <a class="dd-button dd-button--primary" href="mailto:{{ $site['contact']['email_sales'] ?? 'sales@dream-digital.info' }}">sales@dream-digital.info</a>
            </article>
            <article>
              <i class="bx bx-buildings" aria-hidden="true"></i>
              <h2>Offices</h2>
              <p>{{ implode(' / ', $site['company']['offices'] ?? []) }}</p>
              @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Equipe disponible' : 'Team available'])
            </article>
          </div>
        </section>
        @break
    @endswitch
  </main>
@endsection
