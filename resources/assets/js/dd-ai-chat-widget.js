const STORAGE_KEY = 'dd_ai_chat_session_id';

const textFor = (locale, fr, en) => (locale === 'en' ? en : fr);

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
  const messages = widget.querySelector('.dd-ai-chat-widget__messages');

  if (!endpoint || !toggle || !close || !panel || !form || !textarea || !messages) return;

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

    try {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const sessionId = window.localStorage.getItem(STORAGE_KEY);
      const response = await fetch(endpoint, {
        method: 'POST',
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
        window.localStorage.setItem(STORAGE_KEY, data.session_id);
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
      textarea.disabled = false;
      textarea.focus();
      messages.scrollTop = messages.scrollHeight;
    }
  });
};

document.querySelectorAll('.dd-ai-chat-widget').forEach(initWidget);
