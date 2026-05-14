@props(['locale' => 'fr'])

@php
  $copy = $locale === 'en'
    ? [
        'title'    => 'Cookies',
        'body'     => 'This site uses cookies strictly necessary for its operation (session, theme preference, country detection). No third-party tracking. See our privacy policy for details.',
        'cta_ack'  => 'Got it',
        'cta_more' => 'Privacy policy',
      ]
    : [
        'title'    => 'Cookies',
        'body'     => 'Ce site utilise uniquement des cookies necessaires a son fonctionnement (session, theme, detection pays). Aucun tracking publicitaire tiers. Voir notre politique de confidentialite pour plus de details.',
        'cta_ack'  => 'Compris',
        'cta_more' => 'Politique de confidentialite',
      ];
  $legalUrl = url("/{$locale}/legal/rgpd");
@endphp

<aside class="dd-cookie-banner" id="ddCookieBanner" role="region" aria-label="{{ $copy['title'] }}" hidden>
  <div class="dd-cookie-banner__inner">
    <p class="dd-cookie-banner__body">{{ $copy['body'] }}</p>
    <div class="dd-cookie-banner__actions">
      <a href="{{ $legalUrl }}" class="dd-cookie-banner__link">{{ $copy['cta_more'] }}</a>
      <button type="button" class="dd-cookie-banner__cta" id="ddCookieAck">{{ $copy['cta_ack'] }}</button>
    </div>
  </div>
</aside>
