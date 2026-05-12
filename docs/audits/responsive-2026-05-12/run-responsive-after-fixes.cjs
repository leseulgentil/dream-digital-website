// Re-screenshots cibles apres sprint correctif (commits ce416b8 -> 275fe8a)
// Pages : /fr (home) + /fr/products/sms-a2p (produit type)
// Breakpoints : 375 (mobile), 1024 (tablet landscape critique), 1440 (desktop)
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const { Launcher } = require('chrome-launcher');

const BREAKPOINTS = [
  { w: 375,  h: 812,  label: '375-mobile' },
  { w: 1024, h: 768,  label: '1024-tablet-landscape' },
  { w: 1440, h: 900,  label: '1440-desktop' },
];

const PAGES = [
  { url: '/fr',                  slug: 'fr-AFTERFIX' },
  { url: '/fr/products/sms-a2p', slug: 'fr-products-sms-a2p-AFTERFIX' },
];

const BASE = 'http://127.0.0.1:8888';
const OUT = path.join(__dirname, '.');

(async () => {
  const chromePath = Launcher.getInstallations()[0];
  if (!chromePath) { console.error('Chrome not found'); process.exit(1); }

  const browser = await puppeteer.launch({
    executablePath: chromePath,
    headless: 'new',
    args: ['--disable-gpu', '--no-sandbox'],
  });

  const console_messages = {};
  const overflowLog = {};

  for (const page of PAGES) {
    console_messages[page.url] = [];
    overflowLog[page.url] = {};
    for (const bp of BREAKPOINTS) {
      const ctx = await browser.createBrowserContext();
      const tab = await ctx.newPage();
      await tab.setViewport({ width: bp.w, height: bp.h, deviceScaleFactor: 1 });
      const messages = [];
      tab.on('console', m => { if (m.type() === 'error' || m.type() === 'warning') messages.push({ t: m.type(), text: m.text() }); });
      tab.on('pageerror', e => messages.push({ t: 'pageerror', text: e.message }));
      tab.on('requestfailed', r => {
        if (!/favicon|fonts\.gstatic|fonts\.googleapis/.test(r.url())) {
          messages.push({ t: 'requestfailed', text: r.url() + ' -> ' + r.failure()?.errorText });
        }
      });
      try {
        await tab.goto(BASE + page.url, { waitUntil: 'networkidle2', timeout: 30000 });
        const overflow = await tab.evaluate(() => {
          const doc = document.documentElement;
          return {
            scrollWidth: doc.scrollWidth,
            clientWidth: doc.clientWidth,
            hasHOverflow: doc.scrollWidth > doc.clientWidth + 1,
          };
        });
        overflowLog[page.url][bp.label] = overflow;
        const file = path.join(OUT, `${page.slug}__${bp.label}.png`);
        await tab.screenshot({ path: file, fullPage: true });
        console.log(`[ok] ${page.url} @ ${bp.label}  overflow=${overflow.hasHOverflow}  ${overflow.scrollWidth}/${overflow.clientWidth}`);
      } catch (e) {
        console.log(`[err] ${page.url} @ ${bp.label}  ${e.message}`);
      }
      console_messages[page.url].push(...messages.map(m => ({ ...m, bp: bp.label })));
      await ctx.close();
    }
  }

  fs.writeFileSync(path.join(OUT, '_console-after-fix.json'), JSON.stringify(console_messages, null, 2));
  fs.writeFileSync(path.join(OUT, '_overflow-after-fix.json'), JSON.stringify(overflowLog, null, 2));

  let totalErrors = 0;
  for (const url in console_messages) {
    const dedup = {};
    console_messages[url].forEach(m => { dedup[m.t + '|' + m.text] = (dedup[m.t + '|' + m.text] || 0) + 1; });
    if (Object.keys(dedup).length === 0) continue;
    console.log(`\n${url}:`);
    for (const k in dedup) {
      const [t, ...rest] = k.split('|');
      console.log(`  [${t} x${dedup[k]}] ${rest.join('|').slice(0, 160)}`);
      totalErrors++;
    }
  }
  console.log(`\nTotal unique console issues: ${totalErrors}`);

  await browser.close();
})();
