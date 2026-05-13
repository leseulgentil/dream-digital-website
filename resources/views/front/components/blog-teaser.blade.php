@props(['articles' => [], 'locale' => 'fr'])

@php
  use Illuminate\Support\Str;

  $imageUrl = function (?string $path): string {
    if (blank($path)) {
      return asset('img/brand/logo-dd-icon.png');
    }

    return Str::startsWith($path, ['http://', 'https://']) ? $path : asset(ltrim($path, '/'));
  };
@endphp

@if(!empty($articles))
  <section class="dd-section dd-blog-teaser">
    <div class="dd-front-container">
      <div class="dd-section-heading">
        <p class="dd-eyebrow">{{ $locale === 'fr' ? 'Guides' : 'Guides' }}</p>
        <h2>{{ $locale === 'fr' ? 'Approfondir ce sujet' : 'Go deeper on this topic' }}</h2>
      </div>

      <div class="dd-blog-teaser__grid">
        @foreach($articles as $article)
          <article>
            <a href="{{ $article['url'] }}" class="dd-blog-teaser__media" aria-label="{{ $article['title'] }}">
              <img src="{{ $imageUrl($article['image'] ?? null) }}" alt="{{ $article['image_alt'] ?? $article['title'] }}" loading="lazy">
            </a>
            <div>
              <p class="dd-eyebrow">{{ $article['eyebrow'] ?? 'Blog' }}</p>
              <h3><a href="{{ $article['url'] }}">{{ $article['title'] }}</a></h3>
              <p>{{ $article['lead'] ?? '' }}</p>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif
