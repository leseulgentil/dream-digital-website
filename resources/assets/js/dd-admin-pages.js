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
  const sectionField = document.getElementById('section');
  const productDetailPanel = document.querySelector('[data-dd-product-detail-panel]');
  const productDetailJsonField = document.getElementById('product_detail_json');
  const productProofsRepeater = document.getElementById('dd_product_proofs_repeater');
  const productWorkflowRepeater = document.getElementById('dd_product_workflow_repeater');
  const productProofAddButton = document.getElementById('dd_product_proof_add');
  const productWorkflowAddButton = document.getElementById('dd_product_workflow_add');
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

  const embeddedMediaSelector = 'img, picture, video, audio, iframe, embed, object';

  const pickImageFile = () =>
    new Promise(resolve => {
      const input = document.createElement('input');
      input.type = 'file';
      input.accept = 'image/jpeg,image/png,image/webp';
      input.addEventListener('change', () => resolve(input.files?.[0] || null), { once: true });
      input.click();
    });

  const uploadInlineImage = async () => {
    if (!form?.dataset.mediaUploadUrl || !editor) return;

    const file = await pickImageFile();
    if (!file) return;

    const payload = new FormData();
    payload.append('image', file);

    const response = await fetch(form.dataset.mediaUploadUrl, {
      method: 'POST',
      headers: {
        Accept: 'application/json',
        'X-CSRF-TOKEN': form.dataset.csrfToken
      },
      body: payload
    });

    if (!response.ok) {
      window.alert('Upload image impossible. Verifiez le format ou la taille du fichier.');
      return;
    }

    const media = await response.json();
    const range = editor.getSelection(true);
    editor.insertEmbed(range.index, 'image', media.path, 'user');
    editor.setSelection(range.index + 1, 0, 'silent');
    queueJsonSync();
  };

  const editorToSections = () => {
    const richRoot = editor?.root || fallbackRoot;

    if (!richRoot) return parseSectionsJson();

    const sections = [];
    let current = null;

    Array.from(richRoot.children).forEach(node => {
      const tag = node.tagName ? node.tagName.toLowerCase() : '';
      const text = (node.innerText || node.textContent || '').trim();
      const html = normalizeHtml(node.outerHTML || '');
      const embeddedMediaHtml = Array.from(node.querySelectorAll ? node.querySelectorAll(embeddedMediaSelector) : [])
        .map(media => normalizeHtml(media.outerHTML || ''))
        .join('');

      if (tag === 'h1' || tag === 'h2' || tag === 'h3') {
        if (current) sections.push(current);
        current = {
          heading: text || 'Section',
          body: '',
          body_html: embeddedMediaHtml
        };
        return;
      }

      const hasEmbeddedMedia = ['img', 'picture', 'video', 'audio', 'iframe', 'embed', 'object'].includes(tag)
        || Boolean(node.querySelector?.(embeddedMediaSelector));

      if (!text && !html.replace(/<[^>]+>/g, '').trim() && !hasEmbeddedMedia) return;

      if (!current) {
        current = {
          heading: document.getElementById('title')?.value || 'Contenu',
          body: '',
          body_html: ''
        };
      }

      if (text) {
        current.body += `${current.body ? '\n\n' : ''}${text}`;
      }
      current.body_html += html;
    });

    if (current) sections.push(current);

    return sections.length ? sections : parseSectionsJson();
  };

  const syncJsonFromEditor = () => {
    if (!jsonField || (!editor && !fallbackRoot)) return;
    jsonField.value = JSON.stringify(editorToSections(), null, 2);
  };

  const queueJsonSync = () => {
    if (window.requestAnimationFrame) {
      window.requestAnimationFrame(syncJsonFromEditor);
      return;
    }

    window.setTimeout(syncJsonFromEditor, 0);
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
    fallbackRoot.addEventListener('input', queueJsonSync);
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
          ['blockquote', 'link', 'image'],
          ['clean']
        ]
      },
      theme: 'snow'
    });

    editor.getModule('toolbar')?.addHandler('image', uploadInlineImage);

    hydrateEditorFromJson();
    editor.on('text-change', queueJsonSync);
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

  const parseProductDetailJson = () => {
    if (!productDetailJsonField || !productDetailJsonField.value.trim()) {
      return { proofs: [], workflow: [] };
    }

    try {
      const decoded = JSON.parse(productDetailJsonField.value);
      return {
        proofs: Array.isArray(decoded.proofs) ? decoded.proofs : [],
        workflow: Array.isArray(decoded.workflow) ? decoded.workflow : []
      };
    } catch (error) {
      return { proofs: [], workflow: [] };
    }
  };

  const productDetailFromRepeaters = () => ({
    proofs: productProofsRepeater
      ? Array.from(productProofsRepeater.querySelectorAll('[data-dd-product-proof]'))
          .map(item => ({
            icon: item.querySelector('[data-dd-product-proof-icon]')?.value.trim() || 'bx-check',
            title: item.querySelector('[data-dd-product-proof-title]')?.value.trim() || '',
            body: item.querySelector('[data-dd-product-proof-body]')?.value.trim() || ''
          }))
          .filter(item => item.title || item.body)
      : parseProductDetailJson().proofs,
    workflow: productWorkflowRepeater
      ? Array.from(productWorkflowRepeater.querySelectorAll('[data-dd-product-workflow]'))
          .map(item => ({
            label: item.querySelector('[data-dd-product-workflow-label]')?.value.trim() || '',
            body: item.querySelector('[data-dd-product-workflow-body]')?.value.trim() || ''
          }))
          .filter(item => item.label || item.body)
      : parseProductDetailJson().workflow
  });

  const syncProductDetailJsonFromRepeaters = () => {
    if (!productDetailJsonField || !productProofsRepeater || !productWorkflowRepeater) return;
    productDetailJsonField.value = JSON.stringify(productDetailFromRepeaters(), null, 2);
  };

  const renderProductProofItem = (item = {}) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'border rounded p-3 bg-body-tertiary';
    wrapper.dataset.ddProductProof = 'true';
    wrapper.innerHTML = `
      <div class="row g-3 align-items-start">
        <div class="col-md-3">
          <label class="form-label small text-muted">Icone</label>
          <input type="text" class="form-control" data-dd-product-proof-icon value="${escapeHtml(item.icon || 'bx-check')}" placeholder="bx-check">
        </div>
        <div class="col-md-4">
          <label class="form-label small text-muted">Titre</label>
          <input type="text" class="form-control" data-dd-product-proof-title value="${escapeHtml(item.title)}" placeholder="SLA, qualite, supervision...">
        </div>
        <div class="col-md-4">
          <label class="form-label small text-muted">Texte</label>
          <textarea rows="2" class="form-control" data-dd-product-proof-body placeholder="Preuve courte et factuelle.">${escapeHtml(item.body)}</textarea>
        </div>
        <div class="col-md-1 d-flex justify-content-md-end">
          <button type="button" class="btn btn-icon btn-outline-danger mt-md-4" data-dd-product-proof-remove aria-label="Supprimer cette preuve">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>
    `;

    wrapper.querySelectorAll('input, textarea').forEach(field => {
      field.addEventListener('input', syncProductDetailJsonFromRepeaters);
    });
    wrapper.querySelector('[data-dd-product-proof-remove]')?.addEventListener('click', () => {
      wrapper.remove();
      if (!productProofsRepeater.querySelector('[data-dd-product-proof]')) {
        productProofsRepeater.appendChild(renderProductProofItem());
      }
      syncProductDetailJsonFromRepeaters();
    });

    return wrapper;
  };

  const renderProductWorkflowItem = (item = {}) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'border rounded p-3 bg-body-tertiary';
    wrapper.dataset.ddProductWorkflow = 'true';
    wrapper.innerHTML = `
      <div class="row g-3 align-items-start">
        <div class="col-md-5">
          <label class="form-label small text-muted">Etape</label>
          <input type="text" class="form-control" data-dd-product-workflow-label value="${escapeHtml(item.label)}" placeholder="Cadrage, test, go-live...">
        </div>
        <div class="col-md-6">
          <label class="form-label small text-muted">Texte</label>
          <textarea rows="2" class="form-control" data-dd-product-workflow-body placeholder="Description courte de l etape.">${escapeHtml(item.body)}</textarea>
        </div>
        <div class="col-md-1 d-flex justify-content-md-end">
          <button type="button" class="btn btn-icon btn-outline-danger mt-md-4" data-dd-product-workflow-remove aria-label="Supprimer cette etape">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>
    `;

    wrapper.querySelectorAll('input, textarea').forEach(field => {
      field.addEventListener('input', syncProductDetailJsonFromRepeaters);
    });
    wrapper.querySelector('[data-dd-product-workflow-remove]')?.addEventListener('click', () => {
      wrapper.remove();
      if (!productWorkflowRepeater.querySelector('[data-dd-product-workflow]')) {
        productWorkflowRepeater.appendChild(renderProductWorkflowItem());
      }
      syncProductDetailJsonFromRepeaters();
    });

    return wrapper;
  };

  const hydrateProductDetailRepeatersFromJson = () => {
    if (!productProofsRepeater || !productWorkflowRepeater) return;

    const detail = parseProductDetailJson();
    productProofsRepeater.innerHTML = '';
    productWorkflowRepeater.innerHTML = '';
    (detail.proofs.length ? detail.proofs : [{}]).forEach(item => productProofsRepeater.appendChild(renderProductProofItem(item)));
    (detail.workflow.length ? detail.workflow : [{}]).forEach(item => productWorkflowRepeater.appendChild(renderProductWorkflowItem(item)));
  };

  const toggleProductDetailPanel = () => {
    if (!productDetailPanel) return;

    const isProduct = sectionField?.value === 'product';
    productDetailPanel.classList.toggle('d-none', !isProduct);

    if (productDetailJsonField) {
      productDetailJsonField.disabled = !isProduct;
    }
  };

  if (productDetailPanel && productDetailJsonField) {
    hydrateProductDetailRepeatersFromJson();
    toggleProductDetailPanel();
    productDetailJsonField.addEventListener('blur', hydrateProductDetailRepeatersFromJson);
    productProofAddButton?.addEventListener('click', () => {
      productProofsRepeater.appendChild(renderProductProofItem());
      syncProductDetailJsonFromRepeaters();
    });
    productWorkflowAddButton?.addEventListener('click', () => {
      productWorkflowRepeater.appendChild(renderProductWorkflowItem());
      syncProductDetailJsonFromRepeaters();
    });
    sectionField?.addEventListener('change', toggleProductDetailPanel);
    form?.addEventListener('submit', () => {
      if (sectionField?.value === 'product' && productDetailJsonField) {
        productDetailJsonField.disabled = false;
        syncProductDetailJsonFromRepeaters();
      }
    });
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
      const currentSection = document.getElementById('section')?.value;
      const currentSlug = document.getElementById('slug')?.value;
      if (currentSection && ['blog', 'product'].includes(currentSection)) {
        setSelectValue('article_generator_target_section', currentSection);
      }
      setValue('article_generator_target_slug', currentSlug);

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
    toggleProductDetailPanel();
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

  const applyArticleSections = article => {
    if (!jsonField) return;

    const existingSections = parseSectionsJson();
    const generatedSections = Array.isArray(article.sections) ? article.sections : [];

    jsonField.value = JSON.stringify([...existingSections, ...generatedSections], null, 2);
    hydrateEditorFromJson();

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
          <div class="d-grid gap-2 mt-auto">
            <button type="button" class="btn btn-sm btn-primary" data-dd-apply-full>Remplacer toute la page</button>
            <button type="button" class="btn btn-sm btn-outline-primary" data-dd-apply-sections>Ajouter les sections</button>
          </div>
        </div>
      `;
      card.querySelector('[data-dd-apply-full]')?.addEventListener('click', () => applyArticle(article));
      card.querySelector('[data-dd-apply-sections]')?.addEventListener('click', () => applyArticleSections(article));
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
          variants: document.getElementById('article_generator_variants')?.value || 3,
          target_section: document.getElementById('article_generator_target_section')?.value || 'blog',
          target_slug: document.getElementById('article_generator_target_slug')?.value || document.getElementById('slug')?.value || ''
        })
      });

      if (!response.ok) {
        let errorMessage = 'Generation impossible.';

        try {
          const errorPayload = await response.json();
          errorMessage = errorPayload.message || errorPayload.error || errorMessage;
        } catch (error) {
          if (response.status === 429) {
            errorMessage = 'Limite temporaire atteinte. Patientez une minute puis relancez la generation.';
          }
        }

        if (response.status === 429) {
          errorMessage = 'Limite temporaire atteinte. Patientez une minute puis relancez la generation.';
        }

        throw new Error(errorMessage);
      }

      const payload = await response.json();
      renderResults(payload.articles || []);
      const providerLabel = payload.provider === 'openai'
        ? `Source: OpenAI${payload.model ? ` (${payload.model})` : ''}.`
        : (payload.fallback_used ? 'Source: fallback local.' : 'Source: local.');
      generatorStatus.textContent = `${(payload.articles || []).length} article(s) genere(s). ${providerLabel}`;
    } catch (error) {
      generatorStatus.textContent = error?.message || 'Erreur pendant la generation.';
    } finally {
      generatorButton.disabled = false;
    }
  });
})();
