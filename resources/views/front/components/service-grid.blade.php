@props(['services' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-section dd-services" id="services">
  <div class="dd-front-container">
    <div class="dd-section-heading">
      <p class="dd-eyebrow">Produits</p>
      <h2>{{ $locale === 'fr' ? 'Un catalogue telecom sous une seule plateforme' : 'A telecom catalogue on one platform' }}</h2>
      <p>{{ $locale === 'fr' ? 'SMS, voix, numeros, trunks, contact center et eSIM, penses pour des usages B2B exigeants.' : 'SMS, voice, numbers, trunks, contact center and eSIM for demanding B2B workflows.' }}</p>
    </div>

    <div class="dd-service-grid">
      @foreach ($services as $service)
        @php $serviceUrl = url('/' . $locale . '/products/' . ($service['slug'] ?? $service['id'])); @endphp
        <article class="dd-service-card" id="{{ $service['slug'] ?? $service['id'] }}">
          <span class="dd-service-card__icon"><i class="bx {{ $service['icon'] ?? 'bx-radio-circle' }}" aria-hidden="true"></i></span>
          <h3>{{ $t($service['name'] ?? '') }}</h3>
          <p>{{ $t($service['tagline'] ?? $service['description'] ?? '') }}</p>
          <a href="{{ $serviceUrl }}">{{ $t($service['cta_label'] ?? ['fr' => 'En savoir plus', 'en' => 'Learn more']) }} <i class="bx bx-right-arrow-alt" aria-hidden="true"></i></a>
        </article>
      @endforeach
    </div>
  </div>
</section>
