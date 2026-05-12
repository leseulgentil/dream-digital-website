@php
  $site = config('dream-digital.site');
  $services = collect(config('dream-digital.services.items', []))->where('active', true)->sortBy('order')->values();
  $locale = request()->route('locale') ?? session()->get('locale', 'fr');
  $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
  $pageUrl = fn ($page) => url("/{$locale}/{$page}");
@endphp

<footer class="dd-front-footer" aria-label="Footer">
  <div class="dd-front-container dd-front-footer__grid">
    <div class="dd-front-footer__brand">
      <a href="{{ url('/') }}" class="dd-app-brand-link d-inline-flex align-items-center mb-4">
        <span class="dd-app-brand-logo demo">@include('_partials.macros')</span>
        <span class="dd-app-brand-text demo fw-bold ms-2 ps-1">{{ $site['company']['name'] ?? 'Dream Digital' }}</span>
      </a>
      <p>{{ $site['meta']['description_default'] ?? 'Voice. SMS. eSIM. And More.' }}</p>
      <a href="{{ $pageUrl('contact') }}" class="dd-front-footer__status">
        <span></span> {{ $locale === 'fr' ? 'Tous les services operationnels' : 'All services operational' }}
      </a>
    </div>

    <nav aria-label="Produits">
      <h2>Produits</h2>
      @foreach ($services as $service)
        <a href="{{ url("/{$locale}/products/" . ($service['slug'] ?? $service['id'])) }}">{{ $service['name']['fr'] ?? $service['id'] }}</a>
      @endforeach
    </nav>

    <nav aria-label="Developpeurs">
      <h2>Developpeurs</h2>
      <a href="{{ $pageUrl('developers') }}">Documentation API</a>
      <a href="{{ $pageUrl('developers') }}">Webhooks</a>
      <a href="{{ $pageUrl('developers') }}">Sandbox</a>
      <a href="{{ $pageUrl('pricing') }}">Pricing API</a>
    </nav>

    <nav aria-label="Societe">
      <h2>Societe</h2>
      <a href="{{ $pageUrl('coverage') }}">Coverage</a>
      <a href="{{ $pageUrl('solutions') }}">Solutions</a>
      <a href="{{ $pageUrl('company') }}">A propos</a>
      <a href="{{ $pageUrl('contact') }}">Contact</a>
    </nav>
  </div>

  <div class="dd-front-container dd-front-footer__bottom">
    <span>(c) {{ date('Y') }} Dream Digital. {{ implode(' / ', $site['company']['offices'] ?? []) }}.</span>
    @include('front.components.country-language-switcher', ['locale' => $locale])
  </div>
</footer>
