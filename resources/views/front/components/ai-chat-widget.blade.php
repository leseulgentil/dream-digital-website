@php
  $locale = app()->getLocale() ?: 'fr';
  $settings = $settings ?? null;
  $greetings = is_array($settings?->greetings) ? $settings->greetings : [];
  $greeting = $greetings[$locale] ?? $greetings['fr'] ?? $greetings['en'] ?? 'Bonjour, comment puis-je aider ?';
  $maxMessageChars = max(200, min(2000, (int) ($settings?->max_message_chars ?? 1200)));
  $copy = [
    'fr' => [
      'toggle' => 'Assistant IA',
      'eyebrow' => 'Assistant Dream Digital',
      'title' => 'Bonjour, je vous aide a trouver la bonne information.',
      'status' => 'Base locale active',
      'source' => 'Reponses limitees aux donnees publiees',
      'suggestions_label' => 'Questions rapides',
      'placeholder' => 'Posez votre question',
      'send' => 'Envoyer',
      'close' => 'Fermer le chat',
      'suggestions' => [
        'Quels services proposez-vous ?',
        'Comment demander un devis ?',
        'Quels pays couvrez-vous ?',
        'Parler a un conseiller',
      ],
    ],
    'en' => [
      'toggle' => 'AI assistant',
      'eyebrow' => 'Dream Digital assistant',
      'title' => 'Hello, I can help you find the right information.',
      'status' => 'Local knowledge active',
      'source' => 'Answers limited to published data',
      'suggestions_label' => 'Quick questions',
      'placeholder' => 'Ask your question',
      'send' => 'Send',
      'close' => 'Close chat',
      'suggestions' => [
        'What services do you offer?',
        'How can I request a quote?',
        'Which countries do you cover?',
        'Talk to an advisor',
      ],
    ],
  ][$locale === 'en' ? 'en' : 'fr'];
@endphp

<div
  id="dd-ai-chat-widget"
  class="dd-ai-chat-widget"
  data-ai-chat-endpoint="{{ route('front.ai-chat.message') }}"
  data-locale="{{ $locale }}"
  data-country="{{ session('dd_country_code', 'global') }}"
  data-page-url="{{ request()->fullUrl() }}"
>
  <button class="dd-ai-chat-widget__toggle" type="button" aria-expanded="false" aria-controls="dd-ai-chat-panel">
    <span class="dd-ai-chat-widget__toggle-icon">
      <i class="bx bx-message-detail" aria-hidden="true"></i>
    </span>
    <span class="dd-ai-chat-widget__toggle-copy">
      <strong>{{ $copy['toggle'] }}</strong>
      <span>{{ $copy['status'] }}</span>
    </span>
  </button>

  <section class="dd-ai-chat-widget__panel" id="dd-ai-chat-panel" aria-label="Dream Digital chat" hidden>
    <header class="dd-ai-chat-widget__header">
      <div class="dd-ai-chat-widget__identity" aria-hidden="true">
        <span>DD</span>
      </div>
      <div class="dd-ai-chat-widget__header-copy">
        <span>{{ $copy['eyebrow'] }}</span>
        <strong>{{ $copy['title'] }}</strong>
      </div>
      <button class="dd-ai-chat-widget__close" type="button" aria-label="{{ $copy['close'] }}">
        <i class="bx bx-plus" aria-hidden="true"></i>
      </button>
    </header>

    <div class="dd-ai-chat-widget__trust">
      <span class="dd-ai-chat-widget__pulse" aria-hidden="true"></span>
      <span>{{ $copy['source'] }}</span>
    </div>

    <div class="dd-ai-chat-widget__messages" role="log" aria-live="polite" aria-relevant="additions">
      <div class="dd-ai-chat-widget__message dd-ai-chat-widget__message--assistant">{{ $greeting }}</div>
    </div>

    <div class="dd-ai-chat-widget__suggestions" aria-label="{{ $copy['suggestions_label'] }}">
      <span>{{ $copy['suggestions_label'] }}</span>
      <div>
        @foreach($copy['suggestions'] as $suggestion)
          <button type="button" data-ai-chat-suggestion="{{ $suggestion }}">{{ $suggestion }}</button>
        @endforeach
      </div>
    </div>

    <form class="dd-ai-chat-widget__form">
      <label class="dd-ai-chat-widget__input-label" for="dd-ai-chat-message">Message</label>
      <textarea
        id="dd-ai-chat-message"
        name="message"
        maxlength="{{ $maxMessageChars }}"
        rows="2"
        placeholder="{{ $copy['placeholder'] }}"
        required
      ></textarea>
      <button class="dd-ai-chat-widget__send" type="submit" aria-label="{{ $copy['send'] }}">
        <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
      </button>
    </form>
  </section>
</div>
