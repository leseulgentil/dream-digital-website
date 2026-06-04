const STORAGE_KEY = 'dd_ai_chat_session_id';
const CHAT_TIMEOUT_MS = 30000;
const LEAD_TIMEOUT_MS = 30000;

const textFor = (locale, fr, en) => (locale === 'en' ? en : fr);

const getStoredSessionId = () => {
  try {
    return window.localStorage.getItem(STORAGE_KEY);
  } catch (error) {
    return null;
  }
};

const setStoredSessionId = sessionId => {
  try {
    window.localStorage.setItem(STORAGE_KEY, sessionId);
  } catch (error) {
    // Continue without persistence when storage is unavailable.
  }
};

const renderSources = (message, sources, label) => {
  const cleanSources = Array.isArray(sources) ? sources.filter(source => source && (source.title || source.url)) : [];

  if (!cleanSources.length) return;

  const sourcesBox = document.createElement('div');
  sourcesBox.className = 'dd-ai-chat-widget__sources';

  const heading = document.createElement('span');
  heading.textContent = label || 'Sources';
  sourcesBox.appendChild(heading);

  cleanSources.slice(0, 5).forEach(source => {
    const item = source.url ? document.createElement('a') : document.createElement('span');
    item.textContent = source.title || source.source_title || source.url || 'Source';

    if (source.url) {
      item.href = source.url;
      item.target = '_blank';
      item.rel = 'noopener noreferrer';
    }

    sourcesBox.appendChild(item);
  });

  message.appendChild(sourcesBox);
};

const setMessageContent = (message, content, sources = [], sourcesLabel = 'Sources') => {
  message.textContent = '';
  const body = document.createElement('span');
  body.textContent = content;
  message.appendChild(body);
  renderSources(message, sources, sourcesLabel);
};

const appendMessage = (messages, role, content, sources = [], sourcesLabel = 'Sources') => {
  const message = document.createElement('div');
  message.className = `dd-ai-chat-widget__message dd-ai-chat-widget__message--${role}`;
  setMessageContent(message, content, sources, sourcesLabel);
  messages.appendChild(message);
  messages.scrollTop = messages.scrollHeight;

  return message;
};

