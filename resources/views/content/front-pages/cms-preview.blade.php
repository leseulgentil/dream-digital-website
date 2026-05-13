@extends('layouts/layoutMaster')

@php
  $title = $page['title'] ?? 'Preview CMS';
  $lead = $page['lead'] ?? '';
  $description = $lead !== '' ? mb_substr($lead, 0, 280) : $title;
@endphp

@section('title', $title . ' | Dream Digital')
@section('page-description', $description)
@section('og-title', $title . ' | Dream Digital')
@section('og-description', $description)

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('content')
  <main class="dd-home dd-public-page dd-public-page--cms-preview">
    <section class="dd-page-hero dd-page-hero--simple">
      <div class="dd-front-container dd-page-hero__grid">
        <div>
          <p class="dd-eyebrow">{{ $page['eyebrow'] ?? 'Preview CMS' }}</p>
          <h1>{{ $title }}</h1>
          <p>{{ $lead }}</p>
        </div>
        <aside class="dd-page-hero__panel" aria-label="Preview CMS">
          <strong>Preview</strong>
          <span>{{ $locale ?? 'fr' }}</span>
        </aside>
      </div>
    </section>

    <section class="dd-section dd-legal">
      <div class="dd-front-container">
        <article class="dd-legal__body">
          @foreach($page['sections'] ?? [] as $i => $section)
            <section id="section-{{ $i + 1 }}">
              <h2>{{ $section['heading'] ?? '' }}</h2>
              @foreach(preg_split("/\r?\n\r?\n/", trim($section['body'] ?? '')) as $paragraph)
                @if(trim($paragraph) !== '')
                  <p>{!! nl2br(e($paragraph)) !!}</p>
                @endif
              @endforeach
            </section>
          @endforeach
        </article>
      </div>
    </section>
  </main>
@endsection
