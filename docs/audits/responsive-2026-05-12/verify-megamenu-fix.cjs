// Capture MegaMenu ouvert pour valider largeurs panels post-fix
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const { Launcher } = require('chrome-launcher');

(async () => {
  const chromePath = Launcher.getInstallations()[0];
  const browser = await puppeteer.launch({
    executablePath: chromePath,
    headless: 'new',
    args: ['--disable-gpu', '--no-sandbox'],
  });

  const checks = [
    { url: 'http://127.0.0.1:8888/fr', toggleLabel: 'Produits', file: 'megamenu-fix__produits-1440.png' },
    { url: 'http://127.0.0.1:8888/fr', toggleLabel: 'Developers', file: 'megamenu-fix__developers-1440.png' },
    { url: 'http://127.0.0.1:8888/fr', toggleLabel: 'Solutions', file: 'megamenu-fix__solutions-1440.png' },
    { url: 'http://127.0.0.1:8888/fr', toggleLabel: 'Societe', file: 'megamenu-fix__societe-1440.png' },
    { url: 'http://127.0.0.1:8888/fr/company', toggleLabel: null, file: 'navbar-fix__fr-company-1440.png' },
  ];

  for (const c of checks) {
    const ctx = await browser.createBrowserContext();
    const tab = await ctx.newPage();
    await tab.setViewport({ width: 1440, height: 900, deviceScaleFactor: 1 });
    await tab.goto(c.url, { waitUntil: 'networkidle2', timeout: 30000 });

    if (c.toggleLabel) {
      // Hover the dropdown toggle, then wait for panel to be visible
      const found = await tab.evaluate((label) => {
        const toggle = [...document.querySelectorAll('.dd-megamenu-item .nav-link.dropdown-toggle')]
          .find(el => el.textContent.trim() === label);
        if (!toggle) return false;
        const rect = toggle.getBoundingClientRect();
        const evt = new MouseEvent('mouseenter', { bubbles: true });
        toggle.dispatchEvent(evt);
        // Force display via CSS hover: we rely on the CSS :hover rule on .dd-megamenu-item
        // For headless, we add an inline class to force open
        toggle.closest('.dd-megamenu-item').classList.add('show');
        const panel = toggle.nextElementSibling;
        if (panel) panel.classList.add('show');
        return true;
      }, c.toggleLabel);

      if (!found) {
        console.log(`[skip] ${c.toggleLabel} toggle not found`);
        await ctx.close();
        continue;
      }
      await new Promise(r => setTimeout(r, 300));
    }

    await tab.screenshot({
      path: path.join(__dirname, c.file),
      fullPage: false,
      clip: { x: 0, y: 0, width: 1440, height: 600 },
    });
    console.log(`[ok] ${c.file}`);
    await ctx.close();
  }

  await browser.close();
})();
