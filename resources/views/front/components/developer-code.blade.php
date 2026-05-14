@props(['developer', 'liveFeed' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-section dd-developer" id="developer">
  <div class="dd-front-container dd-developer__grid">
    <div>
      <p class="dd-eyebrow">{{ $t($developer['eyebrow'] ?? '') }}</p>
      <h2>{{ $t($developer['title'] ?? '') }}</h2>
      <p>{{ $t($developer['body'] ?? '') }}</p>
      <ul>
        @foreach ($developer['features'] ?? [] as $feature)
          <li><i class="bx bx-check" aria-hidden="true"></i>{{ $t($feature) }}</li>
        @endforeach
      </ul>
      <a class="dd-button dd-button--primary" href="#contact">{{ $locale === 'fr' ? 'Parler a un architecte' : 'Talk to an architect' }}</a>
    </div>

    <div class="dd-developer__console">
      @include('front.components.code-preview', ['locale' => $locale])
      @include('front.components.live-feed', ['items' => $liveFeed, 'locale' => $locale])
    </div>
  </div>
</section>
