@php
  $type = $item['type'] ?? 'link';
  $label = $item['label'] ?? '';
  $url = $item['url'] ?? '#';
  $settings = $item['settings'] ?? [];
  $targetAttributes = !empty($item['opens_new_tab']) ? ' target="_blank" rel="noopener"' : '';
  $path = parse_url($url, PHP_URL_PATH) ?: '';
  $parts = array_values(array_filter(explode('/', trim($path, '/'))));
  $segment = ($parts[0] ?? '') === $locale ? ($parts[1] ?? '') : ($parts[0] ?? '');
  $activeClass = $segment ? $isActive($segment) : '';
@endphp

@switch($type)
  @case(\App\Models\NavigationItem::TYPE_MEGA_SERVICES)
    <li class="nav-item dropdown dd-megamenu-item">
      <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('products') }}"
        data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
        {{ $label }}
      </button>
      <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--wide" role="menu" aria-label="{{ $label }}">
        <div class="dd-megamenu-grid dd-megamenu-grid--3col">
          @foreach ($services as $service)
            <a class="dd-megamenu-card" href="{{ url("/{$locale}/products/" . ($service['slug'] ?? $service['id'])) }}" role="menuitem">
              <span class="dd-megamenu-card__icon"><i class="bx {{ $service['icon'] ?? 'bx-radio-circle' }}" aria-hidden="true"></i></span>
              <span class="dd-megamenu-card__body">
                <span class="dd-megamenu-card__name">{{ $t($service['name'] ?? '') }}</span>
                <span class="dd-megamenu-card__tagline">{{ $t($service['tagline'] ?? '') }}</span>
              </span>
            </a>
          @endforeach
        </div>
        <a class="dd-megamenu-footer" href="{{ $pageUrl('products') }}" role="menuitem">
          {{ $locale === 'fr' ? 'Voir tous les produits' : 'See all products' }} <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
        </a>
      </div>
    </li>
    @break

  @case(\App\Models\NavigationItem::TYPE_MEGA_DEVELOPERS)
    <li class="nav-item dropdown dd-megamenu-item">
      <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('developers') }}"
        data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
        {{ $label }}
      </button>
      <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--medium" role="menu" aria-label="{{ $label }}">
        <div class="dd-megamenu-grid dd-megamenu-grid--2col">
          <a class="dd-megamenu-card" href="{{ $pageUrl('developers') }}#docs" role="menuitem">
            <span class="dd-megamenu-card__icon"><i class="bx bx-book-content" aria-hidden="true"></i></span>
            <span class="dd-megamenu-card__body">
              <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Documentation API' : 'API Documentation' }}</span>
              <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Bientot publiee.' : 'Coming soon.' }}</span>
            </span>
          </a>
          <a class="dd-megamenu-card" href="{{ $pageUrl('developers') }}#webhooks" role="menuitem">
            <span class="dd-megamenu-card__icon"><i class="bx bx-broadcast" aria-hidden="true"></i></span>
            <span class="dd-megamenu-card__body">
              <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Webhooks DLR' : 'DLR Webhooks' }}</span>
              <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Notifications signees, retry policy.' : 'Signed events, retry policy.' }}</span>
            </span>
          </a>
          <a class="dd-megamenu-card" href="{{ $pageUrl('developers') }}#sandbox" role="menuitem">
            <span class="dd-megamenu-card__icon"><i class="bx bx-code-block" aria-hidden="true"></i></span>
            <span class="dd-megamenu-card__body">
              <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Sandbox isole' : 'Isolated Sandbox' }}</span>
              <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Cles de test, aucun frais.' : 'Test keys, no charge.' }}</span>
            </span>
          </a>
          <a class="dd-megamenu-card" href="{{ $pageUrl('pricing') }}" role="menuitem">
            <span class="dd-megamenu-card__icon"><i class="bx bx-dollar-circle" aria-hidden="true"></i></span>
            <span class="dd-megamenu-card__body">
              <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Pricing API' : 'API Pricing' }}</span>
              <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Tarifs et corridors negocies.' : 'Rates and negotiated routes.' }}</span>
            </span>
          </a>
        </div>
      </div>
    </li>
    @break

  @case(\App\Models\NavigationItem::TYPE_MEGA_SOLUTIONS)
    <li class="nav-item dropdown dd-megamenu-item">
      <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('solutions') }}"
        data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
        {{ $label }}
      </button>
      <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--medium" role="menu" aria-label="{{ $label }}">
        <div class="dd-megamenu-grid dd-megamenu-grid--2col">
          @foreach ($industries as $industry)
            <a class="dd-megamenu-card" href="{{ $pageUrl('solutions') }}#{{ $industry['id'] }}" role="menuitem">
              <span class="dd-megamenu-card__icon"><i class="bx {{ $industry['icon'] ?? 'bx-grid-alt' }}" aria-hidden="true"></i></span>
              <span class="dd-megamenu-card__body">
                <span class="dd-megamenu-card__name">{{ $t($industry['name'] ?? '') }}</span>
                <span class="dd-megamenu-card__tagline">{{ $t($industry['description'] ?? '') }}</span>
              </span>
            </a>
          @endforeach
        </div>
        <a class="dd-megamenu-footer" href="{{ $pageUrl('solutions') }}" role="menuitem">
          {{ $locale === 'fr' ? 'Voir toutes les solutions' : 'See all solutions' }} <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
        </a>
      </div>
    </li>
    @break

  @case(\App\Models\NavigationItem::TYPE_MEGA_COMPANY)
    <li class="nav-item dropdown dd-megamenu-item dd-megamenu-item--narrow">
      <button type="button" class="nav-link fw-medium dropdown-toggle {{ $isActive('company') }}"
        data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
        {{ $label }}
      </button>
      <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--narrow" role="menu" aria-label="{{ $label }}">
        <div class="dd-megamenu-grid dd-megamenu-grid--1col">
          <a class="dd-megamenu-card" href="{{ $pageUrl('company') }}" role="menuitem">
            <span class="dd-megamenu-card__icon"><i class="bx bx-buildings" aria-hidden="true"></i></span>
            <span class="dd-megamenu-card__body">
              <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'A propos' : 'About' }}</span>
              <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Equipe, bureaux, mission.' : 'Team, offices, mission.' }}</span>
            </span>
          </a>
          <a class="dd-megamenu-card" href="{{ $pageUrl('contact') }}" role="menuitem">
            <span class="dd-megamenu-card__icon"><i class="bx bx-conversation" aria-hidden="true"></i></span>
            <span class="dd-megamenu-card__body">
              <span class="dd-megamenu-card__name">{{ $locale === 'fr' ? 'Contact' : 'Contact' }}</span>
              <span class="dd-megamenu-card__tagline">{{ $locale === 'fr' ? 'Sales, support, presse.' : 'Sales, support, press.' }}</span>
            </span>
          </a>
        </div>
      </div>
    </li>
    @break

  @case(\App\Models\NavigationItem::TYPE_DROPDOWN)
    @if(!empty($item['children']))
      <li class="nav-item dropdown dd-megamenu-item dd-megamenu-item--narrow">
        <button type="button" class="nav-link fw-medium dropdown-toggle {{ $activeClass }}"
          data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-haspopup="true">
          {{ $label }}
        </button>
        <div class="dropdown-menu dd-megamenu-panel dd-megamenu-panel--narrow" role="menu" aria-label="{{ $label }}">
          <div class="dd-megamenu-grid dd-megamenu-grid--1col">
            @foreach($item['children'] as $child)
              @php
                $childSettings = $child['settings'] ?? [];
                $description = $childSettings['description_' . $locale] ?? $childSettings['description_fr'] ?? ($child['raw_url'] ?? '');
                $childTarget = !empty($child['opens_new_tab']) ? ' target="_blank" rel="noopener"' : '';
              @endphp
              <a class="dd-megamenu-card" href="{{ $child['url'] ?? '#' }}" role="menuitem"{!! $childTarget !!}>
                <span class="dd-megamenu-card__icon"><i class="bx {{ $childSettings['icon'] ?? 'bx-link-alt' }}" aria-hidden="true"></i></span>
                <span class="dd-megamenu-card__body">
                  <span class="dd-megamenu-card__name">{{ $child['label'] ?? '' }}</span>
                  <span class="dd-megamenu-card__tagline">{{ $description }}</span>
                </span>
              </a>
            @endforeach
          </div>
        </div>
      </li>
    @else
      <li class="nav-item"><a class="nav-link fw-medium {{ $activeClass }}" href="{{ $url }}"{!! $targetAttributes !!}>{{ $label }}</a></li>
    @endif
    @break

  @default
    <li class="nav-item"><a class="nav-link fw-medium {{ $activeClass }}" href="{{ $url }}"{!! $targetAttributes !!}>{{ $label }}</a></li>
@endswitch
