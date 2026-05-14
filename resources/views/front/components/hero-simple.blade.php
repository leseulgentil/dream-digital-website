@props(['pageData' => [], 'locale' => 'fr'])

@php
  use Illuminate\Support\Str;

  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
  $imagePath = $pageData['meta_image_path'] ?? null;
  $imageUrl = blank($imagePath)
    ? null
    : (Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset(ltrim($imagePath, '/')));
  $imageAlt = $t($pageData['image_alt'] ?? $pageData['title'] ?? 'Dream Digital');
@endphp

<section class="dd-page-hero dd-page-hero--simple">
  <div class="dd-front-container dd-page-hero__grid">
    <div>
      <p class="dd-eyebrow">{{ $t($pageData['eyebrow'] ?? '') }}</p>
      <h1>{{ $t($pageData['title'] ?? '') }}</h1>
      <p>{{ $t($pageData['lead'] ?? '') }}</p>
    </div>

    @if($imageUrl)
      <figure class="dd-page-hero__media">
        <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" loading="eager">
        <figcaption>
          @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Services operationnels' : 'Services operational'])
          <strong>CPaaS / ITSP</strong>
        </figcaption>
      </figure>
    @else
      <aside class="dd-page-hero__panel" aria-label="Platform status">
        @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Services operationnels' : 'Services operational'])
        <strong>CPaaS / ITSP</strong>
        <span>SMS A2P - Voice - DID - SIP - eSIM</span>
      </aside>
    @endif
  </div>
</section>
