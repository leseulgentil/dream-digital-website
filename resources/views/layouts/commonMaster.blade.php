<!DOCTYPE html>
@php
  use Illuminate\Support\Str;
  use App\Helpers\Helpers;

  $menuFixed =
      $configData['layout'] === 'vertical'
          ? $menuFixed ?? ''
          : ($configData['layout'] === 'front'
              ? ''
              : $configData['headerType']);
  $navbarType =
      $configData['layout'] === 'vertical'
          ? $configData['navbarType']
          : ($configData['layout'] === 'front'
              ? 'dd-layout-navbar-fixed'
              : '');
  $isFront = ($isFront ?? '') == true ? 'Front' : '';
  $contentLayout = isset($container) ? ($container === 'container-xxl' ? 'dd-layout-compact' : 'dd-layout-wide') : '';

  // Get skin name from configData - only applies to admin layouts
  $isAdminLayout = !Str::contains($configData['layout'] ?? '', 'front');
  $skinName = $isAdminLayout ? $configData['skinName'] ?? 'default' : 'default';

  // Get semiDark value from configData - only applies to admin layouts
  $semiDarkEnabled = $isAdminLayout && filter_var($configData['semiDark'] ?? false, FILTER_VALIDATE_BOOLEAN);

  // Generate primary color CSS if color is set
  $primaryColorCSS = '';
  if (isset($configData['color']) && $configData['color']) {
      $primaryColorCSS = Helpers::generatePrimaryColorCSS($configData['color']);
  }

