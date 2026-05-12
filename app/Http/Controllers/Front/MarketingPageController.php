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
        'lead' => $blocks['lead'] ?? '',
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
    $pageData = [
      'eyebrow' => [
        'fr' => 'Produits › ' . ($serviceName['fr'] ?? ''),
        'en' => 'Products › ' . ($serviceName['en'] ?? ''),
      ],
      'title' => $serviceName,
      'lead' => $serviceData['description'] ?? $serviceData['tagline'] ?? '',
    ];

    return view('content.front-pages.marketing-page', array_merge(
      $this->viewData($locale, 'product', $pageData),
      ['service' => $serviceData]
    ));
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
}
