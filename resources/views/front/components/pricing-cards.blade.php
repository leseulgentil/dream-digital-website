@props(['plans' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-section dd-pricing" id="pricing">
  <div class="dd-front-container">
    <div class="dd-section-heading">
      <p class="dd-eyebrow">Pricing</p>
      <h2>{{ $locale === 'fr' ? 'Des offres simples pour demarrer, puis scaler' : 'Simple offers to start, then scale' }}</h2>
      <p>{{ $locale === 'fr' ? 'Les tarifs publics restent indicatifs. Les routes premium et les volumes se negocient avec notre equipe.' : 'Public pricing remains indicative. Premium routes and volumes are negotiated with our team.' }}</p>
    </div>

    <div class="dd-pricing-grid">
      @foreach ($plans as $plan)
        <article class="dd-pricing-card @if($plan['highlight'] ?? false) dd-pricing-card--highlight @endif">
          @if($plan['highlight'] ?? false)
            <span class="dd-pricing-card__badge">{{ $locale === 'fr' ? 'Recommande' : 'Recommended' }}</span>
          @endif
          <h3>{{ $t($plan['name'] ?? '') }}</h3>
          <strong>{{ $t($plan['price'] ?? '') }}</strong>
          <p>{{ $t($plan['description'] ?? '') }}</p>
          <ul>
            @foreach ($plan['features'] ?? [] as $feature)
              <li><i class="bx bx-check" aria-hidden="true"></i>{{ $t($feature) }}</li>
            @endforeach
          </ul>
          <a href="{{ url('/' . $locale . '/contact') }}" class="dd-button @if($plan['highlight'] ?? false) dd-button--primary @else dd-button--secondary @endif">{{ $t($plan['cta'] ?? '') }}</a>
        </article>
      @endforeach
    </div>
  </div>
</section>
