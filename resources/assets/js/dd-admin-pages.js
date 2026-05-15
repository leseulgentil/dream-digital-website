/**
 * Dream Digital admin pages CMS helpers.
 */

'use strict';

(function () {
  const form = document.getElementById('dd-cms-page-form');
  const jsonField = document.getElementById('sections_json');
  const editorTarget = document.getElementById('sections_rich_editor');
  const faqJsonField = document.getElementById('faq_json');
  const faqRepeater = document.getElementById('dd_faq_repeater');
  const faqAddButton = document.getElementById('dd_faq_add');
  let editor = null;
  let fallbackRoot = null;

  const escapeHtml = value =>
    String(value || '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');

  const bodyToHtml = body =>
    String(body || '')
      .split(/\r?\n\r?\n/)
      .map(part => part.trim())
      .filter(Boolean)
      .map(part => `<p>${escapeHtml(part).replace(/\r?\n/g, '<br>')}</p>`)
      .join('');

  const sectionsToHtml = sections => {
    if (!Array.isArray(sections) || !sections.length) {
      return '<h2>Section 1</h2><p></p>';
    }

    return sections
      .map(section => {
        const heading = section.heading ? `<h2>${escapeHtml(section.heading)}</h2>` : '';
        const body = section.body_html || bodyToHtml(section.body || '');

        return `${heading}${body}`;
      })
      .join('');
  };

  const parseSectionsJson = () => {
    if (!jsonField || !jsonField.value.trim()) return [];

    try {
      const decoded = JSON.parse(jsonField.value);
      return Array.isArray(decoded) ? decoded : [];
    } catch (error) {
      return [];
    }
  };

  const normalizeHtml = html =>
    String(html || '')
      .replace(/\sdata-[a-z-]+="[^"]*"/g, '')
      .replace(/\sclass="[^"]*"/g, '')
      .trim();

  const editorToSections = () => {
    const richRoot = editor?.root || fallbackRoot;

    if (!richRoot) return parseSectionsJson();

    const sections = [];
    let current = null;

    Array.from(richRoot.children).forEach(node => {
      const tag = node.tagName ? node.tagName.toLowerCase() : '';
      const text = (node.innerText || node.textContent || '').trim();
      const html = normalizeHtml(node.outerHTML || '');

      if (tag === 'h1' || tag === 'h2' || tag === 'h3') {
        if (current) sections.push(current);
        current = {
          heading: text || 'Section',
          body: '',
          body_html: ''
        };
        return;
      }

      if (!text && !html.replace(/<[^>]+>/g, '').trim()) return;

      if (!current) {
        current = {
          heading: document.getElementById('title')?.value || 'Contenu',
          body: '',
          body_html: ''
        };
      }

      current.body += `${current.body ? '\n\n' : ''}${text}`;
      current.body_html += html;
    });

    if (current) sections.push(current);

    return sections.length ? sections : parseSectionsJson();
  };

  const syncJsonFromEditor = () => {
    if (!jsonField || (!editor && !fallbackRoot)) return;
    jsonField.value = JSON.stringify(editorToSections(), null, 2);
  };

  const hydrateEditorFromJson = () => {
    if (editor) {
      editor.clipboard.dangerouslyPasteHTML(sectionsToHtml(parseSectionsJson()));
      return;
    }

    if (fallbackRoot) {
      fallbackRoot.innerHTML = sectionsToHtml(parseSectionsJson());
    }
  };

  const bindEditorSync = () => {
    jsonField.addEventListener('blur', hydrateEditorFromJson);
    form?.addEventListener('submit', syncJsonFromEditor);
  };

  const bootFallbackEditor = () => {
    fallbackRoot = document.createElement('div');
    fallbackRoot.className = 'ql-editor';
    fallbackRoot.contentEditable = 'true';
    fallbackRoot.dataset.placeholder = 'Redigez le contenu riche ici...';
    editorTarget.classList.add('ql-container', 'ql-snow');
    editorTarget.innerHTML = '';
    editorTarget.appendChild(fallbackRoot);

    hydrateEditorFromJson();
    fallbackRoot.addEventListener('input', syncJsonFromEditor);
    bindEditorSync();
  };

  const bootQuillEditor = QuillEditor => {
    editor = new QuillEditor(editorTarget, {
      bounds: editorTarget,
      placeholder: 'Redigez le contenu riche ici...',
      modules: {
        toolbar: [
          [{ header: [2, 3, false] }],
          ['bold', 'italic', 'underline'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['blockquote', 'link'],
          ['clean']
        ]
      },
      theme: 'snow'
    });

    hydrateEditorFromJson();
    editor.on('text-change', syncJsonFromEditor);
    bindEditorSync();
  };

  const resolveQuillEditor = async () => {
    if (window.Quill) return window.Quill;

    try {
      const quillModule = await import('quill/dist/quill');

      return quillModule.default || quillModule.Quill || null;
    } catch (error) {
      return null;
    }
  };

  if (editorTarget && jsonField) {
    resolveQuillEditor().then(QuillEditor => {
      if (QuillEditor) {
        bootQuillEditor(QuillEditor);
      } else {
        bootFallbackEditor();
      }
    });
  }

  const parseFaqJson = () => {
    if (!faqJsonField || !faqJsonField.value.trim()) return [];

    try {
      const decoded = JSON.parse(faqJsonField.value);
      return Array.isArray(decoded) ? decoded : [];
    } catch (error) {
      return [];
    }
  };

  const faqItemsFromRepeater = () => {
    if (!faqRepeater) return parseFaqJson();

    return Array.from(faqRepeater.querySelectorAll('[data-dd-faq-item]'))
      .map(item => ({
        question: item.querySelector('[data-dd-faq-question]')?.value.trim() || '',
        answer: item.querySelector('[data-dd-faq-answer]')?.value.trim() || ''
      }))
      .filter(item => item.question || item.answer);
  };

  const syncFaqJsonFromRepeater = () => {
    if (!faqJsonField || !faqRepeater) return;
    faqJsonField.value = JSON.stringify(faqItemsFromRepeater(), null, 2);
  };

  const renderFaqItem = (item = {}) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'border rounded p-3 bg-body';
    wrapper.dataset.ddFaqItem = 'true';
    wrapper.innerHTML = `
      <div class="row g-3 align-items-start">
        <div class="col-md-5">
          <label class="form-label small text-muted">Question</label>
          <input type="text" class="form-control" data-dd-faq-question value="${escapeHtml(item.question)}" placeholder="Quelle est la question SEO ?">
        </div>
        <div class="col-md-6">
          <label class="form-label small text-muted">Reponse</label>
          <textarea rows="3" class="form-control" data-dd-faq-answer placeholder="Reponse courte, concrete et publiable.">${escapeHtml(item.answer)}</textarea>
        </div>
        <div class="col-md-1 d-flex justify-content-md-end">
          <button type="button" class="btn btn-icon btn-outline-danger mt-md-4" data-dd-faq-remove aria-label="Supprimer cette question">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>
    `;

    wrapper.querySelectorAll('input, textarea').forEach(field => {
      field.addEventListener('input', syncFaqJsonFromRepeater);
    });

    wrapper.querySelector('[data-dd-faq-remove]')?.addEventListener('click', () => {
      wrapper.remove();
      if (!faqRepeater.querySelector('[data-dd-faq-item]')) {
        faqRepeater.appendChild(renderFaqItem());
      }
      syncFaqJsonFromRepeater();
    });

    return wrapper;
  };

  const hydrateFaqRepeaterFromJson = () => {
    if (!faqRepeater) return;
    const items = parseFaqJson();
    faqRepeater.innerHTML = '';
    (items.length ? items : [{}]).forEach(item => faqRepeater.appendChild(renderFaqItem(item)));
  };

  if (faqRepeater && faqJsonField) {
    hydrateFaqRepeaterFromJson();
    faqJsonField.addEventListener('blur', hydrateFaqRepeaterFromJson);
    faqAddButton?.addEventListener('click', () => {
      faqRepeater.appendChild(renderFaqItem());
      syncFaqJsonFromRepeater();
    });
    form?.addEventListener('submit', syncFaqJsonFromRepeater);
  }

  const generatorButton = document.getElementById('article_generator_submit');
  const generatorStatus = document.getElementById('article_generator_status');
  const generatorResults = document.getElementById('article_generator_results');
  const generatorModal = document.getElementById('ddGenerateArticleModal');

  const openModalFallback = modalEl => {
    if (!modalEl) return;
    modalEl.style.display = 'block';
    modalEl.removeAttribute('aria-hidden');
    modalEl.setAttribute('aria-modal', 'true');
    modalEl.classList.add('show');
    document.body.classList.add('modal-open');

    if (!document.querySelector('.modal-backdrop[data-dd-fallback-backdrop]')) {
      const backdrop = document.createElement('div');
      backdrop.className = 'modal-backdrop fade show';
      backdrop.dataset.ddFallbackBackdrop = 'true';
      document.body.appendChild(backdrop);
    }
  };

  const closeModalFallback = modalEl => {
    if (!modalEl) return;
    modalEl.style.display = 'none';
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.removeAttribute('aria-modal');
    modalEl.classList.remove('show');
    document.body.classList.remove('modal-open');
    document.querySelector('.modal-backdrop[data-dd-fallback-backdrop]')?.remove();
  };

  document.querySelectorAll('[data-dd-open-article-generator]').forEach(button => {
    button.addEventListener('click', () => {
      window.setTimeout(() => {
        if (generatorModal && !generatorModal.classList.contains('show')) {
          if (window.bootstrap?.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(generatorModal).show();
          } else {
            openModalFallback(generatorModal);
          }
        }
      }, 0);
    });
  });

  const setValue = (id, value) => {
    const field = document.getElementById(id);
    if (field) field.value = value || '';
  };

  const setSelectValue = (id, value) => {
    const field = document.getElementById(id);
    if (field) field.value = value || field.value;
  };

  const applyArticle = article => {
    setSelectValue('section', article.section || 'blog');
    setValue('title', article.title);
    setValue('slug', article.slug);
    setSelectValue('locale', article.locale || 'fr');
    setValue('seo_title', article.seo_title);
    setValue('meta_description', article.meta_description);
    setValue('meta_image_path', article.meta_image_path);
    setValue('eyebrow', article.eyebrow || 'Blog');
    setValue('lead', article.lead);
    setValue('author', article.author || 'Dream Digital');
    setValue('reading_time', article.reading_time);
    setValue('tags', Array.isArray(article.tags) ? article.tags.join(', ') : article.tags);
    setValue('image_alt', article.image_alt);
    setValue('image_credit', article.image_credit);
    setValue('image_source_url', article.image_source_url);
    setValue('faq_json', JSON.stringify(article.faq || [], null, 2));
    hydrateFaqRepeaterFromJson();

    if (jsonField) {
      jsonField.value = JSON.stringify(article.sections || [], null, 2);
      hydrateEditorFromJson();
    }

    const modalEl = document.getElementById('ddGenerateArticleModal');
    if (modalEl && window.bootstrap) {
      window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    } else {
      closeModalFallback(modalEl);
    }
  };

  const renderResults = articles => {
    if (!generatorResults) return;
    generatorResults.innerHTML = '';

    articles.forEach((article, index) => {
      const card = document.createElement('div');
      card.className = 'col-md-4';
      card.innerHTML = `
        <div class="border rounded h-100 p-3 d-flex flex-column gap-2">
          <span class="badge bg-label-primary align-self-start">Option ${index + 1}</span>
          <strong>${escapeHtml(article.title)}</strong>
          <p class="text-muted small mb-0">${escapeHtml(article.meta_description)}</p>
          <button type="button" class="btn btn-sm btn-primary mt-auto">Utiliser cet article</button>
        </div>
      `;
      card.querySelector('button')?.addEventListener('click', () => applyArticle(article));
      generatorResults.appendChild(card);
    });
  };

  generatorButton?.addEventListener('click', async () => {
    if (!form) return;

    const idea = document.getElementById('article_generator_idea')?.value.trim();
    if (!idea) {
      generatorStatus.textContent = 'Ajoutez une idee principale.';
      return;
    }

    generatorButton.disabled = true;
    generatorStatus.textContent = 'Generation en cours...';

    try {
      const response = await fetch(form.dataset.generateArticleUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': form.dataset.csrfToken
        },
        body: JSON.stringify({
          idea,
          keywords: document.getElementById('article_generator_keywords')?.value || '',
          guidelines: document.getElementById('article_generator_guidelines')?.value || '',
          locale: document.getElementById('article_generator_locale')?.value || 'fr',
          variants: document.getElementById('article_generator_variants')?.value || 3
        })
      });

      if (!response.ok) throw new Error('Generation impossible');

      const payload = await response.json();
      renderResults(payload.articles || []);
      const providerLabel = payload.provider === 'openai'
        ? `Source: OpenAI${payload.model ? ` (${payload.model})` : ''}.`
        : (payload.fallback_used ? 'Source: fallback local.' : 'Source: local.');
      generatorStatus.textContent = `${(payload.articles || []).length} article(s) genere(s). ${providerLabel}`;
    } catch (error) {
      generatorStatus.textContent = 'Erreur pendant la generation.';
    } finally {
      generatorButton.disabled = false;
    }
  });
})();
