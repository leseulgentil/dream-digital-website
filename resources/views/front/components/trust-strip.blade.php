@props(['signals' => [], 'context' => '', 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-trust" aria-label="Trust signals">
  <div class="dd-front-container">
    <p class="dd-trust__context">{{ $t($context) }}</p>
    <div class="dd-trust__grid">
      @foreach ($signals as $signal)
        <article>
          <i class="bx {{ $signal['icon'] ?? 'bx-check-shield' }}" aria-hidden="true"></i>
          <strong>{{ $signal['value'] ?? '' }}</strong>
          <span>{{ $t($signal['label'] ?? '') }}</span>
        </article>
      @endforeach
    </div>
  </div>
</section>
