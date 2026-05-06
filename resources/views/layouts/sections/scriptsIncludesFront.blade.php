@php
  use Illuminate\Support\Facades\Vite;

  // Get primary color - first from cookie, then from config
  $primaryColor = isset($_COOKIE['front-primaryColor']) ? $_COOKIE['front-primaryColor'] : $configData['color'] ?? null;
@endphp
<!-- laravel style -->
@vite(['resources/assets/vendor/js/helpers.js'])

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
@vite(['resources/assets/js/front-config.js'])

@if ($primaryColor)
<script type="module">
  document.addEventListener('DOMContentLoaded', function() {
    if (window.Helpers && typeof window.Helpers.setColor === 'function') {
      window.Helpers.setColor("{{ $primaryColor }}", true);
    }
  });
</script>
@endif
