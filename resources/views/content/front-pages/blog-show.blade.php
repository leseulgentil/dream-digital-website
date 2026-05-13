@extends('layouts/layoutMaster')

@php
  use Illuminate\Support\Str;

  $title = $article['seo_title'] ?? $article['title'];
  $description = $article['meta_description'] ?: mb_substr($article['lead'] ?? '', 0, 280);
  $imageUrl = function (?string $path): string {
    if (blank($path)) {
      return asset('img/brand/logo-dd-icon.png');
    }

    return Str::startsWith($path, ['http://', 'https://']) ? $path : asset(ltrim($path, '/'));
  };
  $canonicalUrl = $article['url'];
  $ogImage = $imageUrl($article['meta_image_path'] ?? null);
@endphp

@section('title', $title . ' | Dream Digital')
@section('page-description', $description)
@section('og-type', 'article')
@section('og-title', $title . ' | Dream Digital')
@section('og-description', $description)
@section('og-image', $ogImage)
@section('canonical-url', $canonicalUrl)

@php
  $crumbBase = rtrim(config('app.url'), '/') . "/{$locale}";
  $crumbPayload = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
      ['@type' => 'ListItem', 'position' => 1, 'name' => $locale === 'fr' ? 'Accueil' : 'Home', 'item' => $crumbBase],
      ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => "{$crumbBase}/blog"],
      ['@type' => 'ListItem', 'position' => 3, 'name' => $article['title'], 'item' => $canonicalUrl],
    ],
  ];
  $articlePayload = [
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article['title'],
    'description' => $description,
    'image' => [$ogImage],
    'datePublished' => optional($article['published_at'])->toAtomString(),
    'dateModified' => optional($article['updated_at'])->toAtomString(),
    'author' => ['@type' => 'Organization', 'name' => $article['author'] ?? 'Dream Digital'],
    'publisher' => [
      '@type' => 'Organization',
      'name' => config('dream-digital.site.company.name', 'Dream Digital'),
      'logo' => ['@type' => 'ImageObject', 'url' => asset('img/brand/logo-dd-icon.png')],
    ],
    'mainEntityOfPage' => $canonicalUrl,
  ];
@endphp
@section('jsonld-breadcrumb'){!! json_encode($crumbPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}@endsection
@section('jsonld-extra'){!! json_encode($articlePayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('content')
  <main class="dd-home dd-public-page dd-public-page--article">
    <article>
      <header class="dd-article-hero">
        <div class="dd-front-container dd-article-hero__grid">
          <div class="dd-article-hero__content">
            <a class="dd-eyebrow" href="{{ url("/{$locale}/blog") }}">{{ $article['eyebrow'] ?? 'Blog' }}</a>
            <h1>{{ $article['title'] }}</h1>
            <p>{{ $article['lead'] }}</p>
            <div class="dd-blog-meta">
              <span>{{ $article['author'] ?? 'Dream Digital' }}</span>
              <span>{{ optional($article['published_at'])->format('Y-m-d') }}</span>
              @if(!empty($article['reading_time']))
                <span>{{ $article['reading_time'] }}</span>
              @endif
            </div>
          </div>
          <figure class="dd-article-hero__media">
            <img src="{{ $ogImage }}" alt="{{ $article['image_alt'] ?? $article['title'] }}" loading="eager">
            @if(!empty($article['image_credit']) && !empty($article['image_source_url']))
              <figcaption><a href="{{ $article['image_source_url'] }}" target="_blank" rel="noopener">{{ $article['image_credit'] }}</a></figcaption>
            @endif
          </figure>
        </div>
      </header>

      <section class="dd-section dd-article-body">
        <div class="dd-front-container dd-article-body__grid">
          <aside class="dd-article-aside" aria-label="Article details">
            @if(!empty($article['tags']))
              <div class="dd-article-tags">
                @foreach($article['tags'] as $tag)
                  <span>{{ $tag }}</span>
                @endforeach
              </div>
            @endif
            <a class="dd-button dd-button--secondary" href="{{ url("/{$locale}/contact") }}">
              {{ $locale === 'fr' ? 'Parler a un expert' : 'Talk to an expert' }}
            </a>
          </aside>

          <div class="dd-article-content">
            @foreach($article['sections'] ?? [] as $i => $section)
              <section id="section-{{ $i + 1 }}">
                <h2>{{ $section['heading'] ?? '' }}</h2>
                @include('content.front-pages.partials.section-body', ['section' => $section])
              </section>
            @endforeach
          </div>
        </div>
      </section>
    </article>

    @if($related->isNotEmpty())
      <section class="dd-section dd-blog-list dd-blog-related">
        <div class="dd-front-container">
          <div class="dd-section-heading">
            <p class="dd-eyebrow">{{ $locale === 'fr' ? 'A lire ensuite' : 'Read next' }}</p>
            <h2>{{ $locale === 'fr' ? 'Autres guides Dream Digital' : 'More Dream Digital guides' }}</h2>
          </div>
          <div class="dd-blog-grid">
            @foreach($related as $item)
              <article class="dd-blog-card">
                <a href="{{ $item['url'] }}" class="dd-blog-card__media" aria-label="{{ $item['title'] }}">
                  <img src="{{ $imageUrl($item['meta_image_path'] ?? null) }}" alt="{{ $item['image_alt'] ?? $item['title'] }}" loading="lazy">
                </a>
                <div class="dd-blog-card__body">
                  <p class="dd-eyebrow">{{ $item['eyebrow'] ?? 'Blog' }}</p>
                  <h3><a href="{{ $item['url'] }}">{{ $item['title'] }}</a></h3>
                  <p>{{ $item['lead'] }}</p>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>
    @endif
  </main>
@endsection
