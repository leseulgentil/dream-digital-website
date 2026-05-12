# DESIGN_DECISIONS — Sprint 1.5 (Redesign vitrine ITSP/CPaaS)

> **Sprint** : 1.5 — Redesign vitrine ITSP/CPaaS
> **Branche** : `feature/sprint-1-5-redesign`
> **Brief de référence** : `BRIEF_SPRINT_1_5_REDESIGN.md`
> **Analyse préalable** : `ANALYZE_SPRINT_1_5.md`
> **Étude comparative** : `DESIGN_REFERENCES.md` (commit `2459db3`)
> **Date de clôture critères §6** : 2026-05-12
> **PO** : MAPENDO Gentil

---

## Préambule

Ce document consolide toutes les décisions de design qui ont structuré
le Sprint 1.5. Il satisfait le critère d'acceptance **§6.1** du brief
(*"`DESIGN_DECISIONS.md` documente les choix de design effectués"*).

La logique d'arbitrage est documentée dans :
- **`DESIGN_REFERENCES.md`** — étude des 6 sites de référence
  (Twilio, Plivo, Telnyx, Sinch, Bandwidth, Bird) + 3 directions
  visuelles A/B/C proposées
- **`ANALYZE_SPRINT_1_5.md`** — questions Q1-Q5 arbitrées par le PO
  le 2026-05-08
- **`BRAND_KIT_DREAM_DIGITAL.md`** v1.2 — source de vérité visuelle
  (palette, typographie, iconographie, tone of voice)
- **`MODE_FINITION_AUTONOME_PAR_BLOCS.md`** — bascule du workflow
  en mode finition autonome le 2026-05-12

---

## 1. Direction visuelle

### 1.1 Direction A — *"Carrier-grade épuré"* (validée)

**Référence dominante** : Telnyx + Twilio.

**Rationale** :
- Alignement positionnement carrier-grade Tier-1 (brief §2.1 + amendement A)
- Cohérence avec décisions PO du 2026-05-08 : trust signals impersonnels
  = playbook Telnyx natif
- Action Black `#0E121C` (~15% Brand Kit) trouve son emploi naturel
  comme background dark hero/footer/code blocks
- Différenciation marché vs Sinch/Bandwidth (perçus "interchangeables"
  selon étude DESIGN_REFERENCES.md §2.1)
- Faisabilité 4-6 jours respectée (Direction C custom illustrations
  aurait demandé +2-3j hors budget)

**Risque assumé** : peut paraître froid pour visiteurs non-techniques
(banques retail décideurs francophones). Mitigation : sections produits
+ industries en mode clair pour rééquilibrer (hybridation A+B 80/20
implicitement adoptée pendant Étape 4).

**Décisions rejetées** :
- ❌ Direction B pure ("CPaaS accessible Plivo/Sinch") — trop
  interchangeable
- ❌ Direction C pure ("Premium éditorial Stripe") — effort design
  hors budget, risque dilution carrier-grade

### 1.2 Mood et tone of voice

- **Confiance carrier-grade** : *"on est un vrai opérateur, pas une
  app qui fait du SMS"*
- **Global CPaaS/ITSP** avec ancrage local (60%+ clients hors Afrique
  selon Brand Kit v1.2 §12 — **JAMAIS positionner "panafricain"**)
- **Developer-friendly** : code preview central, syntax highlighting,
  curl POST réaliste
- **Premium mais accessible** : pas le luxe Sinch enterprise, pas
  l'agence digitale Sneat default

---

## 2. Tokens visuels (Brand Kit v1.2 — figé)

### 2.1 Palette 4 couleurs (ratios stricts)

| Token | Hex | Rôle | Surface visée |
|---|---|---|---|
| Primary Petrol Teal | `#335F5F` | Branding, sidebars, titres brand | ~30% |
| Secondary Action Black | `#0E121C` | CTAs critiques, hero dark, footer | ~15% |
| Tertiary Teal-Cyan | `#14B8A6` | Spot only, max 5%, max 3-4 occ./page | ~5% |
| Foundation | `#FFFFFF` + warm grays `$dd-ink-*` | Surfaces, body | ~50% |

**Statuts sémantiques (jamais branding)** :
- Success `#0EBE82` · Warning `#F2A93B` · Danger `#EF4361` · Info `#3A86FF`

**Règles cyan strictes (BRAND_KIT_DREAM_DIGITAL.md §3.5)** :
- ❌ Jamais sur CTAs primaires, card backgrounds, headings, body text,
  container borders
- ✅ Seulement : liens éditoriaux, focus rings, NEW badges, live
  indicators, KPIs animés

