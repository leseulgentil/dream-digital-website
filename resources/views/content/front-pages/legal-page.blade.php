@extends('layouts/layoutMaster')

@php
  $title = $legal['title'] ?? '';
@endphp

@section('title', $title . ' | Dream Digital')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('content')
  <main class="dd-home dd-public-page dd-public-page--legal">
    <section class="dd-page-hero dd-page-hero--simple">
      <div class="dd-front-container dd-page-hero__grid">
        <div>
          <p class="dd-eyebrow">{{ $legal['eyebrow'] ?? '' }}</p>
          <h1>{{ $title }}</h1>
          <p>{{ $legal['lead'] ?? '' }}</p>
        </div>
        <aside class="dd-page-hero__panel" aria-label="Document juridique">
          <strong>{{ $locale === 'fr' ? 'Derniere mise a jour' : 'Last updated' }}</strong>
          <span>{{ $legal['last_updated'] ?? '--' }}</span>
        </aside>
      </div>
    </section>

    <section class="dd-section dd-legal">
      <div class="dd-front-container dd-legal__grid">
        <nav class="dd-legal__toc" aria-label="{{ $locale === 'fr' ? 'Autres documents legaux' : 'Other legal documents' }}">
          <p class="dd-eyebrow">{{ $locale === 'fr' ? 'Documents' : 'Documents' }}</p>
          <ul>
            @foreach($allPages as $slugKey => $other)
              @php $isCurrent = ($other['slug'] ?? $slugKey) === ($legal['slug'] ?? ''); @endphp
              <li>
                <a href="{{ url("/{$locale}/legal/" . ($other['slug'] ?? $slugKey)) }}" class="{{ $isCurrent ? 'is-active' : '' }}">
                  {{ $other['title'] ?? $slugKey }}
                </a>
              </li>
            @endforeach
          </ul>
        </nav>

        <article class="dd-legal__body">
          @foreach($legal['sections'] ?? [] as $i => $section)
            <section id="section-{{ $i + 1 }}">
              <h2>{{ $section['heading'] ?? '' }}</h2>
              @foreach(preg_split("/\r?\n\r?\n/", trim($section['body'] ?? '')) as $paragraph)
                @if(trim($paragraph) !== '')
                  <p>{!! nl2br(e($paragraph)) !!}</p>
                @endif
              @endforeach
            </section>
          @endforeach

          <hr class="dd-legal__divider">

          <p class="dd-legal__footer-note">
            {{ $locale === 'fr' ? 'Document fourni a titre indicatif. Validation juridique en cours avant ouverture publique du site.' : 'Document provided for reference. Final legal review pending before public site indexation.' }}
            <br>
            <a href="mailto:{{ $site['contact']['email_sales'] ?? 'sales@dream-digital.info' }}">{{ $locale === 'fr' ? 'Une question ?' : 'Have a question?' }}</a>
          </p>
        </article>
      </div>
    </section>
  </main>
@endsection
