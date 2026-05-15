const STORAGE_KEY = 'dd_ai_chat_session_id';
const CHAT_TIMEOUT_MS = 30000;

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

const appendMessage = (messages, role, content) => {
  const message = document.createElement('div');
  message.className = `dd-ai-chat-widget__message dd-ai-chat-widget__message--${role}`;
  message.textContent = content;
  messages.appendChild(message);
  messages.scrollTop = messages.scrollHeight;

  return message;
};

const initWidget = widget => {
  const endpoint = widget.dataset.aiChatEndpoint;
  const locale = widget.dataset.locale || 'fr';
  const countryCode = widget.dataset.country || 'global';
  const pageUrl = widget.dataset.pageUrl || window.location.href;
  const toggle = widget.querySelector('.dd-ai-chat-widget__toggle');
  const close = widget.querySelector('.dd-ai-chat-widget__close');
  const panel = widget.querySelector('.dd-ai-chat-widget__panel');
  const form = widget.querySelector('.dd-ai-chat-widget__form');
  const textarea = widget.querySelector('textarea[name="message"]');
  const submit = widget.querySelector('.dd-ai-chat-widget__send');
  const messages = widget.querySelector('.dd-ai-chat-widget__messages');

  if (!endpoint || !toggle || !close || !panel || !form || !textarea || !submit || !messages) return;

  const setOpen = isOpen => {
    panel.hidden = !isOpen;
    toggle.setAttribute('aria-expanded', String(isOpen));
    widget.classList.toggle('is-open', isOpen);

    if (isOpen) textarea.focus();
  };

  toggle.addEventListener('click', () => setOpen(panel.hidden));
  close.addEventListener('click', () => setOpen(false));

  form.addEventListener('submit', async event => {
    event.preventDefault();

    const message = textarea.value.trim();
    if (!message) return;

    appendMessage(messages, 'user', message);
    textarea.value = '';
    textarea.disabled = true;

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

      pending.textContent =
        data.message || textFor(locale, 'Je ne peux pas confirmer pour le moment.', 'I cannot confirm that right now.');
    } catch (error) {
      pending.textContent = textFor(
        locale,
        'Le chat est indisponible pour le moment. Vous pouvez aussi utiliser le formulaire de contact.',
        'Chat is unavailable right now. You can also use the contact form.'
      );
    } finally {
      if (timeoutId) window.clearTimeout(timeoutId);

      textarea.disabled = false;
      submit.disabled = false;
      textarea.focus();
      messages.scrollTop = messages.scrollHeight;
    }
  });
};

document.querySelectorAll('.dd-ai-chat-widget').forEach(initWidget);
