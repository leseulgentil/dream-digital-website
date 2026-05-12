@props(['coverage', 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-section dd-coverage" id="coverage">
  <div class="dd-front-container dd-coverage__grid">
    <div class="dd-coverage__map" aria-hidden="true">
      @foreach ([['29.4','27.4'], ['50.6','22.9'], ['58.7','33.3'], ['50.9','46.4'], ['78.9','49.2'], ['48.9','47.0'], ['54.2','52.5'], ['53.5','53.0']] as $point)
        <span class="dd-map-point" style="left: {{ $point[0] }}%; top: {{ $point[1] }}%;"></span>
      @endforeach
    </div>

    <div>
      <p class="dd-eyebrow">Coverage</p>
      <h2>{{ config("dream-digital.coverage.section_title.$locale") ?? config('dream-digital.coverage.section_title.fr') }}</h2>
      <p>{{ $t($coverage['global']['description'] ?? '') }}</p>
      <div class="dd-coverage__stats">
        @foreach ($coverage['continents'] ?? [] as $continent)
          <article>
            <strong>{{ $continent['countries'] }}+</strong>
            <span>{{ $t($continent['name'] ?? '') }}</span>
          </article>
        @endforeach
      </div>
    </div>
  </div>
</section>
