@props(['industries' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-section dd-industries" id="solutions">
  <div class="dd-front-container">
    <div class="dd-section-heading">
      <p class="dd-eyebrow">Solutions</p>
      <h2>{{ config("dream-digital.industries.section_title.$locale") ?? config('dream-digital.industries.section_title.fr') }}</h2>
      <p>{{ config("dream-digital.industries.section_subtitle.$locale") ?? config('dream-digital.industries.section_subtitle.fr') }}</p>
    </div>

    <div class="dd-industry-grid">
      @foreach ($industries as $industry)
        <article>
          <i class="bx {{ $industry['icon'] ?? 'bx-buildings' }}" aria-hidden="true"></i>
          <h3>{{ $t($industry['name'] ?? '') }}</h3>
          <p>{{ $t($industry['description'] ?? '') }}</p>
        </article>
      @endforeach
    </div>
  </div>
</section>