@endphp

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}"
  class="{{ $navbarType ?? '' }} {{ $contentLayout ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }}"
  dir="{{ $configData['textDirection'] }}" data-skin="{{ $skinName }}" data-assets-path="{{ asset('/assets') . '/' }}"
  data-base-url="{{ url('/') }}" data-framework="laravel" data-template="{{ $configData['layout'] }}-menu-template"
  data-bs-theme="{{ $configData['theme'] }}" @if ($isAdminLayout && $semiDarkEnabled) data-semidark-menu="true" @endif>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  {{-- Anti-FOUC: apply user theme preference (Q13) before any CSS loads --}}
  <script>
    (function () {
      try {
        var stored = localStorage.getItem('dd-theme');
        if (stored !== 'light' && stored !== 'dark' && stored !== 'system') stored = 'system';
        var resolved = stored === 'system'
          ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
          : stored;
        document.documentElement.setAttribute('data-bs-theme', resolved);
      } catch (e) { /* localStorage may be disabled — fall back to server-rendered data-bs-theme */ }
    })();
  </script>

  {{-- Google Fonts (S3) — Dream Digital typography pair: Inter + JetBrains Mono --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

  @php
    $ddDescDefault = config('dream-digital.site.meta.description_default');
    $ddDescLocale = app()->getLocale() === 'en' ? 'en' : 'fr';
    $ddDescResolved = is_array($ddDescDefault)
      ? ($ddDescDefault[$ddDescLocale] ?? reset($ddDescDefault))
      : ($ddDescDefault ?? config('variables.templateDescription', ''));
    $ddTitleDefault = config('dream-digital.site.meta.title_default', config('variables.templateName', 'Dream Digital'));
    $ddOgImageDefault = config('dream-digital.site.meta.og_image') ?: asset('img/brand/logo-dd-icon.png');
    $ddCanonicalDefault = request()->url();
    $ddSiteName = config('dream-digital.site.company.name', 'Dream Digital');
  @endphp
  <title>@yield('title', $ddTitleDefault)</title>
  <meta name="description" content="@yield('page-description', $ddDescResolved)" />
  <meta name="keywords" content="@yield('page-keywords', 'cpaas, itsp, voice api, sms api, esim, programmable telecom, dream digital')" />
  <meta name="robots" content="{{ filter_var(env('DD_PUBLIC_INDEXABLE', false), FILTER_VALIDATE_BOOLEAN) ? 'index, follow' : 'noindex, nofollow' }}" />
  <link rel="canonical" href="@yield('canonical-url', $ddCanonicalDefault)" />

  @php
    // hreflang : detecte la locale dans le path et genere les alternates
    // FR / EN / x-default pour les pages publiques /{locale}/* et /{locale}.
    $hreflangPath = request()->path();
    $hreflangSegments = explode('/', $hreflangPath, 2);
    $hreflangFirst = $hreflangSegments[0] ?? '';
    $hreflangIsLocalized = in_array($hreflangFirst, ['fr', 'en'], true);
    $hreflangRest = $hreflangSegments[1] ?? '';
    $hreflangBase = rtrim(config('app.url'), '/');
    $hreflangFr = $hreflangIsLocalized
      ? ($hreflangRest === '' ? "{$hreflangBase}/fr" : "{$hreflangBase}/fr/{$hreflangRest}")
      : null;
    $hreflangEn = $hreflangIsLocalized
      ? ($hreflangRest === '' ? "{$hreflangBase}/en" : "{$hreflangBase}/en/{$hreflangRest}")
      : null;
  @endphp
  @if($hreflangIsLocalized)
    <link rel="alternate" hreflang="fr" href="{{ $hreflangFr }}" />
    <link rel="alternate" hreflang="en" href="{{ $hreflangEn }}" />
    <link rel="alternate" hreflang="x-default" href="{{ $hreflangFr }}" />
  @endif

  <!-- OpenGraph -->
  <meta property="og:type" content="@yield('og-type', 'website')" />
  <meta property="og:site_name" content="{{ $ddSiteName }}" />
  <meta property="og:title" content="@yield('og-title', $ddTitleDefault)" />
  <meta property="og:description" content="@yield('og-description', $ddDescResolved)" />
  <meta property="og:url" content="@yield('og-url', $ddCanonicalDefault)" />
  <meta property="og:image" content="@yield('og-image', $ddOgImageDefault)" />
  <meta property="og:locale" content="{{ app()->getLocale() === 'en' ? 'en_US' : 'fr_FR' }}" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="@yield('twitter-card', 'summary_large_image')" />
  <meta name="twitter:title" content="@yield('og-title', $ddTitleDefault)" />
  <meta name="twitter:description" content="@yield('og-description', $ddDescResolved)" />
  <meta name="twitter:image" content="@yield('og-image', $ddOgImageDefault)" />

  <!-- laravel CRUD token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  @if($hreflangIsLocalized)
    @php
      $orgPayload = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $ddSiteName,
        'legalName' => config('dream-digital.site.company.legal_name') ?: $ddSiteName,
        'url' => $hreflangBase,
        'logo' => asset('img/brand/logo-dd-icon.png'),
        'description' => $ddDescResolved,
        'sameAs' => array_values(array_filter([
          config('dream-digital.site.social.linkedin'),
          config('dream-digital.site.social.twitter'),
          config('dream-digital.site.social.github'),
        ])),
        'contactPoint' => [[
          '@type' => 'ContactPoint',
          'email' => config('dream-digital.site.contact.email_sales', 'sales@dream-digital.info'),
          'contactType' => 'sales',
          'availableLanguage' => ['fr', 'en'],
        ]],
        'address' => array_values(array_filter(array_map(function ($office) {
          if (!is_string($office)) return null;
          preg_match('/^([^(]+)\s*\(([^)]+)\)/', $office, $matches);
          if (count($matches) < 3) return null;
          $countryMap = ['RDC' => 'CD', 'CI' => 'CI', 'CG' => 'CG', 'KENYA' => 'KE', 'FRANCE' => 'FR'];
          $isoCountry = $countryMap[strtoupper(trim($matches[2]))] ?? trim($matches[2]);
          return [
            '@type' => 'PostalAddress',
            'addressLocality' => trim($matches[1]),
            'addressCountry' => $isoCountry,
          ];
        }, config('dream-digital.site.company.offices', [])))),
      ];
      if (empty($orgPayload['sameAs'])) unset($orgPayload['sameAs']);
    @endphp
    <script type="application/ld+json">{!! json_encode($orgPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @hasSection('jsonld-breadcrumb')
      <script type="application/ld+json">@yield('jsonld-breadcrumb')</script>
    @endif
    @hasSection('jsonld-extra')
      <script type="application/ld+json">@yield('jsonld-extra')</script>
    @endif
  @endif
  <!-- Favicon — Dream Digital (Brand Kit v1.2, S5) -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('img/brand/logo-dd-icon.svg') }}" />
  <link rel="alternate icon" type="image/png" href="{{ asset('img/brand/logo-dd-icon.png') }}" />

  <!-- Include Styles -->
  <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/styles' . $isFront)

  @if (
      $primaryColorCSS &&
          (config('custom.custom.primaryColor') ||
              isset($_COOKIE['admin-primaryColor']) ||
              isset($_COOKIE['front-primaryColor'])))
    <!-- Primary Color Style -->
    <style id="primary-color-style">
      {!! $primaryColorCSS !!}
    </style>
  @endif

  <!-- Include Scripts for customizer, helper, analytics, config -->
  <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scriptsIncludes' . $isFront)
</head>

<body>
  <!-- Layout Content -->
  @yield('layoutContent')
  <!--/ Layout Content -->

  

  <!-- Include Scripts -->
  <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
  @include('layouts/sections/scripts' . $isFront)
</body>

</html>
