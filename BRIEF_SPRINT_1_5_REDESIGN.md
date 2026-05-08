# BRIEF — Sprint 1.5 : Redesign vitrine ITSP/CPaaS

> **Position dans la séquence** : à exécuter **après** `BRIEF_DD_DESANONYMIZATION.md` et **avant** `BRIEF_SPRINT_1.md` (fondations multi-pays).
>
> **Pourquoi ?** : la désanonymisation rend le code propriétaire Dream Digital. Ce sprint **transforme l'apparence visuelle** de la landing Sneat pour qu'elle ressemble à un vrai site d'ITSP / Wholesale Voice / SMS A2P-P2A Provider. Il faut le faire avant le Sprint 1 fondations parce que les fondations construiront 35 pages — autant qu'elles soient construites sur le bon socle visuel dès le départ.

> **Durée estimée** : 4-6 jours de travail Claude Code supervisé, **avec validation visuelle stricte par le PO à chaque étape**.

> **Branche Git** : `feature/sprint-1-5-redesign`

---

## 1. Contexte et engagement

### 1.1 Le problème à résoudre

La landing Sneat default a un look "tech corporate générique" qui pourrait être celui d'une agence de marketing, d'un CRM, ou d'un outil de gestion de projet. **Ce look ne distingue pas Dream Digital comme un opérateur télécom B2B.**

Un client banque/fintech qui visite votre site doit, dans les 5 premières secondes, **sentir** qu'il est sur le site d'un opérateur télécom sérieux comparable à Twilio, Plivo, Sinch, Infobip, Telnyx, Bandwidth, MessageBird/Bird.

### 1.2 Engagement de qualité

**Le Product Owner s'engage à valider visuellement** à chaque étape majeure du sprint. Pas de validation par défaut. Si le résultat ne ressemble pas à un vrai site CPaaS, on refait. Cette discipline est non-négociable — c'est elle qui fera la différence entre un Sneat customisé et une vraie vitrine ITSP.

**Claude Code s'engage à** :
- Étudier les références visuelles fournies **avant** de coder
- Présenter chaque écran majeur en preview pour validation avant de continuer
- Refaire sans rechigner si le PO juge le résultat insuffisant
- Documenter ses choix de design dans un fichier `DESIGN_DECISIONS.md`

### 1.3 Sites de référence à étudier obligatoirement

Avant tout code, Claude Code doit ouvrir et étudier ces 6 sites en détail (URLs publiques accessibles librement). **Au minimum 30 minutes par site, à scroller, cliquer, observer**.

| Site | URL | Ce qu'il faut observer |
|---|---|---|
| **Twilio** | https://www.twilio.com | LA référence absolue. Hero épuré, palette rouge/blanc, typographie soignée, exemples de code partout, structure produits/solutions claire |
| **Plivo** | https://www.plivo.com | Plus accessible/simple que Twilio, palette violet/blanc, hero avec illustration télécom, sections produits propres |
| **Telnyx** | https://telnyx.com | Look plus dark/premium, palette noir/vert électrique, très "ingénierie", carte mondiale animée du backbone |
| **Sinch** | https://www.sinch.com | Palette sobre bleue, ton enterprise, beaucoup de logos clients, rassurant |
| **Bandwidth** | https://www.bandwidth.com | Très carrier/wholesale, palette sombre, infographies réseau |
| **MessageBird (Bird)** | https://bird.com | Plus marketing/CX, mais bel exemple de site B2B moderne avec illustrations vectorielles personnalisées |

Pour chaque site, observer spécifiquement :

1. **Hero** : structure, mots-clés employés, CTA principaux, image/visuel à droite
2. **Section produits** : grille ou cards ? Comment sont présentés SMS / Voice / Numbers / SIP ?
3. **Code snippets** : présents ou non ? À quel endroit ? Comment sont stylés ?
4. **Logos clients** : où sont-ils ? Combien ? Quel ton ?
5. **Section "developer-first"** : présente ? Comment l'API est mise en avant ?
6. **Pricing** : page dédiée ou intégrée ? Calculateur ?
7. **Coverage** : carte du monde ? Liste de pays ? Statistiques ?
8. **Section trust** : SLA, compliance (SOC2, GDPR, ISO), uptime ?
9. **Footer** : très riche typiquement, avec produits/solutions/developers/company séparés
10. **Typographie** : généralement sans-serif moderne (Inter, Söhne, Public Sans, Geist…)

---

## 2. Direction visuelle cible

### 2.1 Mood et ton

Dream Digital doit communiquer visuellement :