**Couleurs obsolètes interdites** (brief original avant Brand Kit v1.2) :
- ❌ `#2A4F9E` (ancien primary)
- ❌ `#00D9FF` (ancien accent cyan — remplacé par `#14B8A6`)

Vérification automatisable : `grep -ri "#2A4F9E\|#00D9FF" resources/`
doit retourner 0 hit en code (hors mentions négation explicites).

### 2.2 Code blocks (terminal animation hero Slide 2 + section Dev-First)

Tokens spécifiques pour syntax highlighting dans les terminaux
Telnyx-style :

| Token | Hex | Usage |
|---|---|---|
| `$dd-code-bg` | `#0F1428` | Fond terminal dark navy |
| `$dd-code-text` | `#E1E4F0` | Texte par défaut |
| `$dd-code-keyword` | `#F472B6` | Mots-clés, clés JSON |
| `$dd-code-string` | `#86EFAC` | Strings |
| `$dd-code-num` | `#FBBF24` | Nombres |
| `$dd-code-fn` | `#14B8A6` | Fonctions (= Tertiary Cyan, PAS `#00D9FF`) |
| `$dd-code-comment` | `#8A8FA3` | Commentaires |

### 2.3 Typographie — Pair A

**Décision** : Inter en family unique (300-800) + JetBrains Mono
(400-600) pour le code.

**Chargement** : Google Fonts CDN avec `preconnect` + `display=swap`,
injecté dans `resources/views/layouts/commonMaster.blade.php` (S3 du
Sprint 0, commit `6813439`).

**Rationale** :
- Inter remplace Bricolage Grotesque + Outfit du brief original
  (un seul fichier → payload réduit)
- Inter weight 700-800 sur hero/sections apporte le côté contemporain
  B2B carrier-grade cohérent avec Stripe, Linear, Vercel
- JetBrains Mono pour les blocs code = signature dev-first explicite

**Scale responsive** (clamp mobile-first, voir `_typography.scss`) :
- `$dd-display-1` : `clamp(2.5rem, 6vw, 5rem)` — hero h1
- `$dd-display-2` : `clamp(2rem, 4.5vw, 3.5rem)` — section titles
- `$dd-h1` à `$dd-caption` : voir tokens

**Letter-spacing** :
- Tight `-0.025em` pour display
- Wide `+0.06em` pour uppercase/labels

### 2.4 Espacements et radius

**Décision** : passage de "compact Sneat default" à "généreux et
respirant ITSP".

- Section padding : `clamp(5rem, 10vw, 8rem)` par défaut
  (80-128px vs ~40px Sneat)
- Container max : 1280px (vs 1200 Bootstrap default)
- Container narrow : 960px (pour contenu éditorial)
- Border radius signature : `md = 8px`, `lg = 16px` (cards),
  `2xl = 32px` (hero blocks)

### 2.5 Shadows

**Décision** : "tonal layers" — borders 1px préférés aux shadows
lourdes. Par défaut, no shadow + 1px border. Shadow uniquement sur
hover lift cards (`translateY(-4px)` + `$dd-shadow-md`).

Shadow signature focus ring :
`$dd-shadow-glow: 0 0 0 1px rgba(20, 184, 166, 0.4), 0 0 24px rgba(20, 184, 166, 0.25)`
(Tertiary Cyan).

### 2.6 Animations et micro-interactions

- **Easings** : `cubic-bezier(0.2, 0.8, 0.2, 1)` (out) /
  `cubic-bezier(0.4, 0, 0.2, 1)` (in-out)
- **Durations** : 140ms (hover) · 240ms (transitions) ·
  480ms (sections) · 800ms (hero)
- **Comportements implémentés** :
  1. Hover lift sur cards : translateY + shadow grow
  2. Stagger fade-up au scroll (Intersection Observer)
  3. Smooth scroll natif
  4. Hero Slide 2 : typing effect monocaractère sur curl POST
  5. Hero Slide 4 : drapeaux SVG + GPS coords statiques

**Garde a11y** : tous les keyframes `dd-*` sont protégés par
`@media (prefers-reduced-motion: reduce)` (correctif HIGH-2,
commit `75405ba`, WCAG 2.2.2).

---

## 3. Iconographie

**Décision** : Boxicons unique iconset (jurisprudence Q13 Sprint 0
S9-C3, confirmée pour Sprint 1.5).

