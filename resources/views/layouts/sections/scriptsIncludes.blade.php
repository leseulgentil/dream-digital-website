@php
  use Illuminate\Support\Facades\Vite;

  $menuCollapsed = $configData['menuCollapsed'] === 'layout-menu-collapsed' ? json_encode(true) : false;

  // Get skin value directly from the config, keeping it as numeric if applicable
  $skin = $configData['skins'] ?? 0;

  // If we have a skin name from cookie or other source, use that instead
  $skinName = $configData['skinName'] ?? '';

  // Use either the skin name or numeric ID, prioritizing the name if available
  $defaultSkin = $skinName ?: $skin;

  // Define layout type and cookie naming
  $isAdminLayout = !str_contains($configData['layout'] ?? '', 'front');
  $primaryColorCookieName = $isAdminLayout ? 'admin-primaryColor' : 'front-primaryColor';

  // Get primary color - first from cookie, then from config
  $primaryColor = isset($_COOKIE[$primaryColorCookieName])
      ? $_COOKIE[$primaryColorCookieName]
      : $configData['color'] ?? null;
@endphp
<!-- laravel style -->
@vite(['resources/assets/vendor/js/helpers.js'])

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  @vite(['resources/assets/js/config.js'])

@if ($primaryColor)
<script type="module">
  document.addEventListener('DOMContentLoaded', function() {
    if (window.Helpers && typeof window.Helpers.setColor === 'function') {
      window.Helpers.setColor("{{ $primaryColor }}", true);
    }
  });
</script>
@endif
