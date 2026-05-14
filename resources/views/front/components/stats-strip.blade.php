@props(['stats' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

@if(!empty($stats))
  <section class="dd-stats-strip" aria-label="Dream Digital stats">
    <div class="dd-front-container dd-stats-strip__grid">
      @foreach ($stats as $stat)
        @php
          $value = (int) ($stat['value'] ?? 0);
          $prefix = $stat['prefix'] ?? '';
          $suffix = $stat['suffix'] ?? '';
        @endphp
        <article class="dd-stats-strip__item">
          <strong>
            {{ $prefix }}<span data-dd-count="{{ $value }}" data-dd-suffix="{{ $suffix }}">{{ $value }}{{ $suffix }}</span>
          </strong>
          <span>{{ $t($stat['label'] ?? '') }}</span>
          <p>{{ $t($stat['caption'] ?? '') }}</p>
        </article>
      @endforeach
    </div>
  </section>
@endif
