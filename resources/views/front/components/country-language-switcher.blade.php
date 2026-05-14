@props(['locale' => 'fr'])

@php
  $path = trim(request()->path(), '/');
  $withoutLocale = preg_replace('/^(fr|en)(\/|$)/', '', $path);
@endphp

<div class="dd-locale-switcher" aria-label="Language switcher">
  <a class="{{ $locale === 'fr' ? 'is-active' : '' }}" href="{{ url('/fr/' . $withoutLocale) }}">FR</a>
  <a class="{{ $locale === 'en' ? 'is-active' : '' }}" href="{{ url('/en/' . $withoutLocale) }}">EN</a>
</div>
