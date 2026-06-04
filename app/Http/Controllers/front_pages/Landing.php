<?php

namespace App\Http\Controllers\front_pages;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class Landing extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'front'];
    $locale = $this->contentLocale();
    $site = config('dream-digital.site');
    $home = config('dream-digital.home');
    $homePage = $this->homeCmsPage($locale);

    if ($homePage) {
      [$site, $home] = $this->applyHomeCmsPage($homePage, $site, $home, $locale);
    }

    return view('content.front-pages.landing-page', [
      'pageConfigs' => $pageConfigs,
      'locale' => $locale,
      'site' => $site,
      'home' => $home,
      'homePage' => $this->homePageViewData($homePage),
      'services' => $this->activeItems(config('dream-digital.services.items', [])),
      'homeServiceCards' => $this->activeItems(config('dream-digital.services.home_cards', [])),
      'industries' => $this->activeItems(config('dream-digital.industries.items', [])),
      'trustSignals' => $this->orderedItems(config('dream-digital.trust-signals.items', [])),
      'coverage' => config('dream-digital.coverage'),
      'stats' => config('dream-digital.pages.stats', []),
      'corridors' => config('dream-digital.pages.corridors', []),
      'liveFeed' => config('dream-digital.pages.live_feed', []),
    ]);
  }

  private function contentLocale(): string
  {
    $locale = request()->route('locale') ?? session()->get('locale', 'fr');

    if (in_array($locale, ['fr', 'en'], true)) {
      session()->put('locale', $locale);
      app()->setLocale($locale);
    }

    return in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
  }

  private function homeCmsPage(string $locale): ?Page
  {
    if (! Schema::hasTable('pages')) {
      return null;
    }

    return Page::published()
      ->where('section', 'home')
      ->where('slug', 'home')
      ->where('locale', $locale)
      ->whereNull('country_id')
      ->first();
  }

  private function applyHomeCmsPage(Page $page, array $site, array $home, string $locale): array
  {
    $blocks = $page->content_blocks ?? [];

    data_set($home, "hero.headline.{$locale}", $page->title);

    if (!empty($blocks['eyebrow'])) {
      data_set($site, "tagline.{$locale}", $blocks['eyebrow']);
    }

    if (!empty($blocks['lead'])) {
      data_set($site, "sub_headline.{$locale}", $blocks['lead']);
    }

    return [$site, $home];
  }

  private function homePageViewData(?Page $page): ?array
  {
    if (!$page) {
      return null;
    }

    $blocks = $page->content_blocks ?? [];

    return [
      'title' => $page->title,
      'eyebrow' => $blocks['eyebrow'] ?? null,
      'lead' => $blocks['lead'] ?? null,
      'sections' => $blocks['sections'] ?? [],
      'updated_at' => $page->updated_at,
    ];
  }

  private function activeItems(array $items): array
  {
    return $this->orderedItems(
      array_filter($items, fn ($item) => (bool) ($item['active'] ?? true))
    );
  }

  private function orderedItems(array $items): array
  {
    return Collection::make($items)
      ->sortBy('order')
      ->values()
      ->all();
  }
}
