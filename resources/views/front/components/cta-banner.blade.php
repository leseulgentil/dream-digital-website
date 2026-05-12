@props(['site', 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
  $email = $site['contact']['email_sales'] ?? 'sales@dream-digital.info';
@endphp

<section class="dd-cta" id="contact">
  <div class="dd-front-container dd-cta__box">
    <div>
      <p class="dd-eyebrow">{{ $locale === 'fr' ? 'Passer a l action' : 'Next step' }}</p>
      <h2>{{ $t($site['transition_cta']['title'] ?? '') }}</h2>
      <p>{{ $t($site['transition_cta']['text'] ?? '') }}</p>
    </div>
    <div class="dd-cta__actions">
      <a class="dd-button dd-button--primary" href="mailto:{{ $email }}">{{ $t($site['transition_cta']['cta_primary'] ?? '') }}</a>
      <a class="dd-button dd-button--ghost" href="mailto:{{ $email }}?subject=Etude tarifaire Dream Digital">{{ $t($site['transition_cta']['cta_secondary'] ?? '') }}</a>
    </div>
  </div>
</section>
