'use strict';

(function () {
  const heroSlider = document.querySelector('.dd-hero-slider');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (heroSlider && window.Swiper) {
    new Swiper(heroSlider, {
      loop: true,
      effect: 'fade',
      fadeEffect: { crossFade: true },
      speed: reducedMotion ? 0 : 700,
      autoplay: reducedMotion
        ? false
        : {
            delay: 6000,
            pauseOnMouseEnter: true,
            disableOnInteraction: false
          },
      pagination: {
        el: '.dd-hero-pagination',
        clickable: true
      }
    });
  }

  const typeInto = (el, fullCode, delay) => {
    if (reducedMotion) {
      el.textContent = fullCode;
      return;
    }

    let index = 0;
    const tick = function () {
      el.textContent = fullCode.slice(0, index);
      index += 1;
      if (index <= fullCode.length) window.setTimeout(tick, delay);
    };
    tick();
  };

  const terminalCode = document.getElementById('dd-terminal-code');
  if (terminalCode) {
    typeInto(
      terminalCode,
      '$ curl -X POST \\\n' +
        '    https://api.dream-digital.info/v1/sms/send \\\n' +
        '    -H "Authorization: Bearer dd_live..." \\\n' +
        '    -d \'{\n' +
        '      "to": "+243990000000",\n' +
        '      "from": "DreamDigital",\n' +
        '      "text": "Bienvenue !"\n' +
        '    }\'\n\n' +
        'HTTP/1.1 200 OK\n' +
        '{\n' +
        '  "id": "sms_a2b3c4d5",\n' +
        '  "status": "delivered",\n' +
        '  "cost": 0.0089\n' +
        '}',
      18
    );
  }

  const previewBlocks = document.querySelectorAll('[data-code-preview]');
  if (previewBlocks.length && !reducedMotion) {
    const codeObserver = new IntersectionObserver(
      entries => {
        entries.forEach(entry => {
          if (!entry.isIntersecting) return;
          const el = entry.target;
          const fullCode = el.dataset.fullCode || el.textContent;
          el.dataset.fullCode = fullCode;
          el.textContent = '';
          typeInto(el, fullCode, 10);
          codeObserver.unobserve(el);
        });
      },
      { threshold: 0.35 }
    );

    previewBlocks.forEach(block => codeObserver.observe(block));
  }

  const counters = document.querySelectorAll('[data-dd-count]');
  if (!counters.length || reducedMotion) return;

  const animateCounter = entry => {
    const el = entry.target;
    const target = Number(el.dataset.ddCount || 0);
    const suffix = el.dataset.ddSuffix || '';
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 48));
    const update = () => {
      current = Math.min(target, current + step);
      el.textContent = current + suffix;
      if (current < target) window.requestAnimationFrame(update);
    };
    update();
  };

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      animateCounter(entry);
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.35 });

  counters.forEach(counter => observer.observe(counter));
})();
