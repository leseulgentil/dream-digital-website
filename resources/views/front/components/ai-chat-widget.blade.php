@php
  $locale = app()->getLocale() ?: 'fr';
  $settings = \App\Models\AiChatSetting::current();
  $greetings = is_array($settings->greetings) ? $settings->greetings : [];
  $greeting = $greetings[$locale] ?? $greetings['fr'] ?? $greetings['en'] ?? 'Bonjour, comment puis-je aider ?';
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
    <i class="bx bx-message-detail" aria-hidden="true"></i>
    <span>Chat</span>
  </button>

  <section class="dd-ai-chat-widget__panel" id="dd-ai-chat-panel" aria-label="Dream Digital chat" hidden>
    <header class="dd-ai-chat-widget__header">
      <div>
        <strong>Dream Digital</strong>
        <span>Assistant</span>
      </div>
      <button class="dd-ai-chat-widget__close" type="button" aria-label="Fermer le chat">
        <i class="bx bx-plus" aria-hidden="true"></i>
      </button>
    </header>

    <div class="dd-ai-chat-widget__messages" role="log" aria-live="polite" aria-relevant="additions">
      <div class="dd-ai-chat-widget__message dd-ai-chat-widget__message--assistant">{{ $greeting }}</div>
    </div>

    <form class="dd-ai-chat-widget__form">
      <label class="dd-ai-chat-widget__input-label" for="dd-ai-chat-message">Message</label>
      <textarea
        id="dd-ai-chat-message"
        name="message"
        maxlength="1200"
        rows="2"
        placeholder="Posez votre question"
        required
      ></textarea>
      <button class="dd-ai-chat-widget__send" type="submit" aria-label="Envoyer">
        <i class="bx bx-right-arrow-alt" aria-hidden="true"></i>
      </button>
    </form>
  </section>
</div>
