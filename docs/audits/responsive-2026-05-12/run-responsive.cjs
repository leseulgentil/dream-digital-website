// Capture les screenshots aux 5 breakpoints + collecte les messages console
// Usage : node docs/audits/responsive-2026-05-12/run-responsive.js
// Requiert : node_modules/puppeteer-core + Chrome installe localement
const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer-core');
const { Launcher } = require('chrome-launcher');

const BREAKPOINTS = [
  { w: 375,  h: 812,  label: '375-mobile' },
  { w: 768,  h: 1024, label: '768-tablet' },
  { w: 1024, h: 768,  label: '1024-tablet-landscape' },
  { w: 1440, h: 900,  label: '1440-desktop' },
  { w: 1920, h: 1080, label: '1920-large' },
];

const PAGES = [
  { url: '/fr',                     slug: 'fr' },
  { url: '/fr/products',            slug: 'fr-products' },
  { url: '/fr/products/sms-a2p',    slug: 'fr-products-sms-a2p' },
  { url: '/fr/coverage',            slug: 'fr-coverage' },
  { url: '/fr/pricing',             slug: 'fr-pricing' },
  { url: '/fr/contact',             slug: 'fr-contact' },
];

const BASE = 'http://127.0.0.1:8888';
const OUT = path.join(__dirname, '.');

(async () => {
  const chromePath = Launcher.getInstallations()[0];
  if (!chromePath) { console.error('Chrome not found'); process.exit(1); }
  console.log('Using Chrome:', chromePath);

  const browser = await puppeteer.launch({
    executablePath: chromePath,
    headless: 'new',
    args: ['--disable-gpu', '--no-sandbox'],
  });

  const consoleLog = {};
  const overflowLog = {};

  for (const page of PAGES) {
    consoleLog[page.url] = [];
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
        // Mesure overflow horizontal
        const overflow = await tab.evaluate(() => {
          const doc = document.documentElement;
          const body = document.body;
          return {
            scrollWidth: doc.scrollWidth,
            clientWidth: doc.clientWidth,
            bodyScrollWidth: body.scrollWidth,
            hasHOverflow: doc.scrollWidth > doc.clientWidth + 1,
            innerWidth: window.innerWidth,
          };
        });
        overflowLog[page.url][bp.label] = overflow;
        const file = path.join(OUT, `${page.slug}__${bp.label}.png`);
        await tab.screenshot({ path: file, fullPage: true });
        console.log(`[ok] ${page.url} @ ${bp.label}  overflow=${overflow.hasHOverflow}  ${overflow.scrollWidth}/${overflow.clientWidth}`);
      } catch (e) {
        console.log(`[err] ${page.url} @ ${bp.label}  ${e.message}`);
        overflowLog[page.url][bp.label] = { error: e.message };
      }
      consoleLog[page.url].push(...messages.map(m => ({ ...m, bp: bp.label })));
      await ctx.close();
    }
  }

  fs.writeFileSync(path.join(OUT, '_console.json'), JSON.stringify(consoleLog, null, 2));
  fs.writeFileSync(path.join(OUT, '_overflow.json'), JSON.stringify(overflowLog, null, 2));

  // Synthese overflow
  const overflowSummary = [];
  for (const url in overflowLog) {
    for (const bp in overflowLog[url]) {
      const o = overflowLog[url][bp];
      if (o.hasHOverflow) overflowSummary.push(`${url} @ ${bp}: scroll=${o.scrollWidth}px client=${o.clientWidth}px (OVERFLOW +${o.scrollWidth - o.clientWidth}px)`);
    }
  }
  if (overflowSummary.length) {
    console.log('\n=== OVERFLOW DETECTED ===');
    overflowSummary.forEach(s => console.log('  ' + s));
  } else {
    console.log('\n=== NO HORIZONTAL OVERFLOW DETECTED ===');
  }

  // Synthese console
  let totalErrors = 0;
  console.log('\n=== CONSOLE MESSAGES (errors + warnings + failed requests, excl. fonts/favicon) ===');
  for (const url in consoleLog) {
    const msgs = consoleLog[url];
    if (msgs.length === 0) continue;
    // Dedup messages (same text different breakpoints)
    const dedup = {};
    msgs.forEach(m => { dedup[m.t + '|' + m.text] = (dedup[m.t + '|' + m.text] || 0) + 1; });
    console.log(`\n  ${url}:`);
    for (const k in dedup) {
      const [t, ...rest] = k.split('|');
      console.log(`    [${t} x${dedup[k]}] ${rest.join('|').slice(0, 180)}`);
      totalErrors++;
    }
  }
  console.log(`\nTotal unique console issues: ${totalErrors}`);

  await browser.close();
})();
