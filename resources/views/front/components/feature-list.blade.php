@props(['features' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

@if(!empty($features))
  <div class="dd-feature-list">
    @foreach ($features as $feature)
      <article class="dd-feature-list__item">
        <i class="bx {{ $feature['icon'] ?? 'bx-check-shield' }}" aria-hidden="true"></i>
        <div>
          <h3>{{ $t($feature['title'] ?? '') }}</h3>
          <p>{{ $t($feature['body'] ?? '') }}</p>
        </div>
      </article>
    @endforeach
  </div>
@endif
