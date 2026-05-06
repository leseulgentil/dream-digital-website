/**
 * Dream Digital — Theme Switcher (Q13)
 *
 * Standalone Light/Dark/System theme switcher, decoupled from the removed
 * Sneat Template Customizer (S6). Persists user preference in
 * localStorage['dd-theme'] and applies it via the data-bs-theme attribute
 * on <html> (Bootstrap 5.3+ native dark mode).
 *
 * Companion to the anti-FOUC inline script in commonMaster.blade.php which
 * applies the resolved theme synchronously before any CSS loads.
 */

'use strict';

(function () {
  const STORAGE_KEY = 'dd-theme';
  const VALID_THEMES = ['light', 'dark', 'system'];

  // ---------------------------------------------------------------------------
  // Core helpers
  // ---------------------------------------------------------------------------

  function getStoredTheme() {
    const value = localStorage.getItem(STORAGE_KEY);
    return VALID_THEMES.includes(value) ? value : 'system';
  }

  function setStoredTheme(theme) {
    if (!VALID_THEMES.includes(theme)) return;
    localStorage.setItem(STORAGE_KEY, theme);
  }

  function resolveTheme(theme) {
    if (theme === 'system') {
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    return theme;
  }

  function applyTheme(theme) {
    const resolved = resolveTheme(theme);
    document.documentElement.setAttribute('data-bs-theme', resolved);
  }

  // Active icon mapping (Tabler-equivalent class names from Boxicons set used by the template)
  const THEME_ICON = {
    light: 'bx-sun',
    dark: 'bx-moon',
    system: 'bx-desktop'
  };

  function updateActiveIcon(theme) {
    const switcher = document.querySelector('.dropdown-style-switcher');
    if (!switcher) return;
    const icon = switcher.querySelector('.theme-icon-active');
    if (!icon) return;

    // Strip previous bx-* and apply the one for current theme
    const cls = Array.from(icon.classList).filter(c => !c.startsWith('bx-') || c === 'bx');
    icon.className = cls.join(' ') + ' ' + THEME_ICON[theme];

    // Mark active dropdown item
    switcher.querySelectorAll('.dropdown-item[data-theme]').forEach(item => {
      const isActive = item.getAttribute('data-theme') === theme;
      item.classList.toggle('active', isActive);
      item.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  // ---------------------------------------------------------------------------
  // Wire up dropdown clicks + system listener
  // ---------------------------------------------------------------------------

  function bindClickHandlers() {
    document.querySelectorAll('.dropdown-style-switcher .dropdown-item[data-theme]').forEach(item => {
      item.addEventListener('click', function (event) {
        event.preventDefault();
        const theme = this.getAttribute('data-theme');
        if (!VALID_THEMES.includes(theme)) return;
        setStoredTheme(theme);
        applyTheme(theme);
        updateActiveIcon(theme);
      });
    });
  }

  function bindSystemPrefersListener() {
    const media = window.matchMedia('(prefers-color-scheme: dark)');
    const handler = () => {
      if (getStoredTheme() === 'system') {
        applyTheme('system');
      }
    };
    if (typeof media.addEventListener === 'function') {
      media.addEventListener('change', handler);
    } else if (typeof media.addListener === 'function') {
      // Safari < 14 fallback
      media.addListener(handler);
    }
  }

  // ---------------------------------------------------------------------------
  // One-time silent purge of legacy Template Customizer localStorage keys
  // (S6 removed the customizer; users returning post-S6 may still carry stale
  // keys from earlier visits — clean them up to keep localStorage tidy).
  // ---------------------------------------------------------------------------

  function purgeLegacyKeys() {
    try {
      const legacySuffixes = [
        '--Theme', '--Style', '--Layout', '--LayoutCollapsed',
        '--SemiDark', '--Rtl', '--ShowDropdownOnHover'
      ];
      const keysToRemove = [];
      for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('templateCustomizer-')) {
          // Be lenient: match any legacy suffix or just the prefix
          if (legacySuffixes.some(s => key.endsWith(s)) || key.startsWith('templateCustomizer-')) {
            keysToRemove.push(key);
          }
        }
      }
      keysToRemove.forEach(k => localStorage.removeItem(k));
    } catch (e) {
      // localStorage may be disabled in some privacy modes — ignore silently.
    }
  }

  // ---------------------------------------------------------------------------
  // Init
  // ---------------------------------------------------------------------------

  function init() {
    purgeLegacyKeys();

    const theme = getStoredTheme();
    // Anti-FOUC inline script already applied this; re-asserting is harmless
    // and ensures consistency if some intermediate code touched data-bs-theme.
    applyTheme(theme);
    updateActiveIcon(theme);

    bindClickHandlers();
    bindSystemPrefersListener();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