**Rationale** :
- Boxicons déjà chargé via `vite.icons.plugin.js` post-S9
- Tabler Icons indisponible sans extension du plugin Vite
- Set de 7 icônes choisies pour service-grid : `bx-message-detail`
  (SMS A2P) · `bx-phone-call` (Voice) · `bx-hash` (DID Numbers) ·
  `bx-network-chart` (SIP) · `bx-headphone` (Dialo CC) ·
  `bx-mobile-alt` (eSIM) · `bx-microchip` (alternative eSIM)

**Logos officiels Dream Digital** : SVG inline Pattern A.1
(S5 du Sprint 0, commit `f818f03`) — `logo-dd-icon.svg` 1.4 KB,
5 paths vectoriels, viewBox 1247×1370, couleurs teal `#336666` +
blancs `#FEFEFE` calibrées sur Brand Kit.

---

## 4. Composants — bibliothèque Blade

Localisation : `resources/views/front/components/`.

### 4.1 Composants atomiques (brief §4.1, 16/16 livrés)

| Composant | Rôle | Usage |
|---|---|---|
| `hero-split.blade.php` | Hero split + Swiper 4 slides | Home (`/fr`) |
| `hero-simple.blade.php` | Hero 1 colonne éditorial | À propos, Blog |
| `hero-banner.blade.php` | Hero gradient banner | Pages légales, FAQ |
| `trust-strip.blade.php` | Trust signals impersonnels | Sous tous les heros importants |
| `stats-strip.blade.php` | 4 KPI animés (counter scroll-in) | Home + À propos |
| `service-grid.blade.php` | Grille 6 services CPaaS | Home + Products |
| `industry-grid.blade.php` | 4 cards cas d'usage (remplace témoignages) | Home + Solutions |
| `code-preview.blade.php` | Block code statique réutilisable | Pages produits |
| `developer-code.blade.php` | Section Developer-First (split typing) | Home |
| `coverage-map.blade.php` | Carte constellation cyan | Home + Coverage |
| `pricing-cards.blade.php` | 3 cards pricing teaser | Home + Pricing |
| `testimonials.blade.php` | (rendu conditionnel — non utilisé V1) | — (réintroduit quand vrais clients) |
| `cta-banner.blade.php` | CTA finale bas de page | Toutes les pages produits |
| `country-language-switcher.blade.php` | Switcher pays/langue | Header (Sprint 1 fonctionnel) |
| `geo-detection-banner.blade.php` | Banner geoIP redirect | Toutes pages |
| `faq-accordion.blade.php` | FAQ Bootstrap accordion | Home + FAQ |
| `feature-list.blade.php` | Liste features avec icônes | Pages produits |
| `flag.blade.php` | Drapeau SVG par code pays | Hero Slide 4, switcher |

### 4.2 Composants signature ITSP (brief §4.2, 3/3 livrés)

| Composant | Effet visuel | Référence brief |
|---|---|---|
| `signal-indicator.blade.php` | Pulse animation pour KPI temps réel | §4.2.a |
| `corridor-card.blade.php` | Card corridor SMS (FR → CD avec tarif) | §4.2.b |
| `live-feed.blade.php` | Ticker événements télécom simulés | §4.2.c |

---

## 5. Contenu — décisions PO arbitrées 2026-05-08

Détail complet dans `ANALYZE_SPRINT_1_5.md` §5.

### 5.1 Q1 — Photos d'équipe pays
**✅ Validé avec restriction** : pas de skylines réelles
(Kinshasa CBD / Treichville / Brazzaville Bourg) pour risque kitsch
+ droits. À la place : illustration vectorielle abstraite OU carte
stylisée. **Aucune photo réelle utilisée.**

Implémentation : Hero Slide 4 (Bureaux pays) utilise des cards typo
+ coordonnées GPS + drapeaux SVG (`flag.blade.php`).

### 5.2 Q2 — Trust signals — **❌ logos clients REJETÉS**
**Refus catégorique** d'afficher des placeholders textuels
*"Rawbank, Equity Bank, Vodacom, Carrefour CI, Pullman, DHL Africa"*
même indicatifs (risque juridique d'usurpation d'identité
commerciale).

**À la place — Trust signals impersonnels** alignés playbook Telnyx :
- "200+ pays couverts"
- "Infrastructure carrier-grade"
- "SLA 99.9% disponibilité"
- "Conformité GDPR"
- "Architecture redondée multi-datacenters"
- Certifications visées (ISO 27001, SOC 2) si en cours

Implémentation : `trust-strip.blade.php` rend une grille de chiffres
+ certifs + icônes Boxicons (PAS un marquee de logos).

### 5.3 Q3 — Témoignages — **❌ SECTION SUPPRIMÉE V1**
Refus même de témoignages neutralisés ("Banque partenaire RDC",
"Retailer panafricain") par risque d'interprétation.

