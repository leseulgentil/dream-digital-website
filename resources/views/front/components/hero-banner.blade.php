@props(['pageData' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-page-hero dd-page-hero--banner">
  <div class="dd-front-container">
    <p class="dd-eyebrow">{{ $t($pageData['eyebrow'] ?? '') }}</p>
    <h1>{{ $t($pageData['title'] ?? '') }}</h1>
    <p>{{ $t($pageData['lead'] ?? '') }}</p>
  </div>
</section>
