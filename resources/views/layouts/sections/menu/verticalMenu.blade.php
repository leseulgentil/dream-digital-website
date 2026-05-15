@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
@endphp

<aside id="dd-layout-menu" class="dd-layout-menu dd-menu-vertical menu dd-bg-menu-theme"
    @foreach ($configData['menuAttributes'] as $attribute => $value)
  {{ $attribute }}="{{ $value }}" @endforeach>

  <!-- ! Hide app brand if navbar-full -->
  @if (!isset($navbarFull))
  <div class="dd-app-brand demo">
    <a href="{{ route('admin.dashboard') }}" class="dd-app-brand-link">
      <span class="dd-app-brand-logo demo">@include('_partials.macros')</span>
      <span class="dd-app-brand-text demo dd-menu-text fw-bold ms-2">{{ config('variables.templateName') }}</span>
    </a>

    <button type="button" class="dd-layout-menu-toggle dd-menu-link text-large ms-auto border-0 bg-transparent" aria-label="Fermer le menu admin">
      <i class="icon-base bx bx-chevron-left"></i>
    </button>
  </div>
  @endif

  <div class="dd-menu-inner-shadow"></div>

  <ul class="dd-menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)
    {{-- adding active and open class if child is active --}}

    {{-- menu headers --}}
    @if (isset($menu->menuHeader))
    <li class="dd-menu-header small">
      <span class="dd-menu-header-text">{{ __($menu->menuHeader) }}</span>
    </li>
    @else
    @continue((isset($menu->hidden) && $menu->hidden) || (isset($menu->enabled) && ! $menu->enabled))
    @continue(isset($menu->ability) && ! (auth()->user()?->{$menu->ability}() ?? false))
    @continue(isset($menu->permission) && ! (auth()->user()?->hasPermission($menu->permission) ?? false))

    {{-- active menu method --}}
    @php
    $activeClass = null;
    $currentRouteName = Route::currentRouteName();
    $menuUrl = isset($menu->url) ? (string) $menu->url : '';
    $menuUrlPath = trim((string) (parse_url($menuUrl, PHP_URL_PATH) ?? ''), '/');
    $menuUrlQuery = (string) (parse_url($menuUrl, PHP_URL_QUERY) ?? '');
    $menuQueryParams = [];
    parse_str($menuUrlQuery, $menuQueryParams);
    $menuHasQuery = $menuQueryParams !== [];
    $menuQueryMatches = collect($menuQueryParams)->every(
      fn ($value, $key) => (string) request()->query($key) === (string) $value
    );
    $currentPath = trim(request()->path(), '/');
    $blogFilterIsActive = $currentPath === 'admin/pages' && request()->query('section') === 'blog';
    $menuSlug = $menu->slug ?? null;
    $routeName = (string) $currentRouteName;
    $slugMatches = is_string($menuSlug) && (
      $routeName === $menuSlug
      || str_starts_with($routeName, $menuSlug . '.')
    );

    if ($menuHasQuery) {
      if ($menuUrlPath === $currentPath && $menuQueryMatches) {
        $activeClass = 'active';
      }
    } elseif (! $blogFilterIsActive && $slugMatches) {
      $activeClass = 'active';
    } elseif (isset($menu->submenu)) {
      if (is_array($menuSlug)) {
        foreach ($menuSlug as $slug) {
          if (str_starts_with($routeName, (string) $slug)) {
            $activeClass = 'active open';
          }
        }
      } elseif (is_string($menuSlug) && str_starts_with($routeName, $menuSlug)) {
        $activeClass = 'active open';
      }
    }
    @endphp

    {{-- main menu --}}
    <li class="dd-menu-item {{ $activeClass }}">
      <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
        class="{{ isset($menu->submenu) ? 'dd-menu-link dd-menu-toggle' : 'dd-menu-link' }}" @if (isset($menu->target) and
        !empty($menu->target)) target="_blank" @endif>
        @isset($menu->icon)
        <i class="{{ $menu->icon }}"></i>
        @endisset
        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
        @isset($menu->badge)
        <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
        @endisset
      </a>

      {{-- submenu --}}
      @isset($menu->submenu)
      @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
      @endisset
    </li>
    @endif
    @endforeach
  </ul>

</aside>
