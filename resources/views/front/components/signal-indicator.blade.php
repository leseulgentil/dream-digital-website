@props(['status' => 'success', 'label' => null])

@php
  $statusClass = in_array($status, ['success', 'warning', 'info'], true) ? $status : 'success';
@endphp

<span class="dd-signal-indicator dd-signal-indicator--{{ $statusClass }}">
  <span class="dd-signal-indicator__dot" aria-hidden="true"></span>
  @if($label)
    <span class="dd-signal-indicator__label">{{ $label }}</span>
  @endif
</span>
