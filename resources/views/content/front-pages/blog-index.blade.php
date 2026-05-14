@extends('layouts/layoutMaster')

@php
  use Illuminate\Support\Str;

  $blogTitle = $locale === 'fr' ? 'Blog telecom B2B et CPaaS' : 'B2B telecom and CPaaS blog';
  $blogDescription = $locale === 'fr'
    ? 'Guides Dream Digital sur SMS A2P, Voice Wholesale, DID, SIP Trunking, centre de contact, eSIM, API telecom et corridors multi-pays.'
    : 'Dream Digital guides about A2P SMS, Voice Wholesale, DID, SIP Trunking, contact center, eSIM, telecom APIs and multi-country corridors.';
  $imageUrl = function (?string $path): string {
    if (blank($path)) {
      return asset('img/brand/logo-dd-icon.png');
    }

    return Str::startsWith($path, ['http://', 'https://']) ? $path : asset(ltrim($path, '/'));
  };
  $baseUrl = rtrim(config('app.url'), '/') . "/{$locale}/blog";
  $featuredImage = $featured ? $imageUrl($featured['meta_image_path'] ?? null) : asset('img/brand/logo-dd-icon.png');
@endphp

@section('title', $blogTitle . ' | Dream Digital')
@section('page-description', $blogDescription)
@section('og-title', $blogTitle . ' | Dream Digital')
@section('og-description', $blogDescription)
@section('og-image', $featuredImage)
@section('canonical-url', $baseUrl)

@php
  $crumbPayload = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => $locale === 'fr' ? 'Accueil' : 'Home', 'item' => rtrim(config('app.url'), '/') . "/{$locale}"],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $baseUrl],
    ],
  ];
@endphp
@section('jsonld-breadcrumb'){!! json_encode($crumbPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('content')
  <main class="dd-home dd-public-page dd-public-page--blog">
    <section class="dd-page-hero dd-page-hero--simple dd-blog-hero">
      <div class="dd-front-container dd-page-hero__grid">
        <div>
          <p class="dd-eyebrow">{{ $locale === 'fr' ? 'Ressources' : 'Resources' }}</p>
          <h1>{{ $blogTitle }}</h1>
          <p>{{ $blogDescription }}</p>
        </div>
        <aside class="dd-page-hero__panel" aria-label="Blog Dream Digital">
          <strong>{{ $articles->total() }}</strong>
          <span>{{ $locale === 'fr' ? 'articles publies' : 'published articles' }}</span>
        </aside>
      </div>
    </section>

    @if($featured)
      <section class="dd-section dd-blog-featured">
        <div class="dd-front-container">
          <a class="dd-blog-featured__card" href="{{ $featured['url'] }}">
            <span class="dd-blog-featured__media">
              <img src="{{ $imageUrl($featured['meta_image_path'] ?? null) }}" alt="{{ $featured['image_alt'] ?? $featured['title'] }}" loading="eager">
            </span>
            <span class="dd-blog-featured__body">
              <span class="dd-eyebrow">{{ $featured['eyebrow'] ?? 'Blog' }}</span>
              <strong>{{ $featured['title'] }}</strong>
              <span>{{ $featured['lead'] }}</span>
              <span class="dd-blog-meta">
                {{ optional($featured['published_at'])->format('Y-m-d') }}
                @if(!empty($featured['reading_time'])) &middot; {{ $featured['reading_time'] }} @endif
              </span>
            </span>
          </a>
        </div>
      </section>
    @endif

    <section class="dd-section dd-blog-list">
      <div class="dd-front-container">
        <div class="dd-section-heading">
          <p class="dd-eyebrow">{{ $locale === 'fr' ? 'Guides SEO' : 'SEO guides' }}</p>
          <h2>{{ $locale === 'fr' ? 'Telecom programmable, explique simplement' : 'Programmable telecom, explained clearly' }}</h2>
        </div>

        <div class="dd-blog-grid">
          @foreach($articles as $article)
            <article class="dd-blog-card">
              <a href="{{ $article['url'] }}" class="dd-blog-card__media" aria-label="{{ $article['title'] }}">
                <img src="{{ $imageUrl($article['meta_image_path'] ?? null) }}" alt="{{ $article['image_alt'] ?? $article['title'] }}" loading="lazy">
              </a>
              <div class="dd-blog-card__body">
                <p class="dd-eyebrow">{{ $article['eyebrow'] ?? 'Blog' }}</p>
                <h3><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3>
                <p>{{ $article['lead'] }}</p>
                <div class="dd-blog-card__footer">
                  <span>{{ optional($article['published_at'])->format('Y-m-d') }}</span>
                  @if(!empty($article['reading_time']))
                    <span>{{ $article['reading_time'] }}</span>
                  @endif
                </div>
              </div>
            </article>
          @endforeach
        </div>

        @if($articles->hasPages())
          <div class="dd-blog-pagination">{{ $articles->links() }}</div>
        @endif
      </div>
    </section>
  </main>
@endsection
