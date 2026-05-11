# Brand Kit — Dream Digital
> Version 1.2 — date : 2026-05-05 — statut : VALIDÉ pour exécution

> Ce Brand Kit consolide les décisions visuelles validées par le Product Owner (Lukoo Mapendo Gentil, Founder & CEO Dream Digital) après itération sur 3 versions. Il sert de référence officielle pour tous les développements visuels du nouveau site corporate Dream Digital.

---

## 1. Identité de marque

**Nom officiel** : Dream Digital SARL
**Type** : Opérateur télécom CPaaS/ITSP global
**HQ** : Kinshasa, RDC
**Bureaux opérationnels** : Abidjan (Côte d'Ivoire), Brazzaville (Congo)
**Couverture** : 200+ pays
**Profil clientèle** : 60%+ hors Afrique, 80%+ partenaires hors Afrique

**Positionnement** : opérateur télécom programmable global, ancré à Kinshasa, comparable au modèle Sinch/Bird/Plivo.

**Tagline officielle** :
- EN : Voice. SMS. eSIM. And More.
- FR : Voix. SMS. eSIM. Et bien plus.

---

## 2. Logos disponibles

| Fichier source | Renommage cible | Usage |
|---|---|---|
| DD-01-PNG.png | logo-dd-vertical.png | Logo vertical |
| DD-02__2_.png | logo-dd-horizontal.png | Logo horizontal ★ PRINCIPAL |
| DD-03_-ico.png | logo-dd-icon.png | Favicon source |
| DD-04.png | logo-dd-wordmark.png | Wordmark seul |

À placer dans `public/img/brand/` après renommage.

**Roadmap d'évolution** :
- Phase 1 : PNG actuels (immédiat)
- Phase 2 : conversion SVG (3-6 mois, ~50-200 USD)
- Phase 3 : refonte logo pro (6-12 mois, 3 000-15 000 EUR)

---

## 3. Système couleurs — 4 couleurs hiérarchisées

### 3.1 Vue d'ensemble

| Couleur | Code | Rôle | Usage |
|---|---|---|---|
| **Primary** Petrol Teal | `#335F5F` | Marque, branding | ~30% |
| **Secondary** Action Black | `#0E121C` | CTAs critiques, texte fort | ~15% |
| **Tertiary** Teal-Cyan | `#14B8A6` | Spot accent, liens, highlights | ~5% |
| **Foundation** Whites + neutres | `#FFFFFF` + neutres | Surfaces, body text | ~50% |

**Règle d'or** : la couleur tertiaire doit rester rare. Pas plus de 3-4 occurrences par page.

### 3.2 PRIMARY — Petrol Teal #335F5F

```
Primary 50:   #F0F4F4
Primary 100:  #DCE6E6
Primary 200:  #BCD0D0
Primary 300:  #9CBABA
Primary 400:  #6E9999
Primary 500:  #335F5F   ← Signature
Primary 600:  #2A4F4F
Primary 700:  #224242
Primary 800:  #1A3535
Primary 900:  #0F2222
```

**Usages** : logo, sidebar admin, titres de marque, icônes primaires, backgrounds tints, bordures focus, tags catégoriels.

### 3.3 SECONDARY — Action Black #0E121C

```
Hex: #0E121C
RGB: 14, 18, 28
```

**Usages** : boutons CTA primaires, headings principaux, texte body fort, terminal de code, backgrounds premium (eSIM card par exemple).

**Note** : pas du noir pur #000000 — légèrement teinté pour adoucir sur écrans modernes.

### 3.4 TERTIARY — Teal-Cyan #14B8A6 (SPOT)

```
Tertiary 50:   #ECFDF7
Tertiary 100:  #CCF8EE
Tertiary 200:  #99F1DD
Tertiary 300:  #5EE3C6
Tertiary 400:  #2DC9AB
Tertiary 500:  #14B8A6   ← Spot
Tertiary 600:  #0F9988
Tertiary 700:  #0F766E
Tertiary 800:  #115E56
Tertiary 900:  #134E48
```

**Usages AUTORISÉS uniquement** :
- Liens textuels actifs dans contenu éditorial (pas dans nav)
- Soulignement actif item de menu courant
- Badges "NEW" sur sections nouvellement lancées
- Live indicators (pulse API status)
- Mots-clés highlightés dans le code
- Focus rings sur inputs (accessibilité)
- Coches "step completed" wizards onboarding

**STRICTEMENT INTERDIT** :
- ❌ Boutons CTA primaires (utiliser teal ou noir)
- ❌ Backgrounds de cards courantes
- ❌ Headings ou titres
- ❌ Texte body courant
- ❌ Bordures containers (sauf focus state)

**Règle de surface** : maximum 5% de surface visible totale par écran.

### 3.5 FOUNDATION — Surfaces et neutres

```
Surface primary:    #FFFFFF
Surface secondary:  #F7F8FA
Surface tertiary:   #EFF1F5

Ink 50:    #F7F8FA
Ink 100:   #E4E7EE
Ink 200:   #C9CFD9
Ink 300:   #A5ADBC
Ink 400:   #7B8395
Ink 500:   #5A6275
Ink 600:   #424A5C
Ink 700:   #2D3340
Ink 800:   #1A1F2A
Ink 900:   #0E121C   ← Identique au Secondary Action Black
```

### 3.6 Couleurs sémantiques (status)

```
Success:  #0EBE82    → bg #E5F8F1
Warning:  #F2A93B    → bg #FCF1DE
Danger:   #EF4361    → bg #FCE4E8
Info:     #3A86FF    → bg #E0EBFF
```

**Réservées strictement aux indicateurs de statut**, jamais comme couleurs de marque.

---

## 4. Typographie

### Paire validée : Inter + JetBrains Mono

```
Headings:   Inter (weights 600, 700, 800)
Body:       Inter (weights 400, 500, 600)
Mono/code:  JetBrains Mono (weights 400, 500, 600)
```

### Import Google Fonts

À ajouter dans `<head>` de `commonMaster.blade.php` :

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
```

### Hiérarchie typographique

```
Display 1 (hero h1):   clamp(2.5rem, 6vw, 5rem)        weight 700  ls -0.02em  lh 1.1
Display 2 (sections):  clamp(2rem, 4.5vw, 3.5rem)      weight 700  ls -0.02em  lh 1.15
H1 (page titles):      clamp(1.75rem, 3.5vw, 2.5rem)   weight 600  ls -0.01em  lh 1.2
H2 (subsections):      clamp(1.5rem, 2.5vw, 2rem)      weight 600  ls -0.01em  lh 1.25
H3 (cards titles):     1.25rem                          weight 600  ls 0       lh 1.3
Body large (lead):     1.125rem                         weight 400  ls 0       lh 1.65
Body (default):        1rem                             weight 400  ls 0       lh 1.6
Body small:            0.875rem                         weight 400  ls 0       lh 1.55
Caption (helper):      0.75rem                          weight 500  ls 0.04em  lh 1.4
Label (uppercase):     0.75rem                          weight 600  ls 0.08em  lh 1.4  uppercase
Code inline:           0.875rem                         weight 400  font JetBrains Mono
```

---

## 5. Iconographie

**Tabler Icons** (déjà inclus dans Sneat) — https://tabler-icons.io/
- Stroke width: 1.5px par défaut
- Couleur: `currentColor`
- Tailles: 16px / 20px / 24px / 32px
- Status critiques: versions "filled"

---

## 6. Espacements et grille

```
xs:     4px     (0.25rem)
sm:     8px     (0.5rem)
md:     12px    (0.75rem)
base:   16px    (1rem)
lg:     24px    (1.5rem)
xl:     32px    (2rem)
2xl:    48px    (3rem)
3xl:    64px    (4rem)
4xl:    96px    (6rem)
5xl:    128px   (8rem)

Container max:     1280px
Container narrow:  960px

Section py-sm:    clamp(3rem, 6vw, 5rem)
Section py-md:    clamp(5rem, 10vw, 8rem)
Section py-lg:    clamp(6rem, 14vw, 12rem)
```

---

## 7. Border radius

Style **carrier-grade industrial** : modérés, pas trop arrondis.

```
None:    0
xs:      4px      badges, tags petits, inputs
sm:      6px      boutons small
md:      8px      boutons default, cards courantes ★ signature
lg:      12px     cards mises en avant, modals
xl:      16px     sections premium
2xl:     24px     hero blocks rares
full:    9999px   avatars, pills
```

---

## 8. Ombres

Philosophie **"Tonal Layers"** : bordures fines plutôt qu'ombres lourdes.

```
Shadow none:    aucune (par défaut sur cards statiques)
Shadow sm:      0 1px 2px rgba(14,18,28,0.04), 0 1px 3px rgba(14,18,28,0.06)
Shadow md:      0 4px 12px rgba(14,18,28,0.06), 0 2px 4px rgba(14,18,28,0.04)
Shadow lg:      0 12px 32px rgba(14,18,28,0.08), 0 4px 8px rgba(14,18,28,0.04)
Shadow xl:      0 24px 48px rgba(14,18,28,0.10), 0 8px 16px rgba(14,18,28,0.06)
Shadow focus:   0 0 0 3px rgba(20, 184, 166, 0.25)   ← cyan focus ring
```

**Règle** : par défaut pas d'ombre, utiliser `border: 1px solid var(--ink-100)`. Ombre uniquement au hover ou pour éléments flottants.

---

## 9. Animations et transitions

```
Easings:
ease-out:        cubic-bezier(0.2, 0.8, 0.2, 1)     ← 90% des cas
ease-in-out:     cubic-bezier(0.4, 0, 0.2, 1)
ease-bounce:     cubic-bezier(0.68, -0.55, 0.265, 1.55)

Durations:
fast:            140ms
normal:          240ms
slow:            480ms
slower:          800ms
```

---

## 10. Tone of voice

**Principes** :
1. Direct et précis (pas de jargon marketing creux)
2. Confiance carrier-grade (vocabulaire opérateur télécom)
3. Sans condescendance (parler à des décideurs avertis)
4. Bilingue parfait (chaque message natif FR ET EN)
5. Action verbs ("Connectez", "Programmez", "Activez")

**À éviter** :
- "solution" (vide de sens en B2B tech)
- Superlatifs creux ("la meilleure", "leader")
- Abstractions ("transformer votre business")
- Emojis dans corporate
- "Afrique" comme limitation géographique

**À privilégier** :
- Chiffres concrets (pricing, latence, volumes)
- Verbes d'action
- Cas d'usage réels
- Témoignages vérifiables

---

## 11. Mascotte — Pango le pangolin

**Statut** : mascotte secondaire, version provisoire SVG.

**Concept** : pangolin africain stylisé aux écailles hexagonales en teal Dream Digital, casque télécom.

**Cas d'usage** :
- Page 404, empty states, loaders
- Documentation API (mascotte tutoriel)
- Stickers et merch
- Réseaux sociaux
- Email marketing

**Cas interdits** :
- Hero du site corporate
- Pages produit B2B
- Pages légales et formelles
- Animations excessives

**Évolution** : production pro recommandée à terme (500-2000 USD).

---

## 12. Ce qu'on n'utilise PAS

❌ Effets d'ombre portée flou Photoshop 2010
❌ Gradients colorés multicolores criards
❌ Illustrations cartoon enfantines
❌ Stock photos génériques
❌ Polices fantaisie
❌ Couleurs néon hors palette
❌ Border radius excessif (>24px)
❌ Animations excessives
❌ Plus de 5% de surface en cyan tertiaire
❌ Mention "Afrique" comme limitation

---

## 13. Implementation technique

**Fichier source actif** : `resources/assets/vendor/scss/_custom-variables/_dream-digital.scss`

Intégré en S3 dans la chaîne SCSS Sneat (importé au top de `_bootstrap-extended.scss`),
étendu en Sprint 1.5 avec keyframes nommés (`dd-pulse`, `dd-fade-up`) + guard
`prefers-reduced-motion` (WCAG 2.2.2).

À importer en TÊTE de `_bootstrap-extended.scss` :
```scss
@import "./dream-digital";
```

Tous les tokens accessibles via variables Sass `$dd-*`.

---

## 14. Checklist de validation des designs

Avant validation d'un écran/composant :

- [ ] Les 4 couleurs respectent leur ratio (~30% / 15% / 5% / 50%)
- [ ] Le cyan tertiaire apparaît au maximum 3-4 fois par vue
- [ ] Les CTAs primaires utilisent teal ou noir, pas cyan
- [ ] Les headings utilisent Inter, le code utilise JetBrains Mono
- [ ] Les border radius sont entre 4 et 12px
- [ ] Pas d'ombre lourde sur cards statiques
- [ ] Aucune mention "Afrique" comme limitation
- [ ] Tagline traduite si version FR
- [ ] Pango n'apparaît pas dans contextes inappropriés

---

## 15. Historique des versions

- **v1.0** : palette extraite logos, primary #2A6F76, accent cyan vif #00D9C4
- **v1.1** : fusion Stitch, primary #335F5F plus profond, mascotte Pango
- **v1.2** (FINALE) : système 4 couleurs hiérarchisé, secondary noir #0E121C, tertiary cyan posé #14B8A6 en spot 5%

---

*Brand Kit Dream Digital v1.2 — Document de référence officiel pour tous les développements visuels.*