- **Confiance carrier-grade** : on est un vrai opérateur, pas une app qui fait du SMS
- **Global CPaaS/ITSP avec ancrage local** : 60%+ de clientèle hors Afrique, 80%+ partenaires hors Afrique. Notre positionnement est celui d'un opérateur télécom global avec des équipes basées en Afrique francophone — pas d'un acteur panafricain. L'identité visuelle doit refléter cette ambition globale (sobriété carrier-grade) tout en gardant une mention naturelle des bureaux de Kinshasa, Abidjan et Brazzaville comme preuve d'opérations réelles.
- **Developer-friendly** : le code est partout sur le site
- **Premium mais accessible** : pas le côté "luxueux et inaccessible" d'un Sinch enterprise, mais pas le côté "agence digitale" du Sneat default non plus

### 2.2 Système de couleurs

**Décision** : Brand Kit v1.2 validé, palette **déjà active depuis S3** (override Bootstrap via `_custom-variables/_bootstrap-extended.scss` chargeant `_dream-digital.scss`). Pour Sprint 1.5, utiliser exclusivement les tokens existants — aucun ajout palette.

```scss
// === Palette Dream Digital v1.2 ===
// Source unique : _custom-variables/_dream-digital.scss (déjà actif post-S3)
// Ne PAS dupliquer ici — tous les tokens sont disponibles via les variables 
// déjà importées en _custom-variables/_bootstrap-extended.scss.

// Rappel des couleurs principales (pour référence design uniquement) :
// --bs-primary       : #335F5F  Petrol Teal       ~30% usage (branding)
// --bs-secondary     : #0E121C  Action Black      ~15% usage (CTAs critiques)
// --bs-info          : #14B8A6  Tertiary Cyan     ~5% MAX (spot accent)
// --bs-success       : #0EBE82  Delivered indicator
// --bs-warning       : #F2A93B  Pending indicator
// --bs-danger        : #EF4361  Failed indicator

// === Code blocks (pour terminal animation Section 3.3 Developer-First) ===
$dd-code-bg:      #0F1428;        // dark navy (cohérent avec Action Black)
$dd-code-text:    #E1E4F0;
$dd-code-keyword: #F472B6;        // pink (clés JSON, mots-clés langage)
$dd-code-string:  #86EFAC;        // vert (strings)
$dd-code-num:     #FBBF24;        // jaune (nombres)
$dd-code-fn:      #14B8A6;        // ⚠️ Tertiary Cyan v1.2 (PAS le #00D9FF obsolète)
$dd-code-comment: #8A8FA3;        // gray
```

### 2.3 Typographie

```scss
// === Typographie Dream Digital v1.2 ===
// Stack déjà chargée via Google Fonts en commonMaster.blade.php (S3)
// Inter remplace Bricolage Grotesque + Outfit (un seul fichier au lieu de deux)

$dd-font-display: 'Inter', system-ui, -apple-system, sans-serif;
$dd-font-body:    'Inter', system-ui, -apple-system, sans-serif;
$dd-font-mono:    'JetBrains Mono', ui-monospace, 'SF Mono', monospace;

// Les weights utilisés :
// 300 (light)     : très rare, body de témoignages
// 400 (regular)   : body par défaut
// 500 (medium)    : navigation, labels
// 600 (semibold)  : H3, H4, CTAs
// 700 (bold)      : H1, H2 (sections)
// 800 (extrabold) : Hero display, stats énormes

// Scale (mobile first, responsive via clamp) — INCHANGÉ vs brief original
$dd-display-1: clamp(2.5rem, 6vw, 5rem);     // 40px → 80px (hero h1)
$dd-display-2: clamp(2rem, 4.5vw, 3.5rem);   // 32px → 56px (section titles)
$dd-h1: clamp(1.75rem, 3.5vw, 2.5rem);       // page h1
$dd-h2: clamp(1.5rem, 2.5vw, 2rem);          // h2
$dd-h3: 1.25rem;                              // 20px h3
$dd-body-lg: 1.125rem;                        // 18px (lead)
$dd-body: 1rem;                               // 16px (default)
$dd-body-sm: 0.875rem;                        // 14px (small)
$dd-caption: 0.75rem;                         // 12px

// Letter-spacing
$dd-tracking-tight: -0.025em;  // pour les display
$dd-tracking-normal: 0;
$dd-tracking-wide: 0.06em;     // pour les uppercase/labels

// Line-heights
$dd-leading-tight: 1.15;       // titles
$dd-leading-snug: 1.35;
$dd-leading-normal: 1.55;      // body
$dd-leading-relaxed: 1.7;      // long form
```

**Important** : Inter weight 700-800 sur les hero/sections apporte le côté contemporain B2B carrier-grade, cohérent avec Stripe, Linear, Vercel. Inter en family unique simplifie la stack typographique et réduit le payload Google Fonts.

