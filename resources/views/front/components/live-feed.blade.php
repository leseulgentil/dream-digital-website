@props(['items' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

@if(!empty($items))
  <div class="dd-live-feed">
    <div class="dd-live-feed__header">
      @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Flux live simule' : 'Simulated live feed'])
      <span>DLR / routing</span>
    </div>
    @foreach ($items as $item)
      <article class="dd-live-feed__item">
        <time>{{ $item['time'] ?? '' }}</time>
        <div>
          <strong>{{ $t($item['label'] ?? '') }}</strong>
          <span>{{ $t($item['detail'] ?? '') }}</span>
        </div>
        @include('front.components.signal-indicator', ['status' => $item['status'] ?? 'success'])
      </article>
    @endforeach
  </div>
@endif
