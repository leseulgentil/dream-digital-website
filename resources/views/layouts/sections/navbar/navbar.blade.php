@php
    $containerNav = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';
@endphp

<!-- Navbar -->
@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
    <nav class="dd-layout-navbar {{ $containerNav }} {{ $navbarDetached }} navbar navbar-expand-xl align-items-center dd-bg-navbar-theme"
        id="dd-layout-navbar">
        @include('layouts/sections/navbar/navbar-partial')
    </nav>
@else
    <nav class="dd-layout-navbar navbar navbar-expand-xl align-items-center dd-bg-navbar-theme" id="dd-layout-navbar">
        <div class="{{ $containerNav }}">@include('layouts/sections/navbar/navbar-partial')</div>
    </nav>
@endif
<!-- / Navbar -->
