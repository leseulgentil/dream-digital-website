<?php

namespace App\Http\Controllers\front_pages;

use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class Landing extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'front'];

    return view('content.front-pages.landing-page', [
      'pageConfigs' => $pageConfigs,
      'locale' => $this->contentLocale(),
      'site' => config('dream-digital.site'),
      'home' => config('dream-digital.home'),
      'services' => $this->activeItems(config('dream-digital.services.items', [])),
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