const initWidget = widget => {
  const endpoint = widget.dataset.aiChatEndpoint;
  const leadEndpoint = widget.dataset.aiChatLeadEndpoint;
  const locale = widget.dataset.locale || 'fr';
  const countryCode = widget.dataset.country || 'global';
  const pageUrl = widget.dataset.pageUrl || window.location.href;
  const sourcesLabel = widget.dataset.sourcesLabel || 'Sources';
  const leadSuccess = widget.dataset.leadSuccess || textFor(locale, 'Merci.', 'Thank you.');
  const toggle = widget.querySelector('.dd-ai-chat-widget__toggle');
  const close = widget.querySelector('.dd-ai-chat-widget__close');
  const panel = widget.querySelector('.dd-ai-chat-widget__panel');
  const form = widget.querySelector('.dd-ai-chat-widget__form');
  const leadForm = widget.querySelector('.dd-ai-chat-widget__lead-form');
  const leadClose = widget.querySelector('.dd-ai-chat-widget__lead-close');
  const leadStatus = widget.querySelector('.dd-ai-chat-widget__lead-actions span');
  const textarea = widget.querySelector('textarea[name="message"]');
  const submit = widget.querySelector('.dd-ai-chat-widget__send');
  const messages = widget.querySelector('.dd-ai-chat-widget__messages');
  const suggestions = widget.querySelectorAll('[data-ai-chat-suggestion]');

  if (!endpoint || !toggle || !close || !panel || !form || !textarea || !submit || !messages) return;

  const setOpen = isOpen => {
    panel.hidden = !isOpen;
    toggle.setAttribute('aria-expanded', String(isOpen));
    widget.classList.toggle('is-open', isOpen);

    if (isOpen) textarea.focus();
  };

  toggle.addEventListener('click', () => setOpen(panel.hidden));
  close.addEventListener('click', () => setOpen(false));

  const showLeadForm = need => {
    if (!leadForm) return;

    leadForm.hidden = false;
    const needField = leadForm.querySelector('[name="need"]');
    if (need && needField && !needField.value.trim()) {
      needField.value = need;
    }

    leadForm.querySelector('input[name="name"], input[name="email"], input[name="phone"]')?.focus();
  };

  const hideLeadForm = () => {
    if (leadForm) leadForm.hidden = true;
  };

  leadClose?.addEventListener('click', hideLeadForm);

  const sendMessage = async value => {
    if (textarea.disabled) return;

    const message = value.trim();
    if (!message) return;

    appendMessage(messages, 'user', message);
    textarea.value = '';
    textarea.disabled = true;
    suggestions.forEach(suggestion => {
      suggestion.disabled = true;
    });

    const pending = appendMessage(
      messages,
      'assistant',
      textFor(locale, 'Un instant...', 'One moment...')
    );

    let timeoutId;

    try {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const sessionId = getStoredSessionId();
      const controller = typeof AbortController !== 'undefined' ? new AbortController() : null;

      if (controller) {
        timeoutId = window.setTimeout(() => controller.abort(), CHAT_TIMEOUT_MS);
      }

      submit.disabled = true;

      const response = await fetch(endpoint, {
        method: 'POST',
        signal: controller?.signal,
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          session_id: sessionId || undefined,
          message,
          locale,
          country_code: countryCode,
          page_url: pageUrl
        })
      });

      if (!response.ok) throw new Error(`Chat request failed: ${response.status}`);

      const data = await response.json();

      if (data.session_id) {
        setStoredSessionId(data.session_id);
      }

      setMessageContent(
        pending,
        data.message || textFor(locale, 'Je ne peux pas confirmer pour le moment.', 'I cannot confirm that right now.'),
        data.sources || [],
        sourcesLabel
      );

      if (data.answered === false) {
        showLeadForm(message);
      }
    } catch (error) {
      setMessageContent(
        pending,
        textFor(
          locale,
          'Le chat est indisponible pour le moment. Vous pouvez aussi utiliser le formulaire de contact.',
          'Chat is unavailable right now. You can also use the contact form.'
        ),
        [],
        sourcesLabel
      );
      showLeadForm(message);
    } finally {
      if (timeoutId) window.clearTimeout(timeoutId);

      textarea.disabled = false;
      submit.disabled = false;
      suggestions.forEach(suggestion => {
        suggestion.disabled = false;
      });
      textarea.focus();
      messages.scrollTop = messages.scrollHeight;
    }
  };

  form.addEventListener('submit', event => {
    event.preventDefault();
    sendMessage(textarea.value);
  });

  suggestions.forEach(suggestion => {
    suggestion.addEventListener('click', () => {
      setOpen(true);
      if (suggestion.dataset.aiChatLeadTrigger === 'true') {
        showLeadForm(suggestion.dataset.aiChatSuggestion || suggestion.textContent || '');
      }
      sendMessage(suggestion.dataset.aiChatSuggestion || suggestion.textContent || '');
    });
  });

  leadForm?.addEventListener('submit', async event => {
    event.preventDefault();

    if (!leadEndpoint) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const data = new FormData(leadForm);
    const submitButton = leadForm.querySelector('button[type="submit"]');
    const contact = ['email', 'phone', 'whatsapp'].some(field => String(data.get(field) || '').trim());

    if (!contact) {
      if (leadStatus) {
        leadStatus.textContent = textFor(locale, 'Ajoutez email, telephone ou WhatsApp.', 'Add email, phone or WhatsApp.');
      }
      return;
    }

    let timeoutId;

    try {
      const controller = typeof AbortController !== 'undefined' ? new AbortController() : null;
      if (controller) {
        timeoutId = window.setTimeout(() => controller.abort(), LEAD_TIMEOUT_MS);
      }

      if (submitButton) submitButton.disabled = true;
      if (leadStatus) leadStatus.textContent = textFor(locale, 'Envoi...', 'Sending...');

      const response = await fetch(leadEndpoint, {
        method: 'POST',
        signal: controller?.signal,
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          session_id: getStoredSessionId() || undefined,
          locale,
          country_code: countryCode,
          page_url: pageUrl,
          name: data.get('name') || '',
          email: data.get('email') || '',
          phone: data.get('phone') || '',
          whatsapp: data.get('whatsapp') || '',
          company: data.get('company') || '',
          need: data.get('need') || '',
          consent: Boolean(data.get('consent'))
        })
      });

      if (!response.ok) throw new Error(`Lead request failed: ${response.status}`);

      const payload = await response.json();

      if (payload.session_id) {
        setStoredSessionId(payload.session_id);
      }

      if (leadStatus) leadStatus.textContent = payload.message || leadSuccess;
      appendMessage(messages, 'assistant', payload.message || leadSuccess, [], sourcesLabel);
    } catch (error) {
      if (leadStatus) {
        leadStatus.textContent = textFor(locale, 'Envoi impossible pour le moment.', 'Could not send right now.');
      }
    } finally {
      if (timeoutId) window.clearTimeout(timeoutId);
      if (submitButton) submitButton.disabled = false;
      messages.scrollTop = messages.scrollHeight;
    }
  });
};

document.querySelectorAll('.dd-ai-chat-widget').forEach(initWidget);
