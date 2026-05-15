@extends('layouts/layoutMaster')

@php
  use Illuminate\Support\Str;

  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
  $displayTitle = $t($pageData['title'] ?? config('dream-digital.site.meta.title_default', 'Dream Digital'));
  $seoTitle = $t($pageData['seo_title'] ?? $displayTitle);
  $pageLead = $t($pageData['lead'] ?? '');
  $ogTitle = $seoTitle . ' | Dream Digital';
  $ogDescription = $t($pageData['meta_description'] ?? '') ?: ($pageLead !== '' ? mb_substr($pageLead, 0, 280) : ($t(config('dream-digital.site.meta.description_default', '')) ?: ''));
  $imagePath = $pageData['meta_image_path'] ?? null;
  $ogImage = blank($imagePath) ? null : (Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset(ltrim($imagePath, '/')));
  $pageSections = collect($pageData['sections'] ?? [])
    ->filter(fn ($section) => filled($section['heading'] ?? null) || filled($section['body_html'] ?? null) || filled($section['body'] ?? null))
    ->values();
  $faqItems = collect($pageData['faq'] ?? [])
    ->filter(fn ($item) => filled($item['question'] ?? null) && filled($item['answer'] ?? null))
    ->values();
@endphp

@section('title', $seoTitle . ' | Dream Digital')
@section('page-description', $ogDescription)
@section('og-title', $ogTitle)
@section('og-description', $ogDescription)
@if($ogImage)
  @section('og-image', $ogImage)
@endif

@php
  $crumbBase = rtrim(config('app.url'), '/') . "/{$locale}";
  $crumbHomeName = $locale === 'en' ? 'Home' : 'Accueil';
  $crumbItems = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => $crumbHomeName, 'item' => $crumbBase],
  ];
  if ($page === 'product' && !empty($service)) {
    $crumbItems[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $locale === 'en' ? 'Products' : 'Produits', 'item' => "{$crumbBase}/products"];
    $crumbItems[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $displayTitle, 'item' => "{$crumbBase}/products/" . ($service['slug'] ?? $service['id'] ?? '')];
  } else {
    $crumbItems[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $displayTitle, 'item' => "{$crumbBase}/{$page}"];
  }
  $crumbPayload = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $crumbItems];
  $pageUrl = $page === 'product' && !empty($service)
    ? "{$crumbBase}/products/" . ($service['slug'] ?? $service['id'] ?? '')
    : "{$crumbBase}/{$page}";
  $webPagePayload = [
    '@type' => 'WebPage',
    'name' => $displayTitle,
    'description' => $ogDescription,
    'url' => $pageUrl,
    'isPartOf' => [
      '@type' => 'WebSite',
      'name' => 'Dream Digital',
      'url' => rtrim(config('app.url'), '/'),
    ],
  ];
  if (!empty($pageData['seo_focus_keywords'])) {
    $webPagePayload['keywords'] = implode(', ', $pageData['seo_focus_keywords']);
  }
  $jsonLdGraph = [$webPagePayload];
  if ($faqItems->isNotEmpty()) {
    $jsonLdGraph[] = [
      '@type' => 'FAQPage',
      'mainEntity' => $faqItems->map(fn ($item) => [
        '@type' => 'Question',
        'name' => $item['question'],
        'acceptedAnswer' => [
          '@type' => 'Answer',
          'text' => $item['answer'],
        ],
      ])->all(),
    ];
  }
