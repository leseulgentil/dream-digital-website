@props(['homePage' => null, 'locale' => 'fr'])

@php
  $sections = $homePage['sections'] ?? [];
@endphp

@if(!empty($sections))
  <section class="dd-section dd-home-cms" id="home-content">
    <div class="dd-front-container dd-home-cms__grid">
      <div class="dd-section-heading">
        @if(!empty($homePage['eyebrow']))
          <p class="dd-eyebrow">{{ $homePage['eyebrow'] }}</p>
        @endif
        <h2>{{ $locale === 'fr' ? 'A la une' : 'Featured' }}</h2>
        @if(!empty($homePage['lead']))
          <p>{{ $homePage['lead'] }}</p>
        @endif
      </div>

      <div class="dd-home-cms__body">
        @foreach($sections as $i => $section)
          <article id="home-section-{{ $i + 1 }}">
            @if(!empty($section['heading']))
              <h3>{{ $section['heading'] }}</h3>
            @endif
            @include('content.front-pages.partials.section-body', ['section' => $section])
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
