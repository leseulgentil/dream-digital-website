<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

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
    $pageData = config("dream-digital.pages.pages.$page");

    abort_if(empty($pageData), 404);

    app()->setLocale($locale);
    session()->put('locale', $locale);

    return view('content.front-pages.marketing-page', $this->viewData($locale, $page, $pageData));
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
      'corridors' => config('dream-digital.pages.corridors', []),
      'liveFeed' => config('dream-digital.pages.live_feed', []),
    ];
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
