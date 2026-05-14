@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', config('dream-digital.site.meta.title_default', 'Dream Digital'))

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/swiper/swiper.scss'])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/swiper/swiper.js'])
@endsection

@section('page-script')
  @vite(['resources/assets/js/front-page-landing.js'])
@endsection

@section('content')
  <main class="dd-home">
    @include('front.components.hero-split', [
      'site' => $site,
      'home' => $home,
      'coverage' => $coverage,
      'liveFeed' => $liveFeed ?? [],
      'locale' => $locale,
    ])

    @include('front.components.trust-strip', [
      'signals' => $trustSignals,
      'context' => $home['trust_context'] ?? '',
      'locale' => $locale,
    ])

    @include('front.components.stats-strip', [
      'stats' => $stats ?? [],
      'locale' => $locale,
    ])

    @include('front.components.service-grid', [
      'services' => $services,
      'locale' => $locale,
    ])

    @include('front.components.developer-code', [
      'developer' => $home['developer'] ?? [],
      'liveFeed' => $liveFeed ?? [],
      'locale' => $locale,
    ])

    @include('front.components.industry-grid', [
      'industries' => $industries,
      'locale' => $locale,
    ])

    @include('front.components.coverage-map', [
      'coverage' => $coverage,
      'locale' => $locale,
    ])

    @include('front.components.pricing-cards', [
      'plans' => $home['pricing'] ?? [],
      'locale' => $locale,
    ])

    @include('front.components.faq-accordion', [
      'items' => $home['faq'] ?? [],
      'locale' => $locale,
    ])

    @include('front.components.cta-banner', [
      'site' => $site,
      'locale' => $locale,
    ])
  </main>
@endsection