### 2.4 Espacements et radius

Sneat default = "compact". Dream Digital ITSP = "généreux et respirant".

```scss
// Sections padding (vertical breathing)
$dd-section-py-sm: clamp(3rem, 6vw, 5rem);     // 48-80px
$dd-section-py-md: clamp(5rem, 10vw, 8rem);    // 80-128px (default)
$dd-section-py-lg: clamp(6rem, 14vw, 12rem);   // 96-192px (hero)

// Container max-width
$dd-container-max: 1280px;     // au lieu de 1200 Bootstrap default
$dd-container-narrow: 960px;   // pour le contenu éditorial (blog, à propos)

// Border radius
$dd-radius-xs: 0.375rem;       // 6px (badges)
$dd-radius-sm: 0.5rem;          // 8px (boutons small)
$dd-radius-md: 0.75rem;         // 12px (boutons, inputs)
$dd-radius-lg: 1rem;            // 16px (cards) ← key signature
$dd-radius-xl: 1.5rem;          // 24px (large sections, modals)
$dd-radius-2xl: 2rem;           // 32px (hero blocks)

// Shadows (douces, pas dramatiques)
$dd-shadow-sm: 0 1px 2px rgba(10, 14, 26, 0.04), 0 1px 3px rgba(10, 14, 26, 0.06);
$dd-shadow-md: 0 4px 12px rgba(10, 14, 26, 0.06), 0 2px 4px rgba(10, 14, 26, 0.04);
$dd-shadow-lg: 0 12px 32px rgba(10, 14, 26, 0.08), 0 4px 8px rgba(10, 14, 26, 0.04);
$dd-shadow-glow: 0 0 0 1px rgba(20, 184, 166, 0.4), 0 0 24px rgba(20, 184, 166, 0.25);  // focus ring DD tertiary teal
```

### 2.5 Animations et micro-interactions

```scss
// Easings
$dd-ease-out: cubic-bezier(0.2, 0.8, 0.2, 1);
$dd-ease-in-out: cubic-bezier(0.4, 0, 0.2, 1);

// Durations
$dd-duration-fast: 140ms;       // hover states
$dd-duration-normal: 240ms;     // transitions standard
$dd-duration-slow: 480ms;       // entrées de sections
$dd-duration-slower: 800ms;     // hero animations

// Comportements à implémenter
// 1. Hover lift sur cards : translateY(-4px) + shadow grow
// 2. Stagger fade-up au scroll (Intersection Observer)
// 3. Smooth scroll natif (scroll-behavior: smooth)
// 4. Hero rotation des mots (typing effect) : "SMS · Voice · DID · SIP · eSIM"
```

---

## 3. Refonte de la landing page (le morceau central)

### 3.1 Structure cible

La landing actuelle Sneat doit être **réorganisée** dans cet ordre, qui est le standard CPaaS :

1. **Sticky nav** (transparent au top, devient solide au scroll)
2. **Hero** (avec slider OU avec terminal de code, à choisir — voir sous-section 3.2)
3. **Trust strip** (logos clients en marquee/grille, en dessous du hero immédiatement)
4. **Stats strip** (4 chiffres clés : pays couverts, uptime, latence, présence)
5. **Section Produits** (grille 6 cards : SMS, Voice, DID, SIP, Dialo, eSIM)
6. **Section Developer-First** (split : à gauche le pitch, à droite un code preview animé)
7. **Section Solutions par industrie** (3-4 cards : Banque, Retail, Logistique, Hôtellerie)
8. **Section Coverage** (carte du monde stylisée + stats par région)
9. **Section Pricing teaser** (3 cards : Découverte / Pay-as-you-go / Entreprise)
10. **Section Témoignages** (3 cards client avec photo + quote courte)
11. **Section CTA finale** (gros bandeau "Prêt à envoyer votre premier SMS ?")
12. **Footer riche** (5 colonnes : Produits / Solutions / Développeurs / Société / Légal)

### 3.2 Hero — DÉCISION VISUELLE CRITIQUE

Le PO a confirmé un **slider sur la home**. Voici comment l'adapter au ton CPaaS sans perdre le côté "developer-first" qui distingue le secteur.

**Hero design retenu** : split fullwidth, slider à droite, contenu fixe à gauche.

