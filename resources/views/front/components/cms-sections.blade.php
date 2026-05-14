@props(['sections' => [], 'locale' => 'fr', 'keywords' => [], 'links' => []])

<section class="dd-section dd-article-body dd-cms-sections">
  <div class="dd-front-container dd-article-body__grid">
    <aside class="dd-article-aside" aria-label="{{ $locale === 'fr' ? 'Mots cles SEO' : 'SEO keywords' }}">
      @if(!empty($keywords))
        <div class="dd-article-tags">
          @foreach($keywords as $keyword)
            <span>{{ $keyword }}</span>
          @endforeach
        </div>
      @endif
      @if(!empty($links))
        <nav class="dd-article-tags" aria-label="{{ $locale === 'fr' ? 'Liens utiles' : 'Useful links' }}">
          @foreach($links as $link)
            <a href="{{ url($link['url'] ?? '/') }}">{{ $link['label'] ?? $link['url'] ?? '' }}</a>
          @endforeach
        </nav>
      @endif
      <a class="dd-button dd-button--secondary" href="{{ url("/{$locale}/contact") }}">
        {{ $locale === 'fr' ? 'Discuter du besoin' : 'Discuss the need' }}
      </a>
    </aside>

    <div class="dd-article-content">
      @foreach($sections as $index => $section)
        <section id="cms-section-{{ $index + 1 }}">
          <h2>{{ $section['heading'] ?? '' }}</h2>
          @include('content.front-pages.partials.section-body', ['section' => $section])
        </section>
      @endforeach
    </div>
  </div>
</section>
