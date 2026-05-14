import { test, expect } from '@playwright/test';

const baseURL = process.env.DD_QA_BASE_URL || 'http://127.0.0.1:8899';
const adminEmail = process.env.DD_QA_ADMIN_EMAIL || 'codex.qa@dream-digital.local';
const adminPassword = process.env.DD_QA_ADMIN_PASSWORD || 'CodexQa2026!';

test.describe('Dream Digital visual QA smoke', () => {
  test.setTimeout(90000);

  const gotoPublic = async (page, route) => {
    await page.goto(`${baseURL}${route}`, { waitUntil: 'commit', timeout: 15000 });
    await page.locator('body').waitFor({ state: 'visible', timeout: 10000 });
    await page.waitForTimeout(250);
  };

  test('public pages stay inside viewport and render hero media', async ({ page }) => {
    const routes = ['/fr', '/fr/products', '/fr/pricing', '/fr/coverage', '/fr/contact', '/fr/blog'];
    const viewports = [
      { width: 1440, height: 900 },
      { width: 390, height: 844 }
    ];

    for (const viewport of viewports) {
      await page.setViewportSize(viewport);

      for (const route of routes) {
        await gotoPublic(page, route);
        const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
        expect(overflow, `${route} overflows at ${viewport.width}px`).toBeLessThanOrEqual(2);
      }
    }

    await page.setViewportSize({ width: 390, height: 844 });
    await gotoPublic(page, '/fr/products');
    await expect(page.locator('.dd-page-hero__media img')).toBeVisible();

    await gotoPublic(page, '/fr/blog');
    await expect(page.locator('.dd-blog-hero h1')).toBeVisible();
  });

  test('public menu works on desktop and mobile', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto(`${baseURL}/fr`, { waitUntil: 'networkidle' });

    await expect(page.locator('nav').first()).toContainText('Blog');
    await page.getByRole('link', { name: 'Blog' }).first().click();
    await expect(page).toHaveURL(/\/fr\/blog$/);
    await expect(page.locator('h1')).toContainText(/Blog/i);

    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(`${baseURL}/fr`, { waitUntil: 'networkidle' });
    await page.getByRole('button', { name: /ouvrir la navigation/i }).click();
    await expect(page.locator('#ddFrontNav')).toBeVisible();
    await expect(page.locator('#ddFrontNav')).toContainText('Blog');
  });

  test('admin navigation, generator modal and wysiwyg are clickable', async ({ page }) => {
    await page.goto(`${baseURL}/login`, { waitUntil: 'networkidle' });
    await page.getByLabel(/email/i).fill(adminEmail);
    await page.getByLabel(/password|mot de passe/i).fill(adminPassword);
    await page.getByRole('button', { name: /log in|login|connexion|se connecter/i }).click();
    await expect(page).toHaveURL(/\/admin/);

    await page.goto(`${baseURL}/admin/navigation`, { waitUntil: 'networkidle' });
    await expect(page.locator('h1')).toContainText('Navigation principale');
    await expect(page.locator('body')).toContainText('/{locale}/blog');
    await page.getByRole('link', { name: /nouveau lien/i }).click();
    await page.getByRole('button', { name: /Blog/i }).first().click();
    await expect(page.locator('#url')).toHaveValue('/{locale}/blog');

    await page.goto(`${baseURL}/admin/pages/create`, { waitUntil: 'networkidle' });
    await expect(page.locator('#sections_rich_editor')).toBeVisible();
    await page.getByRole('button', { name: /generate article/i }).click();
    await page.locator('#article_generator_idea').fill('SMS A2P pour fintechs africaines');
    await page.locator('#article_generator_keywords').fill('SMS A2P, OTP, fintech, CPaaS');
    await page.locator('#article_generator_guidelines').fill('Ton expert, SEO, appel a la conversion');
    await page.locator('#article_generator_variants').fill('2');
    await page.locator('#article_generator_submit').click();
    await expect(page.locator('#article_generator_status')).toContainText(/article\(s\) genere\(s\)\.|Erreur pendant la generation\./, { timeout: 60000 });
    await expect(page.locator('#article_generator_status')).not.toContainText('Erreur pendant la generation.');
    await expect(page.locator('#article_generator_results .btn-primary').first()).toBeVisible();
    await page.locator('#article_generator_results .btn-primary').first().click();
    await expect(page.locator('#title')).toHaveValue(/SMS A2P/);
    await expect(page.locator('#sections_json')).toHaveValue(/body_html/);

    await page.locator('#sections_rich_editor .ql-editor').click();
    await page.keyboard.press('Control+End');
    await page.keyboard.type(' Note QA navigateur.');
    await expect(page.locator('#sections_rich_editor .ql-editor')).toContainText('Note QA navigateur.');
  });
});