```
+---------------------------------------------------------+
|                          NAV                            |
+---------------------------------------------------------+
|                                                         |
|  [eyebrow: "Voice. SMS. eSIM. And More."]              |
|                                                         |
|  L'infrastructure                  [Slide 1 visual]    |
|  télécom qui                       → Carte mondiale    |
|  connecte les                        avec points       |
|  entreprises                         animés            |
|  modernes à                                            |
|  200+ pays.                                            |
|                                                         |
|  [Sous-titre : SMS A2P, Voice Wholesale, DID, SIP       |
|   Trunking, Dialo Contact Center, eSIM Zone — sous     |
|   une seule plateforme. Une exigence carrier-grade.]    |
|                                                         |
|  [CTA accent: Échanger avec notre équipe]              |
|  [CTA ghost: Voir la doc API (bientôt)]                |
|                                                         |
|  [Mini-bullets : ✓ Couverture 200+ pays               |
|                  ✓ Bureaux RDC · CI · CG]              |
+---------------------------------------------------------+
|                    TRUST STRIP                          |
+---------------------------------------------------------+
```

**Côté droit (la "scène")** : un slider Swiper avec 4 slides différents qui remplacent l'illustration statique :

| Slide | Visuel |
|---|---|
| 1 | **Carte du monde** stylisée avec points cyan animés sur les 200+ pays couverts (utiliser une SVG world map, ajouter des cercles qui pulsent en cyan accent). Texte overlay : "200+ pays · 5 corridors africains premium" |
| 2 | **Terminal de code** dark animé qui type un exemple curl SMS, avec syntax highlighting. Affiche la réponse JSON après. Donne le côté "developer-first" |
| 3 | **Dashboard preview** : screenshot stylisé du dashboard Dream Digital (KPI live, charts SMS sent / DLR rate / cost). Donne le côté "console pro" |
| 4 | **Mosaïque de drapeaux pays + photos d'équipe locales** (si vous avez des photos, sinon logos pays Kinshasa/Abidjan/Brazzaville). Donne le côté "ancrage africain" |

**Animation entre slides** : fade crossfade doux (pas slide horizontal qui fait "carrousel commercial cheap"). Auto-rotate 6 secondes, pause au hover. Pagination en dots minimalistes.

**Côté gauche** : le contenu reste **fixe** (le titre, sous-titre, CTAs ne changent pas avec les slides). Seule la "scène" droite tourne. C'est plus pro que le slider qui fait tout tourner.

### 3.3 Détails sections clés à coder soigneusement

#### Section "Trust strip" (logos clients)

- **Pas** une grid statique de logos
- **Marquee animé** qui fait défiler en continu (CSS animation)
- Les logos en **grayscale** par défaut, **passent en couleur au hover** de la rangée
- Texte au-dessus : "Ils nous font confiance · Banques · Retail · Logistique" (uppercase, tracking large, gris discret)
- **Logos à intégrer** : à fournir par le PO. En attendant : utiliser des placeholders en SVG text avec les noms (Rawbank, Equity Bank, Vodacom, Carrefour CI, Pullman, DHL Africa, etc.)

#### Section "Stats strip"

```
[200+]              [99.95%]            [~2s]              [5]
Pays couverts       Uptime garanti      Latence            Pays présence
SMS                                     moyenne            locale
```

- 4 colonnes égales, séparées par des dividers fins verticaux
- Chiffres en **Inter weight 800 très gros** (clamp(2rem, 4vw, 3rem))
- Labels en uppercase tracking large gris
- **Animation au scroll** : counter qui s'incrémente de 0 → valeur cible (utiliser CountUp.js ou custom)

#### Section "Produits" (la plus importante)

Grille 3×2 sur desktop, 2×3 sur tablet, 1×6 sur mobile. **Pas de cards "en lévitation"** comme Sneat default. À la place, **cards fusionnées séparées par des lignes**, façon "table de matière premium" :

```
+----------------+----------------+----------------+
| [icon cyan]    | [icon cyan]    | [icon cyan]    |
| SMS A2P        | Voice          | DID Numbers    |
|                | Wholesale      |                |
| Description... | Description... | Description... |
|                |                |                |
| Voir la doc →  | Voir tarifs →  | Acheter →      |
+----------------+----------------+----------------+
| [icon cyan]    | [icon cyan]    | [icon cyan]    |
| SIP Trunking   | Dialo CC       | eSIM Zone      |
|                |                |                |
| Description... | Description... | Description... |
|                |                |                |
| Configurer →   | Démo →         | Visiter →      |
+----------------+----------------+----------------+
```

- Cards **sans border-radius prononcé** (juste 4px), look "industrial"
- Background blanc, séparateurs gris très clair
- **Hover** : la cell devient légèrement teintée cyan (`background: $dd-accent-100`), l'icône change de couleur, la flèche du lien glisse vers la droite
- Icône en **carré 44x44** avec léger gradient cyan, pas en cercle