**À la place — Industries grid** : 4 cas d'usage génériques par
secteur (Banking & Fintech / Retail / Logistics / Hospitality),
aucun client nommé.

Implémentation : `industry-grid.blade.php` rendu à la place de
`testimonials.blade.php` sur la home. Le composant `testimonials.blade.php`
existe mais reste rendu conditionnel — réintroduit uniquement quand
de vrais clients accepteront de témoigner avec autorisation écrite.

### 5.4 Q4 — Coverage — **✅ validé**
"200+ pays" + "5 corridors africains premium" sans liste détaillée.
Carte du monde stylisée façon "constellation" (points cyan animés).
Liste précise par opérateur reportée à Sprint 1 (page produit SMS).

### 5.5 Q5 — Code preview API — **✅ validé avec ajustement**
- Endpoint simulé crédible :
  `curl POST https://api.dream-digital.info/v1/sms/send` +
  response JSON cohérente
- Caveat *"(API en preview, doc publique disponible Sprint 2)"* **retiré**
  du code (fait amateur)
- Remplacé par mention discrète en bas de section Developer-First :
  *"Documentation API détaillée publiée à mesure du déploiement de
  chaque produit"*

---

## 6. Architecture — CMS-ready (configs Laravel)

### 6.1 Principe

Le contenu de la vitrine n'est **jamais** figé dans les vues Blade.
Il est intégralement piloté par des fichiers de configuration sous
`config/dream-digital/*.php`, accessibles dans Blade via
`config('dream-digital.<file>.<key>')`.

**Rationale** :
- Permet la migration future vers une table CMS Eloquent (`pages`,
  prévue Sprint 1) sans toucher aux vues
- Tous les textes sont structurés en `['fr' => ..., 'en' => ...]`
  prêts pour i18n (Sprint 1)
- Le PO peut modifier le copy directement en éditant un fichier
  config sans risque de casser le markup

### 6.2 Fichiers config (commit `a8b2589` et suivants)

| Fichier | Rôle |
|---|---|
| `site.php` | Métadonnées globales (tagline, contact, social, meta) |
| `home.php` | Sections home : hero, developer, pricing, faq, transitions |
| `services.php` | 6 services CPaaS (SMS A2P, Voice, DID, SIP, Dialo, eSIM) |
| `industries.php` | 4 cas d'usage par industrie (Banking, Retail, Logistics, Hospitality) |
| `coverage.php` | Stats pays/régions + corridors |
| `trust-signals.php` | 6 signaux de confiance impersonnels |
| `partners.php` | Partenaires neutralisés (réservé Sprint 1) |
| `footer.php` | Footer 5 colonnes |
| `pages.php` | Métadonnées pages publiques modulaires |

### 6.3 Convention NULL VALUE (jurisprudence MED-5)

Certaines clés sont volontairement à `null` (`contact.email_support`,
`contact.phone`, `social.*`, `meta.og_image`, etc.) — elles attendent
un input PO.

**Règle Blade** : toujours utiliser `??` null-coalescing ou `@if` de
garde pour éviter du markup cassé silencieux. Documentée dans le
docblock de `config/dream-digital/site.php` (commit `2459db3`).

### 6.4 Limite connue (TD-005)

Les configs ont des clés `'en'` qui contiennent du texte FR identique
au `'fr'`. C'est une décision consciente pour livrer Sprint 1.5 vite.
Les vraies traductions EN seront intégrées en Sprint 1.

**Cas particulier** : `site.tagline.fr` = `site.tagline.en` =
*"Voice. SMS. eSIM. And More."* est **intentionnel** (tagline EN
validée comme marque, Brand Kit v1.2 §1).

---

## 7. Animations clés — choix techniques

### 7.1 Hero Slide 2 — Terminal code typing

**Décision** : implémentation custom JS sans dépendance externe
(pas de TypeIt.js).

**Rationale** :
- Réduire la taille du bundle JS (TypeIt = ~10 KB gzip)
- Contrôle fin : état `'idle' / 'typing' / 'done'` explicite
- Reset typing si `intersection.isIntersecting === false &&
  boundingClientRect.top > 0` (scroll past)
- Sync avec Swiper slide change : reset au sortir de slide 2

**Risque mitigé R3** (brief §4 ANALYZE) : glitch ruinant l'effet
"wow" — couvert par état explicite et reset propre.

### 7.2 Hero Slide 1 — Carte du monde

**Décision** : approche **"constellation cyan stylisée"** plutôt
qu'une vraie SVG world map à 200 points.

**Rationale** :
- Performance : 200 points cyan animés simultanés = jank sur mobile
  entry-level (risque R2 brief ANALYZE)