@endphp
@section('jsonld-breadcrumb'){!! json_encode($crumbPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}@endsection
@section('jsonld-extra'){!! json_encode(['@context' => 'https://schema.org', '@graph' => $jsonLdGraph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('content')
  <main class="dd-home dd-public-page dd-public-page--{{ $page }}">
    @if(in_array($page, ['pricing', 'contact'], true))
      @include('front.components.hero-banner', ['pageData' => $pageData, 'locale' => $locale])
    @else
      @include('front.components.hero-simple', ['pageData' => $pageData, 'locale' => $locale])
    @endif

    @if($pageSections->isNotEmpty())
      @include('front.components.cms-sections', [
        'sections' => $pageSections->all(),
        'locale' => $locale,
        'keywords' => $pageData['seo_focus_keywords'] ?? [],
        'links' => $pageData['internal_links'] ?? [],
      ])
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
        @include('front.components.product-proof', ['detail' => $productDetail ?? [], 'locale' => $locale])
        @include('front.components.blog-teaser', ['articles' => $blogGuides ?? [], 'locale' => $locale])
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
        @php($entities = collect(data_get($site, 'company.entities', []))->filter(fn ($entity) => filled(data_get($entity, 'city')) || filled(data_get($entity, 'latitude'))))
        <section class="dd-section dd-contact-page">
          @if(session('status'))
            <div class="dd-front-container">
              <div class="dd-contact-alert">{{ session('status') }}</div>
            </div>
          @endif
          <div class="dd-front-container dd-contact-page__grid">
            <article class="dd-contact-form-card">
              <i class="bx bx-envelope" aria-hidden="true"></i>
              <h2>Sales</h2>
              <p>{{ $locale === 'fr' ? 'Pour pricing, routes, volume, SMS, voice ou eSIM.' : 'For pricing, routes, volume, SMS, voice or eSIM.' }}</p>
              <form method="POST" action="{{ route('front.contact-leads.store', ['locale' => $locale]) }}" class="dd-contact-form">
                @csrf
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="dd-contact-form__trap" aria-hidden="true">
                <div class="dd-contact-form__grid">
                  <label>
                    <span>{{ $locale === 'fr' ? 'Nom' : 'Name' }}</span>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required maxlength="160">
                  </label>
                  <label>
                    <span>{{ $locale === 'fr' ? 'Societe' : 'Company' }}</span>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" maxlength="190">
                  </label>
                  <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" required maxlength="190">
                  </label>
                  <label>
                    <span>{{ $locale === 'fr' ? 'Telephone' : 'Phone' }}</span>
                    <input type="text" name="phone" value="{{ old('phone') }}" maxlength="80">
                  </label>
                  <label>
                    <span>Service</span>
                    <select name="service_interest">
                      <option value="">{{ $locale === 'fr' ? 'Choisir' : 'Select' }}</option>
                      @foreach(['sms' => 'SMS', 'voice' => 'Voice', 'esim' => 'eSIM', 'did' => 'DID', 'sip' => 'SIP', 'dialo' => 'Dialo', 'other' => 'Other'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('service_interest') === $value)>{{ $label }}</option>
                      @endforeach
                    </select>
                  </label>
                  <label>
                    <span>{{ $locale === 'fr' ? 'Volume mensuel' : 'Monthly volume' }}</span>
                    <input type="text" name="monthly_volume" value="{{ old('monthly_volume') }}" maxlength="80" placeholder="100k-500k">
                  </label>
                </div>
                <label>
                  <span>Message</span>
                  <textarea name="message" rows="5" required maxlength="3000">{{ old('message') }}</textarea>
                </label>
                <button type="submit" class="dd-button dd-button--primary">{{ $locale === 'fr' ? 'Envoyer la demande' : 'Send request' }}</button>
              </form>
            </article>
            <article>
              <i class="bx bx-buildings" aria-hidden="true"></i>
              <h2>Offices</h2>
              <p>{{ implode(' / ', $site['company']['offices'] ?? []) }}</p>
              @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Equipe disponible' : 'Team available'])
            </article>
          </div>
          @if($entities->isNotEmpty())
            <div class="dd-front-container dd-contact-entities">
              @foreach($entities as $entity)
                @php($mapUrl = filled(data_get($entity, 'latitude')) && filled(data_get($entity, 'longitude')) ? 'https://www.google.com/maps?q=' . data_get($entity, 'latitude') . ',' . data_get($entity, 'longitude') : null)
                <article class="dd-contact-entity">
                  <div>
                    <span>{{ strtoupper(data_get($entity, 'country_code', '')) }}</span>
                    <h3>{{ data_get($entity, 'city') }}{{ filled(data_get($entity, 'country_label')) ? ' - ' . data_get($entity, 'country_label') : '' }}</h3>
                    @if(filled(data_get($entity, 'address_line')))
                      <p>{{ data_get($entity, 'address_line') }}</p>
                    @endif
                  </div>
                  <dl>
                    @if(filled(data_get($entity, 'public_phone')))
                      <div>
                        <dt>{{ $locale === 'fr' ? 'Telephone' : 'Phone' }}</dt>
                        <dd><a href="tel:{{ data_get($entity, 'public_phone') }}">{{ data_get($entity, 'public_phone') }}</a></dd>
                      </div>
                    @endif
                    @if(filled(data_get($entity, 'whatsapp_number')))
                      <div>
                        <dt>WhatsApp</dt>
                        <dd>{{ data_get($entity, 'whatsapp_number') }}</dd>
                      </div>
                    @endif
                    @if($mapUrl)
                      <div>
                        <dt>GPS</dt>
                        <dd><a href="{{ $mapUrl }}" target="_blank" rel="noopener">{{ data_get($entity, 'latitude') }}, {{ data_get($entity, 'longitude') }}</a></dd>
                      </div>
                    @endif
                  </dl>
                </article>
              @endforeach
            </div>
          @endif
        </section>
        @break
    @endswitch

    @if($faqItems->isNotEmpty())
      @include('front.components.faq-accordion', ['items' => $faqItems->all(), 'locale' => $locale])
    @elseif(in_array($page, ['developers', 'pricing'], true))
      @include('front.components.faq-accordion', ['items' => $home['faq'] ?? [], 'locale' => $locale])
    @endif
  </main>
@endsection
