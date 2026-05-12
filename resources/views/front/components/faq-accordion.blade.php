@props(['items' => [], 'locale' => 'fr'])

@php
  $t = fn ($value) => is_array($value) ? ($value[$locale] ?? $value['fr'] ?? reset($value)) : $value;
@endphp

<section class="dd-section dd-faq" id="faq">
  <div class="dd-front-container dd-faq__grid">
    <div>
      <p class="dd-eyebrow">FAQ</p>
      <h2>{{ $locale === 'fr' ? 'Questions frequentes' : 'Frequently asked questions' }}</h2>
      <p>{{ $locale === 'fr' ? 'Quelques reponses utiles avant de lancer une integration.' : 'Useful answers before starting an integration.' }}</p>
    </div>

    <div class="accordion" id="ddFaq">
      @foreach ($items as $index => $item)
        @php $id = 'dd-faq-' . $index; @endphp
        <article class="accordion-item dd-faq-item">
          <h3 class="accordion-header" id="{{ $id }}-heading">
            <button class="accordion-button @if($index !== 0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="{{ $id }}">
              {{ $t($item['question'] ?? '') }}
            </button>
          </h3>
          <div id="{{ $id }}" class="accordion-collapse collapse @if($index === 0) show @endif" aria-labelledby="{{ $id }}-heading" data-bs-parent="#ddFaq">
            <div class="accordion-body">{{ $t($item['answer'] ?? '') }}</div>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
