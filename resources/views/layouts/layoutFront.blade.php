@php
  $configData = Helper::appClasses();
  $isFront = true;
@endphp

@php
  $cookieLocale = request()->route('locale') ?? session()->get('locale', 'fr');
  $cookieLocale = in_array($cookieLocale, ['fr', 'en'], true) ? $cookieLocale : 'fr';
@endphp

@section('layoutContent')
  @extends('layouts/commonMaster')

  @include('layouts/sections/navbar/navbar-front')

  <!-- Sections:Start -->
  @yield('content')
  <!-- / Sections:End -->

  @include('layouts/sections/footer/footer-front')
  @include('front.components.cookie-banner', ['locale' => $cookieLocale])
@endsection
