@props(['services' => [], 'homeCards' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
  $cards = !empty($homeCards) ? $homeCards : collect($services)->map(fn ($service) => [
    'id' => $service['id'] ?? $service['slug'] ?? null,
    'title' => $service['name'] ?? '',
    'excerpt' => $service['tagline'] ?? $service['description'] ?? '',
    'icon' => $service['icon'] ?? 'bx-radio-circle',
    'cta' => [
      'url' => '/{locale}/products/' . ($service['slug'] ?? $service['id']),
      'label' => $service['cta_label'] ?? ['fr' => 'En savoir plus', 'en' => 'Learn more'],
      'external' => false,
    ],
  ])->all();
@endphp

<section class="dd-section dd-services" id="services">
  <div class="dd-front-container">
    <div class="dd-section-heading">
      <p class="dd-eyebrow">Produits</p>
      <h2>{{ $locale === 'fr' ? 'Les services qui portent vos communications' : 'Services that carry your communications' }}</h2>
      <p>{{ $locale === 'fr' ? 'Wholesale, retail, voix, SMS, eSIM et centre de contact : les offres essentielles Dream Digital, lisibles en un coup d\'oeil.' : 'Wholesale, retail, voice, SMS, eSIM and contact center: Dream Digital essentials, easy to scan and act on.' }}</p>
    </div>

    <div class="dd-service-grid">
      @foreach ($cards as $card)
        @php
          $cta = $card['cta'] ?? [];
          $ctaUrl = (string) ($cta['url'] ?? '#');
          $ctaUrl = str_replace('{locale}', $locale, $ctaUrl);
          $isExternal = (bool) ($cta['external'] ?? str_starts_with($ctaUrl, 'http'));
          $href = $isExternal ? $ctaUrl : url($ctaUrl);
          $illustration = $card['illustration'] ?? [];
        @endphp
        <article class="dd-service-card" id="{{ $card['id'] ?? Str::slug($t($card['title'] ?? 'service')) }}" data-dd-home-service-card>
          @if(!empty($illustration['src']))
            <a class="dd-service-card__media" href="{{ $href }}" @if($isExternal) target="_blank" rel="noopener" @endif aria-label="{{ $t($card['title'] ?? '') }}">
              <img
                src="{{ asset(ltrim($illustration['src'], '/')) }}"
                alt="{{ $t($illustration['alt'] ?? $card['title'] ?? '') }}"
                width="{{ $illustration['width'] ?? 960 }}"
                height="{{ $illustration['height'] ?? 640 }}"
                loading="lazy">
            </a>
          @else
            <span class="dd-service-card__icon"><i class="bx {{ $card['icon'] ?? 'bx-radio-circle' }}" aria-hidden="true"></i></span>
          @endif
          <div class="dd-service-card__body">
            <div class="dd-service-card__header">
              <span class="dd-service-card__icon"><i class="bx {{ $card['icon'] ?? 'bx-radio-circle' }}" aria-hidden="true"></i></span>
              <h3>{{ $t($card['title'] ?? '') }}</h3>
            </div>
            <p>{{ $t($card['excerpt'] ?? '') }}</p>
            @if(!empty($card['badges']))
              <div class="dd-service-card__badges">
                @foreach($card['badges'] as $badge)
                  <span>{{ $t($badge) }}</span>
                @endforeach
              </div>
            @endif
            @if(!empty($card['proof_points']))
              <ul class="dd-service-card__proof">
                @foreach($card['proof_points'] as $point)
                  <li><i class="bx bx-check" aria-hidden="true"></i>{{ $t($point) }}</li>
                @endforeach
              </ul>
            @endif
            <a class="dd-service-card__cta" href="{{ $href }}" @if($isExternal) target="_blank" rel="noopener" @endif>
              {{ $t($cta['label'] ?? ['fr' => 'En savoir plus', 'en' => 'Learn more']) }}
              <i class="bx {{ $isExternal ? 'bx-link-external' : 'bx-right-arrow-alt' }}" aria-hidden="true"></i>
            </a>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
