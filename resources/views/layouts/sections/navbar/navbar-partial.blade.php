@php
use Illuminate\Support\Facades\Auth;

$user = Auth::user();
$initials = collect(explode(' ', trim((string) ($user?->name ?? 'Admin'))))
  ->filter()
  ->map(fn ($part) => mb_substr($part, 0, 1))
  ->take(2)
  ->implode('');
$initials = $initials !== '' ? mb_strtoupper($initials) : 'DD';
@endphp

@if (isset($navbarFull))
<div class="navbar-brand dd-app-brand demo d-none d-xl-flex py-0 me-6">
  <a href="{{ route('admin.dashboard') }}" class="dd-app-brand-link gap-2">
    <span class="dd-app-brand-logo demo">@include('_partials.macros')</span>
    <span class="dd-app-brand-text demo dd-menu-text fw-bold text-heading">{{ config('variables.templateName') }}</span>
  </a>
</div>
@endif

@if (!isset($navbarHideToggle))
<div class="dd-layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
  <button type="button" class="nav-item nav-link btn btn-link px-0 me-xl-6" aria-label="Ouvrir le menu admin">
    <i class="icon-base bx bx-menu icon-md"></i>
  </button>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
  <ul class="navbar-nav flex-row align-items-center ms-md-auto">
    <li class="nav-item me-2 me-xl-3 d-none d-sm-flex">
      <a class="btn btn-sm btn-outline-primary" href="{{ url('/fr') }}" target="_blank" rel="noopener">
        <i class="icon-base bx bx-world me-1"></i>
        Voir le site
      </a>
    </li>

    <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
      <button type="button" class="nav-link dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-label="Theme" aria-expanded="false">
        <i class="icon-base bx bx-sun icon-md theme-icon-active"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
        <li>
          <button type="button" class="dropdown-item" data-theme="light">
            <span class="align-middle"><i class="icon-base bx bx-sun icon-md me-3"></i>Light</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item" data-theme="dark">
            <span class="align-middle"><i class="icon-base bx bx-moon icon-md me-3"></i>Dark</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item" data-theme="system">
            <span class="align-middle"><i class="icon-base bx bx-desktop icon-md me-3"></i>System</span>
          </button>
        </li>
      </ul>
    </li>

    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <button type="button" class="nav-link dropdown-toggle hide-arrow p-0 btn btn-link" data-bs-toggle="dropdown" aria-label="Menu utilisateur" aria-expanded="false">
        <span class="avatar avatar-online">
          <span class="avatar-initial rounded-circle bg-label-primary">{{ $initials }}</span>
        </span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <div class="dropdown-item-text">
            <div class="d-flex align-items-center">
              <div class="avatar avatar-online me-3">
                <span class="avatar-initial rounded-circle bg-label-primary">{{ $initials }}</span>
              </div>
              <div>
                <h6 class="mb-0">{{ $user?->name ?? 'Admin Dream Digital' }}</h6>
                <small class="text-body-secondary">{{ $user?->roleLabel() ?? 'Admin' }}</small>
              </div>
            </div>
          </div>
        </li>
        <li><div class="dropdown-divider my-1"></div></li>
        <li class="d-sm-none">
          <a class="dropdown-item" href="{{ url('/fr') }}" target="_blank" rel="noopener">
            <i class="icon-base bx bx-world icon-md me-3"></i><span>Voir le site</span>
          </a>
        </li>
        @if ($user?->canManageUsers())
        <li>
          <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
            <i class="icon-base bx bx-user icon-md me-3"></i><span>Mon compte</span>
          </a>
        </li>
        @endif
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Logout</span>
          </a>
        </li>
      </ul>
      <form method="POST" id="logout-form" action="{{ route('logout') }}">
        @csrf
      </form>
    </li>
  </ul>
</div>
