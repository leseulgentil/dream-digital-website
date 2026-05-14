@php
$containerFooter =
isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
? 'container-xxl'
: 'container-fluid';
@endphp

<footer class="dd-content-footer footer dd-bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column gap-2">
      <span>&copy; {{ date('Y') }} Dream Digital. Backoffice interne.</span>
      <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="footer-link">Dashboard</a>
        <a href="{{ url('/fr') }}" target="_blank" rel="noopener" class="footer-link">Site public</a>
      </div>
    </div>
  </div>
</footer>
