<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\ServicePrice;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class MarketingPageController extends Controller
{
  public function show(string $page)
  {
    return $this->render($this->contentLocale(), $page);
  }

  public function localized(string $locale, string $page)
  {
    return $this->render($this->normalizeLocale($locale), $page);
  }

  public function product(string $service)
  {
    return $this->renderProduct($this->contentLocale(), $service);
  }

  public function localizedProduct(string $locale, string $service)
  {
    return $this->renderProduct($this->normalizeLocale($locale), $service);
  }

  private function render(string $locale, string $page)
  {
    $pageData = $this->resolveMarketingPage($page, $locale);

    abort_if($pageData === null, 404);

    app()->setLocale($locale);
    session()->put('locale', $locale);

    return view('content.front-pages.marketing-page', $this->viewData($locale, $page, $pageData));
  }

  /**
   * Resolve marketing page content: DB-first (section='marketing'),
   * fallback config dream-digital.pages.pages. Retourne null si
   * aucune source ne couvre le slug.
   */
  private function resolveMarketingPage(string $page, string $locale): ?array
  {
    $dbPage = Schema::hasTable('pages')
      ? Page::published()
          ->where('section', 'marketing')
          ->where('slug', $page)
          ->where('locale', $locale)
          ->whereNull('country_id')
          ->first()
      : null;

    if ($dbPage !== null) {
      $blocks = $dbPage->content_blocks ?? [];
      return [
        'eyebrow' => $blocks['eyebrow'] ?? '',
        'title' => $dbPage->title,
        'seo_title' => $blocks['seo_title'] ?? $dbPage->title,
        'meta_description' => $dbPage->meta_description,
        'meta_image_path' => $dbPage->meta_image_path,
        'lead' => $blocks['lead'] ?? '',
        'image_alt' => $blocks['image_alt'] ?? $dbPage->title,
        'image_credit' => $blocks['image_credit'] ?? null,
        'image_source_url' => $blocks['image_source_url'] ?? null,
        'seo_focus_keywords' => $blocks['seo_focus_keywords'] ?? [],
        'faq' => $blocks['faq'] ?? [],
        'internal_links' => $blocks['internal_links'] ?? [],
        'sections' => $blocks['sections'] ?? [],
        'source' => 'db',
      ];
    }

    $cfg = config("dream-digital.pages.pages.$page");
    if (empty($cfg)) {
      return null;
    }

    // Preserve l'API existante : tableaux nested fr/en sont resolus par
    // le helper $t() cote vue (hero-simple / hero-banner). Pas besoin de
    // pre-resoudre ici cote controller pour rester compatible avec la
    // structure de config historique.
    return array_merge($cfg, ['source' => 'config']);
  }

  private function renderProduct(string $locale, string $service)
  {
    $services = $this->activeItems(config('dream-digital.services.items', []));
    $serviceData = Collection::make($services)->first(
      fn ($item) => in_array($service, [$item['slug'] ?? null, $item['id'] ?? null], true)
    );

    abort_if(empty($serviceData), 404);

    app()->setLocale($locale);
    session()->put('locale', $locale);

    $serviceName = $serviceData['name'] ?? ['fr' => '', 'en' => ''];
    $productSlug = $serviceData['slug'] ?? $serviceData['id'] ?? $service;
    $pageData = $this->resolveProductPage($productSlug, $locale) ?? [
      'eyebrow' => [
        'fr' => 'Produits › ' . ($serviceName['fr'] ?? ''),
        'en' => 'Products › ' . ($serviceName['en'] ?? ''),
      ],
      'title' => $serviceName,
      'lead' => $serviceData['description'] ?? $serviceData['tagline'] ?? '',
      'source' => 'config',
    ];

    return view('content.front-pages.marketing-page', array_merge(
      $this->viewData($locale, 'product', $pageData),
      [
        'service' => $serviceData,
        'productDetail' => $this->productDetailFor($productSlug, $pageData['product_detail'] ?? []),
        'blogGuides' => $this->resolveBlogGuides($locale, $productSlug),
      ]
    ));
  }

  private function resolveProductPage(string $serviceSlug, string $locale): ?array
  {
    $dbPage = Schema::hasTable('pages')
      ? Page::published()
          ->where('section', 'product')
          ->where('slug', $serviceSlug)
          ->where('locale', $locale)
          ->whereNull('country_id')
          ->first()
      : null;

    if ($dbPage === null) {
      return null;
    }

    $blocks = $dbPage->content_blocks ?? [];

    return [
      'eyebrow' => $blocks['eyebrow'] ?? '',
      'title' => $dbPage->title,
      'seo_title' => $blocks['seo_title'] ?? $dbPage->title,
      'meta_description' => $dbPage->meta_description,
      'meta_image_path' => $dbPage->meta_image_path,
      'lead' => $blocks['lead'] ?? '',
      'image_alt' => $blocks['image_alt'] ?? $dbPage->title,
      'image_credit' => $blocks['image_credit'] ?? null,
      'image_source_url' => $blocks['image_source_url'] ?? null,
      'seo_focus_keywords' => $blocks['seo_focus_keywords'] ?? ($blocks['tags'] ?? []),
      'faq' => $blocks['faq'] ?? [],
      'internal_links' => $blocks['internal_links'] ?? [],
      'sections' => $blocks['sections'] ?? [],
      'product_detail' => $blocks['product_detail'] ?? [],
      'source' => 'db',
    ];
  }

  private function viewData(string $locale, string $page, array $pageData): array
  {
    return [
      'pageConfigs' => ['myLayout' => 'front'],
      'locale' => $locale,
      'page' => $page,
      'pageData' => $pageData,
      'site' => config('dream-digital.site'),
      'home' => config('dream-digital.home'),
      'services' => $this->activeItems(config('dream-digital.services.items', [])),
      'industries' => $this->activeItems(config('dream-digital.industries.items', [])),
      'coverage' => config('dream-digital.coverage'),
      'stats' => config('dream-digital.pages.stats', []),
      'features' => config('dream-digital.pages.features', []),
      'corridors' => $this->resolveCorridors($locale),
      'liveFeed' => config('dream-digital.pages.live_feed', []),
    ];
  }

  /**
   * Charge les corridor cards depuis ServicePrice (DB) si disponible,
   * sinon retombe sur config('dream-digital.pages.corridors').
   * Pre-resoud le libelle/status pour la locale demandee.
   */
  private function resolveCorridors(string $locale): array
  {
    if (!Schema::hasTable('service_prices')) {
      return config('dream-digital.pages.corridors', []);
    }

    $records = ServicePrice::published()
      ->whereNotNull('destination_country')
      ->with('country')
      ->orderByDesc('quality')
      ->limit(6)
      ->get();

    if ($records->isEmpty()) {
      return config('dream-digital.pages.corridors', []);
    }

    return $records->map(function (ServicePrice $sp) use ($locale) {
      $fromCode = $sp->country?->code ? strtoupper($sp->country->code) : '--';
      $toCode = strtoupper($sp->destination_country ?? '--');
      $fromName = $locale === 'en' ? ($sp->country?->name_en ?? '') : ($sp->country?->name_fr ?? '');

      return [
        'from' => $fromCode,
        'to' => $toCode,
        'title' => $fromName ? "{$fromName} {$this->arrow($locale)} {$toCode}" : "{$fromCode} {$this->arrow($locale)} {$toCode}",
        'label' => $locale === 'en' ? ($sp->label_en ?? '') : ($sp->label_fr ?? ''),
        'quality' => (int) ($sp->quality ?? 3),
        'status' => $locale === 'en' ? ($sp->status_en ?? '') : ($sp->status_fr ?? ''),
        'source' => 'db',
      ];
    })->all();
  }

  private function arrow(string $locale): string
  {
    return $locale === 'en' ? 'to' : 'vers';
  }

  private function resolveBlogGuides(string $locale, ?string $serviceSlug): array
  {
    if (!Schema::hasTable('pages')) {
      return [];
    }

    $keywords = [
      'sms-a2p' => ['sms', 'otp', 'a2p'],
      'voice' => ['voice', 'voix'],
      'did' => ['did', 'numero', 'number'],
      'sip' => ['sip', 'trunk'],
      'dialo' => ['contact', 'dialo', 'omnichannel', 'omnicanal'],
      'esim' => ['esim'],
    ][$serviceSlug] ?? [];

    $articles = Page::published()
      ->where('section', 'blog')
      ->where('locale', $locale)
      ->whereNull('country_id')
      ->orderByDesc('published_at')
      ->get();

    if ($articles->isEmpty()) {
      return [];
    }

    $matches = $articles
      ->filter(function (Page $page) use ($keywords) {
        if ($keywords === []) {
          return false;
        }

        $blocks = $page->content_blocks ?? [];
        $haystack = mb_strtolower(implode(' ', array_filter([
          $page->slug,
          $page->title,
          $blocks['eyebrow'] ?? '',
          $blocks['lead'] ?? '',
          implode(' ', $blocks['tags'] ?? []),
        ])));

        return collect($keywords)->contains(fn (string $keyword) => str_contains($haystack, $keyword));
      })
      ->sortByDesc(fn (Page $page) => str_contains($page->slug, (string) $serviceSlug) ? 2 : 1)
      ->take(3);

    if ($matches->count() < 3) {
      $matches = $matches
        ->concat($articles->whereNotIn('id', $matches->pluck('id'))->take(3 - $matches->count()))
        ->values();
    }

    return $matches
      ->map(function (Page $page) {
        $blocks = $page->content_blocks ?? [];

        return [
          'title' => $page->title,
          'eyebrow' => $blocks['eyebrow'] ?? 'Blog',
          'lead' => $blocks['lead'] ?? '',
          'image' => $page->meta_image_path,
          'image_alt' => $blocks['image_alt'] ?? $page->title,
          'url' => url('/' . $page->locale . '/blog/' . $page->slug),
        ];
      })
      ->all();
  }

  private function contentLocale(): string
  {
    return $this->normalizeLocale(session()->get('locale', 'fr'));
  }

  private function normalizeLocale(?string $locale): string
  {
    return in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
  }

  private function activeItems(array $items): array
  {
    return Collection::make($items)
      ->filter(fn ($item) => (bool) ($item['active'] ?? true))
      ->sortBy('order')
      ->values()
      ->all();
  }

  private function productDetailFor(string $slug, mixed $cmsDetail): array
  {
    $detail = config('dream-digital.product-pages.items.' . $slug, []);

    if (! is_array($detail)) {
      $detail = [];
    }

    if (! is_array($cmsDetail)) {
      return $detail;
    }

    foreach (['proofs', 'workflow'] as $key) {
      if (array_key_exists($key, $cmsDetail) && is_array($cmsDetail[$key])) {
        $detail[$key] = $cmsDetail[$key];
      }
    }

    return $detail;
  }
}
