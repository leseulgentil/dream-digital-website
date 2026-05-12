@props(['site', 'home', 'coverage', 'liveFeed' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
  $offices = collect($coverage['offices'] ?? [])->sortBy('order')->values();
@endphp

<section class="dd-hero" id="dd-hero" aria-label="Dream Digital">
  <div class="dd-front-container dd-hero__grid">
    <div class="dd-hero__content">
      <div class="dd-hero__status">
        @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Reseau supervise 24/7' : 'Network monitored 24/7'])
        <span>{{ $locale === 'fr' ? 'Carrier-grade CPaaS' : 'Carrier-grade CPaaS' }}</span>
      </div>
      <p class="dd-eyebrow">{{ $t($site['tagline'] ?? '') }}</p>
      <h1>{{ $t($home['hero']['headline'] ?? '') }}</h1>
      <p class="dd-hero__lead">{{ $t($site['sub_headline'] ?? '') }}</p>

      <div class="dd-hero__actions">
        <a href="#contact" class="dd-button dd-button--primary">{{ $t($site['transition_cta']['cta_primary'] ?? '') }}</a>
        <a href="#developer" class="dd-button dd-button--ghost">
          {{ $t($home['hero']['cta_secondary'] ?? '') }}
          <span>{{ $locale === 'fr' ? 'bientot' : 'soon' }}</span>
        </a>
      </div>

      <ul class="dd-hero__bullets">
        @foreach ($home['hero']['bullets'] ?? [] as $bullet)
          <li><i class="bx bx-check" aria-hidden="true"></i>{{ $t($bullet) }}</li>
        @endforeach
      </ul>
    </div>

    <div class="dd-hero__visual">
      <div class="swiper dd-hero-slider">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="dd-map-card">
              <div class="dd-map-card__grid" aria-hidden="true">
                @foreach ([['29.4','27.4'], ['50.6','22.9'], ['58.7','33.3'], ['50.9','46.4'], ['78.9','49.2'], ['48.9','47.0'], ['54.2','52.5'], ['53.5','53.0']] as $point)
                  <span class="dd-map-point" style="left: {{ $point[0] }}%; top: {{ $point[1] }}%;"></span>
                @endforeach
              </div>
              <div class="dd-map-card__stat">
                <strong>{{ $coverage['global']['countries_count'] ?? '200' }}+</strong>
                <span>{{ $t($coverage['global']['countries_label'] ?? '') }}</span>
              </div>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="dd-terminal-card">
              <div class="dd-terminal-card__bar">
                <span></span><span></span><span></span>
                <strong>curl POST /v1/sms/send</strong>
              </div>
              <pre id="dd-terminal-code" class="dd-terminal-card__code" data-typing-target aria-label="Exemple API SMS Dream Digital"></pre>
            </div>
          </div>

          <div class="swiper-slide">
            <div class="dd-dashboard-card" data-dashboard-target>
              <div class="dd-dashboard-card__header">
                @include('front.components.signal-indicator', ['label' => $locale === 'fr' ? 'Console live' : 'Live console'])
                <span>Dream Digital Ops</span>
              </div>
              <div class="dd-dashboard-card__metrics">
                @foreach ([['Uptime SLA', '99,95%', '+0,02%'], ['Delivery rate', '99,2%', '+0,3 pts'], ['Latency avg', '1,8s', '-0,2s']] as $metric)
                  <article>
                    <span>{{ $metric[0] }}</span>
                    <strong>{{ $metric[1] }}</strong>
                    <small>{{ $metric[2] }}</small>
                  </article>
                @endforeach
              </div>
              @include('front.components.live-feed', ['items' => $liveFeed, 'locale' => $locale])
            </div>
          </div>

          <div class="swiper-slide">
            <div class="dd-office-grid" data-offices-target>
              @foreach ($offices as $office)
                @php
                  $country = $t($office['country'] ?? '');
                  $description = $t($office['description'] ?? '');
                @endphp
                <article class="dd-office-card @if($office['is_hq'] ?? false) dd-office-card--hq @endif">
                  <span class="dd-office-card__flag">
                    @include('front.components.flag', ['id' => $office['id'] ?? '', 'label' => $country])
                  </span>
                  <strong>{{ $country }}</strong>
                  <span>{{ $office['city'] ?? '' }} @if(!empty($office['iso_alpha3'])) / {{ $office['iso_alpha3'] }} @endif</span>
                  <p>{{ $description }}</p>
                  @if($office['coming_soon'] ?? false)
                    <em>{{ $locale === 'fr' ? 'Bientot' : 'Soon' }}</em>
                  @endif
                </article>
              @endforeach
            </div>
          </div>
        </div>
        <div class="swiper-pagination dd-hero-pagination"></div>
      </div>
    </div>
  </div>
</section>