Icônes à utiliser (Boxicons, déjà chargés via `vite.icons.plugin.js` post-S9 — cohérent avec le reste du site Dream Digital. Tabler Icons NON disponible sans extension du plugin Vite, décision Q13 S9-C3 confirmée pour S1.5) :

- SMS A2P         : `bx-message-rounded` ou `bx-message-detail`
- Voice Wholesale : `bx-phone` ou `bx-phone-call`
- DID Numbers     : `bx-hash` ou `bx-numbers`
- SIP Trunking    : `bx-network-chart` ou `bx-server`
- Dialo CC        : `bx-headphone` ou `bx-support`
- eSIM Zone       : `bx-mobile-alt` ou `bx-microchip`

#### Section "Developer-First"

C'est **LA** section qui doit faire dire au visiteur "OK, ces gars sont sérieux" :

```
+---------------------------+----------------------------+
| [tag: Developer experience]|                          |
|                           |  [Terminal animé]         |
| Onboardez en              |  ╔═════════════════════╗   |
| 10 minutes.               |  ║ ● ● ●   curl POST  ║   |
| Vraiment.                 |  ╠═════════════════════╣   |
|                           |  ║ $ curl -X POST \\   ║   |
| Inscription, génération   |  ║   https://api.dd...║   |
| de l'API key, crédit de   |  ║   -H "Authorization║   |
| test, premier SMS — sans  |  ║   ...              ║   |
| intervention humaine.     |  ║                    ║   |
|                           |  ║ {                  ║   |
| ✓ Crédit gratuit         |  ║   "id": "sms_...", ║   |
| ✓ Doc publique           |  ║   "status": "OK",  ║   |
| ✓ Webhooks signés        |  ║   "cost": 0.0089   ║   |
| ✓ Sandbox isolée         |  ║ }                  ║   |
|                           |  ╚═════════════════════╝   |
| [CTA: Lire la doc]       |                            |
+---------------------------+----------------------------+
```

