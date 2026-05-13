@props(['detail' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : ($value ?? '');
  $proofs = $detail['proofs'] ?? [];
  $workflow = $detail['workflow'] ?? [];
@endphp

@if($proofs !== [] || $workflow !== [])
  <section class="dd-section dd-product-proof">
    <div class="dd-front-container">
      @if($proofs !== [])
        <div class="dd-product-proof__grid">
          @foreach($proofs as $proof)
            <article>
              <i class="bx {{ $proof['icon'] ?? 'bx-check' }}" aria-hidden="true"></i>
              <h3>{{ $t($proof['title'] ?? '') }}</h3>
              <p>{{ $t($proof['body'] ?? '') }}</p>
            </article>
          @endforeach
        </div>
      @endif

      @if($workflow !== [])
        <div class="dd-product-workflow">
          <div class="dd-section-heading">
            <p class="dd-eyebrow">{{ $locale === 'fr' ? 'Mise en route' : 'Rollout' }}</p>
            <h2>{{ $locale === 'fr' ? 'Un chemin clair du cadrage au live' : 'A clear path from scoping to live' }}</h2>
          </div>
          <div class="dd-product-workflow__steps">
            @foreach($workflow as $i => $step)
              <article>
                <span>{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $t($step['label'] ?? '') }}</h3>
                <p>{{ $t($step['body'] ?? '') }}</p>
              </article>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </section>
@endif
