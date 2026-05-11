/**
 * Main - Front Pages
 */
'use strict';

(function () {
  const nav = document.querySelector('.dd-layout-navbar'),
    heroAnimation = document.getElementById('hero-animation'),
    animationImg = document.querySelectorAll('.hero-dashboard-img'),
    animationElements = document.querySelectorAll('.hero-elements-img'),
    swiperLogos = document.getElementById('swiper-clients-logos'),
    swiperReviews = document.getElementById('swiper-reviews'),
    ReviewsPreviousBtn = document.getElementById('reviews-previous-btn'),
    ReviewsNextBtn = document.getElementById('reviews-next-btn'),
    ReviewsSliderPrev = document.querySelector('.swiper-button-prev'),
    ReviewsSliderNext = document.querySelector('.swiper-button-next'),
    priceDurationToggler = document.querySelector('.price-duration-toggler'),
    priceMonthlyList = [].slice.call(document.querySelectorAll('.price-monthly')),
    priceYearlyList = [].slice.call(document.querySelectorAll('.price-yearly')),
    heroSlider = document.querySelector('.dd-hero-slider');

  // Hero
  const mediaQueryXL = '1200';
  const width = screen.width;
  if (width >= mediaQueryXL && heroAnimation) {
    heroAnimation.addEventListener('mousemove', function parallax(e) {
      animationElements.forEach(layer => {
        layer.style.transform = 'translateZ(1rem)';
      });
      animationImg.forEach(layer => {
        let x = (window.innerWidth - e.pageX * 2) / 100;
        let y = (window.innerHeight - e.pageY * 2) / 100;
        layer.style.transform = `perspective(1200px) rotateX(${y}deg) rotateY(${x}deg) scale3d(1, 1, 1)`;
      });
    });
    nav.addEventListener('mousemove', function parallax(e) {
      animationElements.forEach(layer => {
        layer.style.transform = 'translateZ(1rem)';
      });
      animationImg.forEach(layer => {
        let x = (window.innerWidth - e.pageX * 2) / 100;
        let y = (window.innerHeight - e.pageY * 2) / 100;
        layer.style.transform = `perspective(1200px) rotateX(${y}deg) rotateY(${x}deg) scale3d(1, 1, 1)`;
      });
    });

    heroAnimation.addEventListener('mouseout', function () {
      animationElements.forEach(layer => {
        layer.style.transform = 'translateZ(0)';
      });
      animationImg.forEach(layer => {
        layer.style.transform = 'perspective(1200px) scale(1) rotateX(0) rotateY(0)';
      });
    });
  }

  // swiper carousel
  // Customers reviews
  // -----------------------------------
  if (swiperReviews) {
    new Swiper(swiperReviews, {
      slidesPerView: 1,
      spaceBetween: 5,
      grabCursor: true,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false
      },
      loop: true,
      loopAdditionalSlides: 1,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev'
      },
      breakpoints: {
        1200: {
          slidesPerView: 3,
          spaceBetween: 26
        },
        992: {
          slidesPerView: 2,
          spaceBetween: 20
        }
      }
    });
  }

  // Sprint 1.5 Phase 4 — Hero Swiper (4 slides : map, terminal, dashboard, offices)
  // -----------------------------------
  // Brief Section 3.2 : fade crossfade · autoplay 6s · pause hover · pagination
  // dots minimalistes · côté gauche fixe (Swiper côté droit uniquement).
  if (heroSlider) {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    new Swiper(heroSlider, {
      loop: true,
      effect: 'fade',
      fadeEffect: { crossFade: true },
      speed: prefersReducedMotion ? 0 : 700,
      autoplay: prefersReducedMotion
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

  // Sprint 1.5 Phase 6 — Slide 2 Code Terminal typing animation
  // -----------------------------------
  // Custom JS typing (no TypeIt dependency, ~1KB gzipped). Triggered on
  // Swiper slideChange when realIndex === 1. Pauses autoplay during typing
  // to let the user see the completed code for the full 6s autoplay delay.
  // Second visit (loop) types 2x faster. prefers-reduced-motion -> static.
  const terminalCode = document.getElementById('dd-terminal-code');
  if (terminalCode && heroSlider) {
    // Tokens : {cls, text}. cls null = default text color.
    const codeTokens = [
      { cls: 'dd-syn-prompt', text: '$' },
      { text: ' ' },
      { cls: 'dd-syn-keyword', text: 'curl' },
      { text: ' -X ' },
      { cls: 'dd-syn-keyword', text: 'POST' },
      { text: ' \\\n    ' },
      { cls: 'dd-syn-string', text: 'https://api.dream-digital.info/v1/sms/send' },
      { text: ' \\\n    -H ' },
      { cls: 'dd-syn-string', text: '"Authorization: Bearer dd_..."' },
      { text: ' \\\n    -d \'{\n      ' },
      { cls: 'dd-syn-key', text: '"to"' },
      { text: ': ' },
      { cls: 'dd-syn-string', text: '"+243990000000"' },
      { text: ',\n      ' },
      { cls: 'dd-syn-key', text: '"from"' },
      { text: ': ' },
      { cls: 'dd-syn-string', text: '"DreamDigital"' },
      { text: ',\n      ' },
      { cls: 'dd-syn-key', text: '"text"' },
      { text: ': ' },
      { cls: 'dd-syn-string', text: '"Bienvenue !"' },
      { text: '\n    }\'\n\n' },
      { cls: 'dd-syn-status', text: 'HTTP/1.1 200 OK' },
      { text: '\n{\n  ' },
      { cls: 'dd-syn-key', text: '"id"' },
      { text: ': ' },
      { cls: 'dd-syn-fn', text: '"sms_a2b3c4d5"' },
      { text: ',\n  ' },
      { cls: 'dd-syn-key', text: '"status"' },
      { text: ': ' },
      { cls: 'dd-syn-string', text: '"delivered"' },
      { text: ',\n  ' },
      { cls: 'dd-syn-key', text: '"cost"' },
      { text: ': ' },
      { cls: 'dd-syn-num', text: '0.0089' },
      { text: ',\n  ' },
      { cls: 'dd-syn-key', text: '"to"' },
      { text: ': ' },
      { cls: 'dd-syn-string', text: '"+243990000000"' },
      { text: '\n}' }
    ];
    const totalChars = codeTokens.reduce(function (sum, t) {
      return sum + t.text.length;
    }, 0);
    const escapeHtml = function (s) {
      return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    };
    const renderTyped = function (charCount) {
      let html = '';
      let remaining = charCount;
      for (let i = 0; i < codeTokens.length; i++) {
        if (remaining <= 0) break;
        const tok = codeTokens[i];
        const slice = tok.text.slice(0, remaining);
        const safe = escapeHtml(slice);
        html += tok.cls ? '<span class="' + tok.cls + '">' + safe + '</span>' : safe;
        remaining -= slice.length;
      }
      return html;
    };
    const cursorHtml = '<span class="dd-terminal__cursor" aria-hidden="true">▮</span>';
    const fullHtml = renderTyped(totalChars);
    const prmTerminal = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let typingTimer = null;
    let seenCount = 0;

    const cancelTyping = function () {
      if (typingTimer !== null) {
        clearTimeout(typingTimer);
        typingTimer = null;
      }
    };
    const startTyping = function (swiperInstance) {
      cancelTyping();
      // ~28ms first visit, ~14ms on replay (2x faster — brief Phase 4 spec).
      const speed = seenCount === 0 ? 28 : 14;
      let charCount = 0;
      const terminalBody = terminalCode.parentElement;
      const tick = function () {
        charCount++;
        terminalCode.innerHTML = renderTyped(charCount) + cursorHtml;
        // Auto-scroll body so cursor stays in view if content overflows.
        if (terminalBody) terminalBody.scrollTop = terminalBody.scrollHeight;
        if (charCount >= totalChars) {
          seenCount++;
          // Resume autoplay 800ms after typing completes — gives the user a
          // beat to see the finished code before the 6s autoplay starts.
          typingTimer = setTimeout(function () {
            if (swiperInstance && swiperInstance.autoplay && swiperInstance.autoplay.start) {
              swiperInstance.autoplay.start();
            }
          }, 800);
          return;
        }
        typingTimer = setTimeout(tick, speed);
      };
      tick();
    };

    if (prmTerminal) {
      // Reduced motion : show the full code statically, no cursor, no typing.
      terminalCode.innerHTML = fullHtml;
    } else {
      // Defer until Swiper has attached itself to the heroSlider element.
      const attach = function () {
        const swiperInstance = heroSlider.swiper;
        if (!swiperInstance) {
          return;
        }
        swiperInstance.on('slideChange', function () {
          if (swiperInstance.realIndex === 1) {
            // Slide 2 active : pause autoplay then type.
            if (swiperInstance.autoplay && swiperInstance.autoplay.stop) {
              swiperInstance.autoplay.stop();
            }
            startTyping(swiperInstance);
          } else {
            // Left Slide 2 : abort any in-flight typing.
            cancelTyping();
          }
        });
      };
      // Swiper is instantiated synchronously above (Phase 4 block), so it
      // should already be attached. The microtask defer is purely defensive.
      if (heroSlider.swiper) {
        attach();
      } else {
        setTimeout(attach, 0);
      }
    }
  }

  // Sprint 1.5 Phase 7+8 — Slides 3 & 4 stagger animations
  // -----------------------------------
  // Generic stagger fade-up trigger : when slide with given realIndex becomes
  // active, toggle .is-visible on the target. CSS transitions per child
  // (delays 0/100/200ms) produce the stagger. Class is removed on leaving so
  // animation replays on each return. prefers-reduced-motion short-circuits
  // to permanent .is-visible (transitions already neutralised by SCSS guard).
  const staggerSlides = [
    { selector: '[data-dashboard-target]', realIndex: 2 }, // Phase 7 dashboard
    { selector: '[data-offices-target]',   realIndex: 3 }  // Phase 8 offices
  ];
  const prmStagger = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  staggerSlides.forEach(function (cfg) {
    const target = document.querySelector(cfg.selector);
    if (!target || !heroSlider) return;
    if (prmStagger) {
      target.classList.add('is-visible');
      return;
    }
    const attachStagger = function () {
      const swiperInstance = heroSlider.swiper;
      if (!swiperInstance) return;
      swiperInstance.on('slideChange', function () {
        if (swiperInstance.realIndex === cfg.realIndex) {
          target.classList.add('is-visible');
        } else {
          target.classList.remove('is-visible');
        }
      });
      if (swiperInstance.realIndex === cfg.realIndex) {
        target.classList.add('is-visible');
      }
    };
    if (heroSlider.swiper) {
      attachStagger();
    } else {
      setTimeout(attachStagger, 0);
    }
  });

  // Reviews slider next and previous
  // -----------------------------------
  // Add click event listener to next button
  ReviewsNextBtn.addEventListener('click', function () {
    ReviewsSliderNext.click();
  });
  ReviewsPreviousBtn.addEventListener('click', function () {
    ReviewsSliderPrev.click();
  });

  // Review client logo
  // -----------------------------------
  if (swiperLogos) {
    new Swiper(swiperLogos, {
      slidesPerView: 2,
      autoplay: {
        delay: 3000,
        disableOnInteraction: false
      },
      breakpoints: {
        992: {
          slidesPerView: 5
        },
        768: {
          slidesPerView: 3
        }
      }
    });
  }

  // Pricing Plans
  // -----------------------------------
  document.addEventListener('DOMContentLoaded', function (event) {
    function togglePrice() {
      if (priceDurationToggler.checked) {
        // If checked
        priceYearlyList.map(function (yearEl) {
          yearEl.classList.remove('d-none');
        });
        priceMonthlyList.map(function (monthEl) {
          monthEl.classList.add('d-none');
        });
      } else {
        // If not checked
        priceYearlyList.map(function (yearEl) {
          yearEl.classList.add('d-none');
        });
        priceMonthlyList.map(function (monthEl) {
          monthEl.classList.remove('d-none');
        });
      }
    }
    // togglePrice Event Listener
    togglePrice();

    priceDurationToggler.onchange = function () {
      togglePrice();
    };
  });
})();
