# Dream Digital — Corporate website & client portal

> **Voice. SMS. eSIM. And More.**

Dream Digital is a programmable telecom operator (CPaaS/ITSP) headquartered in Kinshasa (DRC), with operational offices in Abidjan (Côte d'Ivoire) and Brazzaville (Congo). Coverage : 200+ countries.

This repository hosts the corporate website (public marketing site) and the upcoming client portal (self-service SMS, account management) and admin backoffice (CRM, pricing, RBAC).

---

## Stack

- **Backend** : Laravel 12 / PHP 8.4
- **Database** : SQLite (development) — MySQL 8 or PostgreSQL 16 in production
- **Frontend** : Bootstrap 5.3 + Vite 6 + Sass + Blade templates
- **Auth** : Jetstream (planned for client portal)
- **i18n** : French (default) and English — to be activated in Sprint 1

## Local setup (Windows / WAMP / PowerShell)

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev                         # Vite dev server (http://127.0.0.1:5174)
php artisan serve --port=8888       # Laravel dev server (http://127.0.0.1:8888)
```

Open `http://127.0.0.1:8888` in your browser.

## Repository layout

| Path | Role |
|---|---|
| `app/Http/Controllers/` | HTTP controllers |
| `resources/views/` | Blade templates |
| `resources/assets/vendor/scss/` | SCSS sources compiled by Vite |
| `resources/menu/` | Navigation JSON definitions |
| `routes/web.php` | Public + admin routes |
| `public/img/brand/` | Official Dream Digital logos (populated in Sprint 0 / S5) |
| `_template-license/` | Local-only proof of purchase for the Sneat template (gitignored) |

## Internal documentation

- [BRAND_KIT_DREAM_DIGITAL.md](./BRAND_KIT_DREAM_DIGITAL.md) — official visual reference (v1.2, validated 2026-05-05)
- [BRANDING.md](./BRANDING.md) — developer-oriented branding cheatsheet
- [SECURITY.md](./SECURITY.md) — secrets management and incident response
- [ANALYZE_DESANONYMIZATION.md](./ANALYZE_DESANONYMIZATION.md) — Sprint 0 working analysis (Sneat desanonymization)

## Sprint roadmap

| Sprint | Status | Focus |
|---|---|---|
| **Sprint 0** | In progress | Desanonymization of the underlying Sneat template (branch `feature/desanonymization`) |
| **Sprint 1.5** | Planned | Visual redesign aligned with Brand Kit v1.2 |
| **Sprint 1** | Planned | Multi-country foundations + FR/EN i18n |
| **Sprint 2+** | TBD | Client portal (SMS self-service), admin backoffice (pricing per country, RBAC) |

## License

Proprietary code © Dream Digital SARL. All rights reserved.

The underlying Sneat Dashboard PRO Laravel template is licensed under a regular commercial license from ThemeSelection. Proof of purchase is kept locally in `_template-license/` (gitignored). The desanonymization work documented in `BRIEF_DD_DESANONYMIZATION.md` is a standard customization of a paid template, not a license circumvention.

---

© Dream Digital SARL — Kinshasa · Abidjan · Brazzaville