- Le terminal a un fond **`$dd-code-bg`** (#0F1428) avec ombre généreuse
- Header avec 3 dots macOS style + titre `curl · POST /v1/sms/send`
- Syntax highlighting avec les couleurs définies en 2.2
- **Animation typing** : le code se "type" caractère par caractère au scroll-into-view (utiliser TypeIt.js ou custom)
- **Pas** un screenshot, le code est en HTML pour qu'il soit copiable et SEO-friendly

#### Section "Coverage"

Carte du monde stylisée façon "constellation" :

- SVG du monde en **outline subtle** (`stroke: $dd-ink-300`)
- Sur les pays couverts SMS : **cercles cyan animés** qui pulsent
- 3 cercles plus gros pour les pays de présence (CD Kinshasa, CI Abidjan, CG Brazzaville) avec label des bureaux
- **Stats à droite** : "Afrique · 54 pays · 47 corridors premium" / "Europe · 44 pays" / "Amérique · 35 pays" / "Asie · 41 pays"

Pour la carte SVG, recommandation à Claude Code : utiliser https://www.amcharts.com/svg-maps/ qui propose des SVG world maps en domaine public, ou simple-maps (https://github.com/zcreativelabs/react-simple-maps) si une approche JS est préférée. Le SVG doit être inline pour permettre le styling CSS.

#### Section "Pricing teaser"

3 cards. La carte du milieu est **mise en avant** par :
- Légèrement plus grande (transform: scale(1.04))
- Border cyan accent au lieu de gray
- Badge "Recommandé" en cyan sur fond cyan-100

Pour Dream Digital, la teaser cards :

```
[Découverte]              [Pay-as-you-go ⭐]           [Entreprise]
$0 / inscription           $0.0089 / SMS RDC            Sur-mesure
                           
Crédit de test offert      Tarif unitaire indicatif     Volumes >1M/mois
                           Tarifs dégressifs >10k/mois   SIP/Voice + CC
                           
✓ Crédit de test          ✓ Sender ID dédié           ✓ Tarification négo
✓ Sender ID test          ✓ Recharge en ligne         ✓ Account manager
✓ API complète            ✓ Webhooks DLR               ✓ SLA 99.95%
✓ Support email           ✓ Support FR/EN             ✓ Support 24/7
                           ✓ SLA 99.9%                  ✓ SMPP / SS7
                           
[Commencer]               [Démarrer]                   [Nous contacter]
```

#### Footer riche

5 colonnes :

| Col 1 (large) | Col 2 | Col 3 | Col 4 | Col 5 |
|---|---|---|---|---|
| Logo Dream Digital + paragraphe + boutons sociaux | **Produits** : SMS, Voice, DID, SIP, Dialo, eSIM | **Développeurs** : Documentation, SDK, Status, Changelog | **Société** : À propos, Carrières, Blog, Contact | **Légal** : Conditions, Confidentialité, DPA, Anti-fraude |

Sous-footer (copyright + versions + bureaux) :

```
© 2026 Dream Digital. Kinshasa · Abidjan · Brazzaville.
Build 4af2c19 · v2.0
```

Avec en plus :
- **Sélecteur de langue** (FR / EN) à droite
- **Statut services** mini-indicator : un dot vert + "Tous les services opérationnels" qui pointe vers /status

---

## 4. Components à coder en priorité

### 4.1 Components atomiques

Sous `resources/views/front/components/`, créer ces components Blade réutilisables (ils seront utilisés dans toutes les ~35 pages MVP du Sprint 1) :

| Component | Fichier | Usage |
|---|---|---|
| Hero split avec slider | `hero-split.blade.php` | Home + pages produits |
| Hero simple (1 colonne) | `hero-simple.blade.php` | Pages éditoriales (À propos, Blog) |
| Hero gradient banner | `hero-banner.blade.php` | Pages légales, FAQ |
| Trust strip marquee | `trust-strip.blade.php` | Sous tous les hero importants |
| Stats strip | `stats-strip.blade.php` | Home + page À propos |
| Service grid (6 cards) | `service-grid.blade.php` | Home |
| Industry grid (4 cards) | `industry-grid.blade.php` | Home + page Solutions |
| Code preview (avec typing) | `code-preview.blade.php` | Home + Developer + pages produits |
| Coverage map | `coverage-map.blade.php` | Home + page SMS + page Voice |
| Pricing cards (3 colonnes) | `pricing-cards.blade.php` | Home + page Tarifs |
| Testimonial cards | `testimonials.blade.php` | Home + landing |
| CTA banner | `cta-banner.blade.php` | Bas de toutes les pages produits |
| Country/Language switcher | `country-language-switcher.blade.php` | Header |
| Geo-detection banner | `geo-detection-banner.blade.php` | Tout site |
| FAQ accordion | `faq-accordion.blade.php` | Pages produits + FAQ |
| Feature list with icons | `feature-list.blade.php` | Pages produits |

### 4.2 Components signature ITSP

Trois components qui font la signature visuelle Dream Digital, à coder spécifiquement pour le secteur :

#### a) `signal-indicator.blade.php`

Petit point qui pulse pour les KPI temps réel :

```html
<span class="dd-signal-indicator dd-signal-{{ $status }}">
    <span class="dd-signal-dot"></span>
    <span class="dd-signal-pulse"></span>
    <span class="dd-signal-label">{{ $label }}</span>
</span>
```

```scss
.dd-signal-indicator {
    display: inline-flex; align-items: center; gap: 6px;
    
    .dd-signal-dot {
        width: 8px; height: 8px; border-radius: 999px;
        background: $dd-success; // ou autre selon status
    }
    .dd-signal-pulse {
        position: absolute;
        width: 8px; height: 8px; border-radius: 999px;
        background: $dd-success;
        animation: dd-pulse 2s ease-out infinite;
    }
}
@keyframes dd-pulse {
    0% { transform: scale(1); opacity: 0.6; }
    100% { transform: scale(2.5); opacity: 0; }
}
```

#### b) `corridor-card.blade.php`

Card spécifique pour afficher un corridor SMS (FR → CD = "France vers RDC") :

```
+------------------+
| 🇫🇷 → 🇨🇩      |
| France vers RDC  |
| $0.025 par SMS   |
| ●●●●○ Premium    |
+------------------+
```

#### c) `live-feed.blade.php`

Petit ticker qui montre des "événements" télécom en direct (à animer avec JavaScript et données simulées au début) :

```
[●] SMS livré · 🇨🇮 Côte d'Ivoire · il y a 2s
[●] OTP envoyé · 🇨🇩 RDC · il y a 4s
[●] Voice connecté · 🇨🇬 Brazzaville · il y a 7s
[●] eSIM activée · 🇫🇷 Roaming · il y a 9s
```

Donne un côté "plateforme vivante 24/7" très ITSP.

---

## 5. Plan d'exécution Claude Code

### Étape 1 — Étude des références (jour 1, matin) — OBLIGATOIRE

**Avant tout code**, Claude Code doit :
1. Visiter les 6 sites de référence (section 1.3)
2. Prendre des screenshots des hero, sections produits, code previews
3. Documenter dans `DESIGN_REFERENCES.md` ses observations
4. Proposer 3 directions visuelles distinctes pour Dream Digital basées sur ces observations
5. **STOP** : attendre validation PO avant de coder

### Étape 2 — Système de design (jour 1 après-midi)

Une fois la direction validée, implémenter le système de design :
1. Modifier `_variables.scss` avec les tokens de la section 2.2-2.5
2. Vérifier que Inter (300-800) + JetBrains Mono (400-600) sont bien chargés via les Google Fonts ajoutés en `commonMaster.blade.php` (S3). Pas de réimport nécessaire — les fonts sont déjà actives. Si manquantes, vérifier `commonMaster.blade.php:60-64`.
3. Créer `_typography.scss`, `_spacing.scss`, `_animations.scss`
4. Tester avec une page de demo qui affiche tous les tokens (couleurs, typo, shadows, spacings)
5. **VALIDATION PO** : screenshot de la page demo

### Étape 3 — Refonte hero home (jour 2)

1. Coder le hero split avec slider Swiper (4 slides)
2. Slide 1 : carte du monde animée
3. Slide 2 : terminal de code typing
4. Slide 3 : dashboard preview (mockup statique au début)
5. Slide 4 : mosaïque pays
6. Tester responsive mobile/tablet/desktop
7. **VALIDATION PO** : screenshots des 4 slides + version mobile

### Étape 4 — Sections principales home (jour 3)

Dans cet ordre, avec validation après chaque section :
1. Trust strip (marquee logos)
2. Stats strip (avec animation counters)
3. Service grid (6 cards style "industrial")
4. Developer-first split (avec code typing)
5. Coverage map
6. **VALIDATION PO** après chaque section

### Étape 5 — Sections complémentaires home (jour 4)

1. Industry grid (4 cards solutions)
2. Pricing teaser (3 cards)
3. Testimonials (3 cards)
4. CTA banner finale
5. Footer riche (5 colonnes)
6. **VALIDATION PO**

### Étape 6 — Components atomiques (jour 5)

Créer **tous** les components Blade réutilisables listés en section 4.1. Pour chacun, créer une fiche de demo dans `/preview/components/{component-name}` accessible en mode dev pour tester les variations.

**VALIDATION PO** : revue de toute la bibliothèque de components.

### Étape 7 — Components signature (jour 6)

1. signal-indicator (avec animation pulse)
2. corridor-card
3. live-feed (avec data simulée)
4. **VALIDATION PO**

### Étape 8 — Polish final (jour 6 fin)

1. Animations stagger fade-up au scroll
2. Smooth scroll
3. Hover lift sur toutes les cards
4. Performances : lazy loading images, font display swap
5. Accessibilité : focus visible, contraste WCAG AA, aria-labels
6. Responsive final : test sur 5 breakpoints (mobile 375, tablet 768, laptop 1024, desktop 1440, wide 1920)
7. **VALIDATION PO FINALE**

---

## 6. Critères d'acceptance globaux du Sprint 1.5

À la fin du sprint, on doit pouvoir cocher TOUTES ces cases :

- [ ] La home `/fr` (mode global) ressemble à un site CPaaS sérieux comparable à Plivo/Telnyx/Bandwidth (validation PO subjective mais ferme)
- [ ] Le hero a un slider Swiper fonctionnel avec 4 slides distincts
- [ ] La typography utilise bien Inter (300-800) + JetBrains Mono (400-600), héritées du Brand Kit v1.2 via S3
- [ ] La palette est appliquée cohéremment (cyan accent visible aux endroits clés)
- [ ] Le code preview a une animation typing au scroll-into-view
- [ ] La carte de couverture a des points cyan qui pulsent
- [ ] Tous les components atomiques (section 4.1) existent et ont une page de demo
- [ ] Les 3 components signature (signal indicator, corridor card, live feed) fonctionnent
- [ ] Le site est responsive sur mobile 375px à wide 1920px
- [ ] Performance : Lighthouse score > 85 sur Performance, > 95 sur Accessibility
- [ ] Aucun warning console dans le navigateur
- [ ] Tous les fichiers commitent en séquence atomique sur la branche
- [ ] `DESIGN_DECISIONS.md` documente les choix de design effectués
- [ ] `DESIGN_REFERENCES.md` documente les observations des sites de référence

---

## 7. Procédure de démarrage Claude Code

### 7.1 Prompt initial à donner à Claude Code

> Bonjour Claude Code. Tu vas m'aider à transformer la landing Sneat default en une vraie vitrine ITSP/CPaaS digne d'un opérateur télécom B2B sérieux comparable à Twilio, Plivo, Telnyx, Sinch, Bandwidth, Bird.
>
> **Lis attentivement le fichier `BRIEF_SPRINT_1_5_REDESIGN.md` à la racine** dans son intégralité. Puis crée un fichier `ANALYZE_SPRINT_1_5.md` qui contient :
>
> 1. Ta compréhension du sprint (objectif, ce que ça change par rapport à Sneat default, niveau d'ambition visuelle)
> 2. Les questions ou clarifications dont tu as besoin (notamment : photos d'équipe pays disponibles ? Logos clients à intégrer ? Les couleurs proposées sont-elles validées ?)
> 3. Ton plan d'attaque conforme aux 8 étapes décrites en section 5
> 4. Les risques techniques que tu identifies (par ex : Swiper config complexe, animation SVG world map performance)
>
> **Étape 1 OBLIGATOIRE avant tout code** : visite les 6 sites de référence (Twilio, Plivo, Telnyx, Sinch, Bandwidth, Bird) et produis le fichier `DESIGN_REFERENCES.md` documentant tes observations + 3 directions visuelles proposées pour Dream Digital. Ne touche à aucun fichier de code avant que je valide ce document.
>
> Une fois validé, tu enchaînes étape par étape (8 étapes section 5) avec **VALIDATION PO obligatoire** entre chaque, marquée par un commit Git atomique et une preview navigateur que je teste manuellement avant de te dire "go" pour la suite.
>
> Tu **n'as pas le droit** de passer à l'étape suivante sans mon validation explicite par message "VALIDÉ étape X".

### 7.2 Engagement du PO (Gentil)

Pour que ce sprint réussisse, le PO s'engage à :

1. **Tester chaque livraison** dans son navigateur (pas valider sur description)
2. **Donner un feedback ferme** : si une section ressemble encore trop à Sneat default, dire "non, refais"
3. **Comparer aux sites de référence** : "est-ce que cette section a le même niveau de finesse que Twilio ? si non, on refait"
4. **Ne pas valider par fatigue** : prendre 10 min pour vraiment tester, sinon on accumule de la dette visuelle
5. **Fournir les assets manquants** (logos clients, photos équipes, charte graphique officielle si elle existe)

### 7.3 Quand le sprint est-il terminé ?

Le sprint est terminé quand le PO ouvre la home dans son navigateur, fait défiler tout, et dit honnêtement : **"Si je montre ça à un de mes clients banque/retail, il croira que c'est un vrai site CPaaS comparable à Twilio."**

Pas avant.

---

## 8. Notes pratiques pour la suite

À la fin de ce sprint, le **Sprint 1 fondations** (multi-pays + i18n) reprend la main et utilise tous les components créés ici pour construire les ~35 pages MVP. La cohérence visuelle est assurée parce qu'on aura tous les blocs de construction prêts.

Le brief `BRIEF_SPRINT_1.md` (fondations) ne change pas en termes d'objectifs techniques — il s'appuiera juste sur les nouveaux components au lieu de partir des composants Sneat default.

---

## 9. Adaptation Scénario B (mise en ligne progressive)

Le PO a opté pour une mise en ligne progressive : **page d'attente Dream Digital cette semaine** sur dream-digital.info (substituant WordPress legacy), puis **bascule vers le site complet Sprint 1.5** la semaine suivante.

### Implication pour Claude Code

Le travail Sprint 1.5 commence directement par la **home complète** du site corporate (homepage avec les 12 sections décrites en Section 3.1). La page d'attente intermédiaire n'est PAS dans le scope Sprint 1.5 — elle sera traitée en **pré-S1.5 dans un mini-sprint dédié** de 4-6h (branche distincte `feature/landing-attente`).

### Conséquence sur la timeline

- **Semaine en cours** : pré-S1.5 (page d'attente, 4-6h, branche `feature/landing-attente`) → déploiement immédiat sur dream-digital.info, élimination WordPress.
- **Semaine suivante** : Sprint 1.5 démarre directement à l'Étape 1 (étude des 6 sites de référence) sans détour par une page d'attente. Branche `feature/sprint-1-5-redesign`.
- **Bascule** : à la fin de Sprint 1.5, déploiement remplace la page d'attente par le site complet (1 commit + push deploy.sh).

### Engagement PO

Le PO s'engage à :
- Valider visuellement la page d'attente avant déploiement (1 itération max avant production)
- Garder dream-digital.info accessible en mode "page d'attente" pendant 4-6 jours de Sprint 1.5
- Ne pas perturber le sprint 1.5 par des demandes de modification de la page d'attente (qui est temporaire par définition)

---

**FIN DU BRIEF SPRINT 1.5**

Pour toute question pendant l'exécution, mettre à jour `ANALYZE_SPRINT_1_5.md` et signaler les blocages au product owner avant de continuer.
