@props(['pageData' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-page-hero dd-page-hero--simple">
  <div class="dd-front-container dd-page-hero__grid">
    <div>
      <p class="dd-eyebrow">{{ $t($pageData['eyebrow'] ?? '') }}</p>
      <h1>{{ $t($pageData['title'] ?? '') }}</h1>
      <p>{{ $t($pageData['lead'] ?? '') }}</p>
    </div>
    <aside class="dd-page-hero__panel" aria-label="Platform status">
      @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Services operationnels' : 'Services operational'])
      <strong>CPaaS / ITSP</strong>
      <span>SMS A2P · Voice · DID · SIP · eSIM</span>
    </aside>
  </div>
</section>
