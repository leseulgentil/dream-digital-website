# Code review — dream-digital-website SPRINT-1-5 redesign
Date: 2026-05-08
Branch: feature/sprint-1-5-redesign
Scope: 5 derniers commits (c744e04 · 5a52e8d · a8b2589 · 2aaeb59 · ad3174e)
Méthodologie: PaulRBerg code-review (défauts à impact élevé, avec preuves)

Fichiers audités : `config/dream-digital/*.php` (7), `_custom-variables/_dream-digital.scss`,
`dream-digital/_dd-code-blocks.scss`, `design-tokens.blade.php` (lecture seule),
`routes/web.php:185-188`, `_dream-digital-tokens.scss` (racine), `vite.config.js`.

---

## Findings

### [HIGH] Route `/preview/design-tokens` publique — aucune protection d'accès

**Sévérité :** HIGH
**Localisation :** `routes/web.php:185-188`
**Problème :** Route enregistrée sans middleware (`auth`, IP whitelist, guard d'environnement).
Accessible en production : expose le commit SHA Git courant, toute l'architecture de design
tokens interne, les noms de fichiers SCSS privés. Le commentaire « internal dev page, remove
before prod » est une intention sans enforcement.

**Preuve :**
```php
Route::get('/preview/design-tokens', function () {
    return view('preview.design-tokens');
})->name('preview.design-tokens');
```

**Correction :** Ajouter `->middleware('auth')` en attente de suppression, ou conditionner avec
`abort_unless(app()->environment('local', 'staging'), 404)` dans la closure.

---

### [HIGH] `@keyframes dd-pulse` infinie — aucun `prefers-reduced-motion`

**Sévérité :** HIGH
**Localisation :** `_custom-variables/_dream-digital.scss:282-291` · `design-tokens.blade.php:182`
**Problème :** `dd-pulse 2s ease-out infinite` est une animation déclenchée automatiquement, en
boucle perpétuelle. Aucun `@media (prefers-reduced-motion: reduce)` n'existe dans l'ensemble du
repo (grep exhaustif). Ce keyframe sera utilisé pour les signal indicators live de la home
Sprint 1.5 (brief §4.2.a). WCAG 2.1 SC 2.2.2 impose qu'une telle animation soit contrôlable.
`dd-fade-up`, futur scroll-trigger en production, est dans le même cas.

**Preuve :**
```scss
@keyframes dd-pulse { /* _dream-digital.scss:282 */
  0%   { transform: scale(1);   opacity: 0.6; }
  100% { transform: scale(2.5); opacity: 0;   }
}
/* design-tokens.blade.php:182 */
animation: dd-pulse 2s ease-out infinite;
```

**Correction :** Ajouter dans `_dream-digital.scss` (ou dans chaque composant consommateur) :
```scss
@media (prefers-reduced-motion: reduce) {
  *[class*="dd-"], *[class*="dtp-"] {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
  }
}
```
Variante ciblée préférable sur les composants production (évite la règle universelle).

---

### [MED] `@keyframes dd-hover-lift` — dead code avec `box-shadow` non-compositable

**Sévérité :** MEDIUM
**Localisation :** `_custom-variables/_dream-digital.scss:306-317`
**Problème :** Le `@keyframes dd-hover-lift` est déclaré mais n'est référencé par aucune
propriété `animation:` dans tout le codebase. La démo Blade utilise correctement une
`transition: all 240ms` CSS à la place. Problème double : (1) dead code dans tous les bundles
CSS compilés, (2) si utilisé tel quel, `box-shadow` dans un `@keyframes` déclenche **paint**
à chaque frame (non GPU-compositable) — dégradation visible sur mobiles bas de gamme
(cible Afrique subsaharienne).

**Preuve :**
```scss
@keyframes dd-hover-lift {
  0%   { transform: translateY(0);    box-shadow: 0 1px 2px ...; }
  100% { transform: translateY(-4px); box-shadow: 0 12px 32px ...; }
}
```

**Correction :** Supprimer `@keyframes dd-hover-lift`. Conserver la `transition` CSS telle
qu'implémentée dans la Blade demo. Si un effet élévation est nécessaire, utiliser
`transition: transform 240ms, box-shadow 240ms` — deux propriétés séparées.

---

### [MED] Texte `en` identique au `fr` dans les configs CMS-ready

**Sévérité :** MEDIUM
**Localisation :** `config/dream-digital/site.php:25-26,36-44,52-53` · `coverage.php:25-26`
**Problème :** Les clés `'en'` de `sub_headline`, `pitch.paragraphs[0-1]`, `transition_cta.text`
et `coverage.global.description` contiennent du texte français. Quand un template Blade lira
`config('dream-digital.site.sub_headline.en')`, il servira du français aux visiteurs EN.
Aucun fallback implicite dans l'architecture config Laravel.

**Correction :** Renseigner les vrais textes EN, ou documenter l'état provisoire avec un
pattern explicite : `'en' => null` + guard Blade `config(... .en) ?? config(... .fr)`.

---

### [MED] `_dream-digital-tokens.scss` racine — copie orpheline divergente

**Sévérité :** MEDIUM
**Localisation :** `_dream-digital-tokens.scss` (racine, 282 lignes) vs
`_custom-variables/_dream-digital.scss` (337 lignes, version active)
**Problème :** La copie racine est la v1.2 originale du Brand Kit (sans le `@import
"../dream-digital/dd-code-blocks"` et sans les keyframes Étape 2). Un développeur futur
modifiant la racine croira agir sur les tokens actifs — drift silencieux.

**Correction :** Soit supprimer `_dream-digital-tokens.scss` de la racine et mettre à jour
`BRAND_KIT_DREAM_DIGITAL.md` pour pointer vers `_custom-variables/`, soit ajouter en tête :
`// ARCHIVE — v1.2 figée. Source active : resources/assets/vendor/scss/_custom-variables/_dream-digital.scss`.

---

### [MED] Clés `null` sans contrat d'usage Blade documenté

**Sévérité :** MEDIUM
**Localisation :** `config/dream-digital/site.php:67-75`
**Problème :** `email_support`, `phone`, `social.*`, `company.legal_name`, `meta.og_image`
sont `null`. Si un template Blade émet directement `<a href="tel:{{ config('...phone') }}">`,
le résultat est `<a href="tel:">` — lien brisé silencieux. Le pattern guard (`?? ''`) n'est
pas documenté ni illustré dans les configs.

**Correction :** Ajouter un commentaire de convention dans chaque config concernée :
```php
// Guard Blade : {{ config('dream-digital.site.contact.phone') ?? '' }}
// Ou : @if(config('dream-digital.site.contact.phone'))
```

---

## Ce qui a été vérifié et est OK

- **Architecture SCSS Q21** : chaîne d'injection correcte — `_custom-variables/_bootstrap-extended.scss` → `@import "dream-digital"` positionné entre `functions` et `variables` Bootstrap. Conforme jurisprudence Q21.
- **Import `dd-code-blocks`** : chemin `@import "../dream-digital/dd-code-blocks"` depuis `_custom-variables/` résout vers `dream-digital/_dd-code-blocks.scss`. Sass résout le préfixe `_` automatiquement. Pas de fichier manquant.
- **Animations GPU** : `dd-pulse` (`transform` + `opacity`) et `dd-fade-up` (`translateY` + `opacity`) utilisent uniquement des propriétés GPU-compositables. Zéro `will-change` superflu dans les nouveaux fichiers.
- **Keyframes préfixés `dd-`** : zéro collision avec Sneat/Bootstrap. Conforme convention Q14.
- **Zéro résidus Sneat** dans les 7 configs CMS et la page preview (`app-brand`, `layout-*`, `bg-*-theme`, `template-customizer` — absents).
- **Zéro `{!! !!}` dans la page preview** : tous les outputs utilisent `{{ }}`. `$gitSha` lu depuis `.git/HEAD` interne, non user-controlled. Pas de vecteur XSS dans les nouvelles vues.
- **Code-blocks XSS** : snippets de code en section 6 = littéraux Blade statiques. Aucune variable user-controlled.
- **`noindex`** : `commonMaster.blade.php:82` émet `noindex,nofollow` hardcodé. La page preview via `blankLayout` → `commonMaster` est correctement noindexée.
- **Vite config** : `sourcemap: false`, `minify: esbuild`, hashes explicites, `target: es2020`. Conforme S8. Aucune dépendance nouvelle introduite.
- **Structure CMS** : 7 fichiers cohérents, clés `id/slug/order/active` sur chaque item facilitent la migration Eloquent. `partners.show_section: false` correct (logos non fournis).
- **Tagline bilingue** : `'fr' => 'Voice. SMS. eSIM. And More.'` = `'en'` = intentionnel (tagline EN validé comme marque, ANALYZE C-ter Q11).

---

## Nits stylistiques (hors rapport principal)

- `design-tokens.blade.php:309` : `{{ now()->format('Y-m-d H:i') }}` expose l'heure serveur dans le HTML source. Bénin sur page interne ; à retirer avant tout usage public.
- `services.php` et `industries.php` : champs `'en'` aussi en français — même cause que finding MED-4.
- `_dd-code-blocks.scss:30` : `$dd-code-cursor: $dd-code-fn` crée une dépendance implicite entre tokens sémantiques différents — fragile si `$dd-code-fn` change pour raisons typographiques.

---

## Questions ouvertes pour relecture humaine

1. **Cookie `admin-primaryColor` → injection CSS** (pré-existant, hors scope) : `Helpers::generatePrimaryColorCSS()` interpole la valeur du cookie sans validation regex hex dans `commonMaster.blade.php:102` via `{!! !!}`. Mérite un ticket sécurité avant mise en production publique.
2. **Suppression de la route preview** : le commentaire `// remove before prod` n'a pas de guard technique. Confirmer si un ticket backlog Sprint 1.5 existe explicitement pour cette suppression, ou si le middleware `auth` doit être ajouté maintenant.
3. **Traductions EN** : les champs `'en'` vides/FR sont-ils intentionnellement en attente (site FR-only pour l'instant) ou doivent-ils être remplis avant l'Étape 3 de Sprint 1.5 ?
