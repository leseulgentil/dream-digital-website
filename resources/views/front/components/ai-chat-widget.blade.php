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
      'sources_label' => 'Sources',
      'placeholder' => 'Posez votre question',
      'send' => 'Envoyer',
      'close' => 'Fermer le chat',
      'lead_title' => 'Laisser vos coordonnees',
      'lead_name' => 'Nom',
      'lead_company' => 'Entreprise',
      'lead_email' => 'Email',
      'lead_phone' => 'Telephone',
      'lead_whatsapp' => 'WhatsApp',
      'lead_need' => 'Besoin',
      'lead_consent' => 'J accepte que Dream Digital me contacte au sujet de ma demande.',
      'lead_submit' => 'Envoyer le lead',
      'lead_success' => 'Merci, un conseiller peut reprendre contact avec vous.',
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
      'sources_label' => 'Sources',
      'placeholder' => 'Ask your question',
      'send' => 'Send',
      'close' => 'Close chat',
      'lead_title' => 'Leave your contact details',
      'lead_name' => 'Name',
      'lead_company' => 'Company',
      'lead_email' => 'Email',
      'lead_phone' => 'Phone',
      'lead_whatsapp' => 'WhatsApp',
      'lead_need' => 'Need',
      'lead_consent' => 'I agree that Dream Digital may contact me about my request.',
      'lead_submit' => 'Send lead',
      'lead_success' => 'Thank you, an advisor can follow up with you.',
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
  data-ai-chat-lead-endpoint="{{ route('front.ai-chat.lead') }}"
  data-locale="{{ $locale }}"
  data-country="{{ session('dd_country_code', 'global') }}"
  data-page-url="{{ request()->fullUrl() }}"
  data-sources-label="{{ $copy['sources_label'] }}"
  data-lead-success="{{ $copy['lead_success'] }}"
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
          <button
            type="button"
            data-ai-chat-suggestion="{{ $suggestion }}"
            @if($loop->last) data-ai-chat-lead-trigger="true" @endif
          >{{ $suggestion }}</button>
        @endforeach
      </div>
    </div>

    <form class="dd-ai-chat-widget__lead-form" hidden>
      <div class="dd-ai-chat-widget__lead-head">
        <strong>{{ $copy['lead_title'] }}</strong>
        <button type="button" class="dd-ai-chat-widget__lead-close" aria-label="{{ $copy['close'] }}">
          <i class="bx bx-plus" aria-hidden="true"></i>
        </button>
      </div>
      <div class="dd-ai-chat-widget__lead-grid">
        <label>
          <span>{{ $copy['lead_name'] }}</span>
          <input type="text" name="name" maxlength="160" autocomplete="name">
        </label>
        <label>
          <span>{{ $copy['lead_company'] }}</span>
          <input type="text" name="company" maxlength="190" autocomplete="organization">
        </label>
        <label>
          <span>{{ $copy['lead_email'] }}</span>
          <input type="email" name="email" maxlength="190" autocomplete="email">
        </label>
        <label>
          <span>{{ $copy['lead_phone'] }}</span>
          <input type="tel" name="phone" maxlength="80" autocomplete="tel">
        </label>
        <label>
          <span>{{ $copy['lead_whatsapp'] }}</span>
          <input type="tel" name="whatsapp" maxlength="80">
        </label>
        <label class="dd-ai-chat-widget__lead-need">
          <span>{{ $copy['lead_need'] }}</span>
          <textarea name="need" rows="2" maxlength="2000"></textarea>
        </label>
      </div>
      <label class="dd-ai-chat-widget__lead-consent">
        <input type="checkbox" name="consent" value="1" required>
        <span>{{ $copy['lead_consent'] }}</span>
      </label>
      <div class="dd-ai-chat-widget__lead-actions">
        <button type="submit">{{ $copy['lead_submit'] }}</button>
        <span role="status"></span>
      </div>
    </form>

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
