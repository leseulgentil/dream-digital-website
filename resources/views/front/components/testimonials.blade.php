@props(['items' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

@if(!empty($items))
  <section class="dd-section dd-testimonials">
    <div class="dd-front-container">
      <div class="dd-section-heading">
        <p class="dd-eyebrow">Proof</p>
        <h2>{{ $locale === 'fr' ? 'Retours clients valides' : 'Validated customer proof' }}</h2>
      </div>
      <div class="dd-testimonials__grid">
        @foreach ($items as $item)
          <article>
            <p>{{ $t($item['quote'] ?? '') }}</p>
            <strong>{{ $t($item['name'] ?? '') }}</strong>
            <span>{{ $t($item['role'] ?? '') }}</span>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
