@props(['locale' => 'fr'])

<aside class="dd-geo-banner" aria-label="Country preference">
  <div class="dd-front-container dd-geo-banner__inner">
    <span>{{ $locale === 'fr' ? 'Version globale affichee' : 'Global version displayed' }}</span>
    <a href="{{ url('/fr') }}">FR</a>
    <a href="{{ url('/en') }}">EN</a>
  </div>
</aside>
