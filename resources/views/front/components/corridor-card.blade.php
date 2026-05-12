@props(['corridor', 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
  $quality = max(0, min(5, (int) ($corridor['quality'] ?? 0)));
@endphp

<article class="dd-corridor-card">
  <div class="dd-corridor-card__route">
    <span>{{ $corridor['from'] ?? '' }}</span>
    <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
    <span>{{ $corridor['to'] ?? '' }}</span>
  </div>
  <h3>{{ $t($corridor['title'] ?? '') }}</h3>
  <p>{{ $t($corridor['label'] ?? '') }}</p>
  <div class="dd-corridor-card__quality" role="img" aria-label="Route quality {{ $quality }} sur 5">
    @for($i = 1; $i <= 5; $i++)
      <span class="{{ $i <= $quality ? 'is-active' : '' }}" aria-hidden="true"></span>
    @endfor
  </div>
  <small>{{ $t($corridor['status'] ?? '') }}</small>
</article>