- Limite à 50-80 points visuellement (sub-sample des 200+ pays)
  via CSS `animation` + `will-change: transform, opacity`
- Pause animation sur `prefers-reduced-motion: reduce`
- Approche métaphorique cohérente avec Direction A (sobriété
  carrier-grade) vs une carte réaliste qui ferait "trop marketing"

### 7.3 Stats strip — Counter animation

**Décision** : counter qui s'incrémente de 0 → valeur cible au
scroll-in, implémentation custom sans CountUp.js (cohérence avec
décision 7.1).

### 7.4 Coverage map

Reprend l'approche "constellation cyan" du Hero Slide 1 mais étendue
sur largeur full container, avec 3 marqueurs plus gros pour les
bureaux Kinshasa + Abidjan + Brazzaville (+ amendement Nairobi /
Gentilly, commit `f0a2e09`).

---

## 8. Décisions techniques transverses

### 8.1 Iconset Boxicons (vs Tabler)
Jurisprudence Q13 Sprint 0 — pas réouverte en Sprint 1.5.

### 8.2 Slider Swiper
Effect `'fade'` avec `crossFade: true` (pas slide horizontal qui
fait "carrousel commercial cheap" — brief §3.2). Auto-rotate 6s,
pause au hover, pagination dots minimalistes.

### 8.3 Plugin Vite `vite.icons.plugin.js`
Inchangé post-S9. Pas d'extension pour Tabler.

### 8.4 Build public léger
Script `npm run build:public` ajouté (109 modules vs 1491 pour le
build complet). Permet de déployer la vitrine sans tout le template
demo Sneat (Bloc 4 mode finition).

### 8.5 Mode finition autonome (2026-05-12)
Bascule du workflow checkpoint-par-checkpoint au mode "blocs
autonomes de 2-4h" pour accélérer la phase de finition. Voir
`MODE_FINITION_AUTONOME_PAR_BLOCS.md`. Garde-fou : validation PO
requise uniquement pour les éléments produit/design majeurs
(hero, navigation, claims business, ouverture publique).

---

## 9. Critères d'acceptance §6 du brief — récap

| # | Critère | État |
|---|---|---|
| 6.1 | `DESIGN_DECISIONS.md` documente les choix | ✅ ce document |
| 6.2 | Home `/fr` ressemble à un site CPaaS sérieux | ⚠️ validation PO visuelle pendante |
| 6.3 | Hero slider Swiper 4 slides distincts | ✅ commits Étape 3 phases 4 → 8b |
| 6.4 | Inter (300-800) + JetBrains Mono (400-600) | ✅ S3 Sprint 0 |
| 6.5 | Palette appliquée cohéremment (cyan accent ≤5%) | ✅ Brand Kit v1.2 strict |
| 6.6 | Code preview typing animation au scroll-in | ✅ Hero Slide 2 |
| 6.7 | Coverage map points cyan animés | ✅ constellation stylisée |
| 6.8 | Tous components atomiques + page demo | ✅ 16/16 + `/preview/design-tokens` |
| 6.9 | 3 components signature fonctionnels | ✅ signal-indicator + corridor-card + live-feed |
| 6.10 | Responsive mobile 375 → wide 1920 | ⚠️ matrice à exécuter (P3) |
| 6.11 | Lighthouse perf > 85, a11y > 95 | ⚠️ audit à exécuter (P2) |
| 6.12 | Aucun warning console | ⚠️ audit à exécuter (P4) |
| 6.13 | Commits atomiques en séquence | ✅ workflow strict respecté |
| 6.14 | `DESIGN_DECISIONS.md` (ce doc) | ✅ |
| 6.15 | `DESIGN_REFERENCES.md` (observations sites) | ✅ commit `2459db3` |

---

## 10. Liens utiles

- Brand Kit complet : `BRAND_KIT_DREAM_DIGITAL.md`
- Tokens SCSS : `resources/assets/vendor/scss/_custom-variables/_dream-digital.scss`
- Page demo tokens : `/preview/design-tokens` (mode local/staging)
- Étude sites : `DESIGN_REFERENCES.md`
- Arbitrages Q&A : `ANALYZE_SPRINT_1_5.md` (Q1-Q5 PO 2026-05-08)
- Méthodologie finition : `MODE_FINITION_AUTONOME_PAR_BLOCS.md`
- Dette technique : `TECH_DEBT.md`

---

*Document figé au 2026-05-12 dans le cadre de la clôture autonome
des 4 critères d'acceptance manquants. Toute évolution future de la
direction visuelle doit être consignée ici avec date + rationale.*
