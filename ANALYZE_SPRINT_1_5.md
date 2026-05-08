# ANALYZE — Sprint 1.5 (Redesign vitrine ITSP/CPaaS)

> **Brief de référence** : `BRIEF_SPRINT_1_5_REDESIGN.md` (commit `73d076f`, amendements A-I appliqués)
> **Branche** : `feature/sprint-1-5-redesign`
> **Démarrage** : 2026-05-08 (vendredi, lendemain clôture pré-S1.5)
> **Durée estimée** : 4-6 jours supervisés
> **PO** : MAPENDO Gentil

---

## 1. Compréhension du sprint

**Objectif business** : transformer la landing Sneat default ("look tech corporate générique, agence digitale / SaaS de gestion") en une vitrine **ITSP/CPaaS B2B carrier-grade** comparable à Twilio / Plivo / Telnyx / Sinch / Bandwidth / Bird. Le visiteur doit, en moins de 5 secondes, **sentir** qu'il est sur le site d'un opérateur télécom sérieux — pas d'un outil de gestion ou d'une agence marketing.

**Ce que ça change vs Sneat default** : tout le visuel public. Hero gradient violet façon SaaS générique → split fullwidth carrier-grade avec slider de scènes pertinentes (carte mondiale, terminal de code, dashboard preview, mosaïque pays). Section produits cards "lévitation" → grille industrielle propre type "table de matières premium". Ajout des sections signature ITSP : trust strip (logos clients en marquee), stats strip (200+ pays / 99.95% uptime / latence / présence), developer-first split (code typing animé), coverage map (constellation), pricing teaser, témoignages, CTA finale. Footer 5 colonnes riche.

**Niveau d'ambition** : non-négociable — sprint terminé seulement quand le PO peut honnêtement dire *"Si je montre ça à un client banque/retail, il croira que c'est un vrai site CPaaS comparable à Twilio."* Pas de validation par fatigue. Refaire si insuffisant.

**Décisions actées (rappel jurisprudence)** :
- **Positionnement** : global CPaaS/ITSP (60%+ clients hors Afrique, 80%+ partenaires hors Afrique) — **pas panafricain**. Bureaux Kinshasa + Abidjan + Brazzaville mentionnés naturellement comme preuve d'opérations réelles.
- **Palette** : Brand Kit v1.2 active depuis S3 (`_custom-variables/_dream-digital.scss`) — Petrol Teal `#335F5F` (primary ~30%), Action Black `#0E121C` (secondary ~15%), Tertiary Cyan `#14B8A6` (spot ≤5%, max 3-4 occurrences/page). **Interdit** : `#2A4F9E`, `#00D9FF` (obsolètes brief original).
- **Typographie** : Inter unique family (300-800) + JetBrains Mono (400-600) — Bricolage Grotesque + Outfit abandonnés. Déjà chargés via Google Fonts en `commonMaster.blade.php:60-64` (S3).
- **Iconset** : Boxicons (Q13 S9-C3) — Tabler Icons indisponibles sans extension du plugin Vite.
- **Page d'attente Scénario B** : abandonnée au niveau exécution (déploiement direct du dashboard Sneat rebrandé en pré-S1.5, clôture 2026-05-08 commit `30740ec`). **MAIS** : le copy page d'attente rédigé en draft 2 le 2026-05-08 matin (tagline `"Voice. SMS. eSIM. And More."` · pitch 6 services SMS A2P/Voice Wholesale/DID/SIP Trunking/Dialo CC/eSIM Zone · partenaires neutralisés · footer 3 bureaux) **reste utilisable** et sera **intégré au hero + sections du nouveau design** Sprint 1.5. À ne pas réécrire from scratch — repartir de ce copy validé.

---

## 2. Questions / clarifications PO requises

Avant Étape 2 (système de design implémenté), j'ai besoin d'arbitrages sur les 5 points suivants. Mes hypothèses par défaut sont entre crochets — confirme/corrige.

### Q1 — Photos d'équipe pays
Le hero slide 4 (mosaïque pays) et la section coverage évoquent des photos d'équipe de Kinshasa / Abidjan / Brazzaville. **Hypothèse par défaut** : aucune photo d'équipe disponible → fallback à des **logos pays** (drapeaux SVG) + photos d'archive génériques skylines (Kinshasa CBD, Treichville, Brazzaville Bourg). Confirme cette voie OU dis-moi si tu fournis des photos d'équipe d'ici Étape 3.

### Q2 — Logos clients pour le trust strip
Le trust strip animé (marquee) prévoit 6-10 logos clients avec ton "Banques · Retail · Logistique". **Hypothèse par défaut** : pas de logos réels validés juridiquement à ce stade → utiliser des **placeholders SVG text** (Rawbank, Equity Bank, Vodacom, Carrefour CI, Pullman, DHL Africa) avec mention discrète "(visuels indicatifs, partenariats sous NDA)" pour préserver la posture commerciale. Confirme OU fournis une liste validée de 6-10 logos officiels.

### Q3 — Témoignages clients
La section témoignages prévoit 3 cards client (photo + quote courte + name + role). **Hypothèse par défaut** : pas de témoignages clients officiellement collectés et validés à ce stade → utiliser **3 placeholder testimonials neutralisés** (style "Banque partenaire RDC" / "Retailer panafricain" / "Logisticien régional" avec quotes génériques mais crédibles) avec photos avatar abstraites (UI Faces ou similaire). Confirme OU fournis 3 quotes réelles si dispo.

### Q4 — Coverage map : pays SMS réellement couverts
Le copy évoque "200+ pays couverts SMS" + "5 corridors africains premium". **Hypothèse par défaut** : on affiche ces chiffres au visiteur (carte mondiale avec ~200 points cyan animés, label "200+ pays") sans liste détaillée à ce stade — la liste précise par opérateur viendra dans Sprint 1 (page produit SMS dédiée). Confirme l'approche OU précise-moi le nombre exact de pays / corridors.

### Q5 — Code preview API
La section developer-first prévoit un terminal animé montrant un `curl POST /v1/sms/send` avec syntax highlighting et response JSON. **Hypothèse par défaut** : l'API publique Phoenix SMS n'est pas encore live → le code preview est **simulé crédible** (exemple curl réaliste, endpoint `https://api.dream-digital.info/v1/sms/send`, response JSON cohérente avec ce que l'API future exposera). Mention discrète "(API en preview, doc publique disponible Sprint 2)". Confirme l'approche OU précise si l'endpoint réel doit déjà être référencé.

---

## 3. Plan d'attaque (8 étapes alignées brief Section 5)

Validation PO obligatoire entre chaque étape (commit + push + screenshot/preview navigateur + message explicite "VALIDÉ Étape X, GO Étape suivante").

| # | Étape | Livrables | Validation |
|---|---|---|---|
| **1** | **Étude 6 sites de référence** (CETTE PHASE — phases 3+4 du brief Bloc 2) | `DESIGN_REFERENCES.md` à la racine + 3 directions visuelles A/B/C + recommandation Claude | PO tranche A/B/C ou hybride |
| **2** | **Système de design** | Vérification tokens Brand Kit v1.2 actifs (déjà OK post-S3) ; création `_typography.scss`, `_spacing.scss`, `_animations.scss` ; page demo `/preview/components/design-tokens` affichant tous les tokens | Screenshot demo |
| **3** | **Hero home split + slider Swiper** | 4 slides : (1) carte monde animée · (2) terminal code typing · (3) dashboard preview · (4) mosaïque pays. Côté gauche fixe (titre + sous-titre + CTAs + bullets), seule la "scène" droite tourne. Auto-rotate 6s, fade crossfade, pagination dots. Copy issu du draft 2 page d'attente 2026-05-08. | 4 screenshots (1 par slide) + version mobile |
| **4** | **Sections principales home** | Trust strip marquee · Stats strip (counters animés) · Service grid 6 cards industrielles · Developer-first split (code typing) · Coverage map constellation | Validation après chaque section |
| **5** | **Sections complémentaires home** | Industry grid 4 cards · Pricing teaser 3 cards · Témoignages 3 cards · CTA banner finale · Footer riche 5 colonnes | Validation finale home complète |
| **6** | **Components atomiques** | 16 components Blade réutilisables sous `resources/views/front/components/` (cf. brief Section 4.1) + page demo par component sous `/preview/components/{name}` | Revue bibliothèque components |
| **7** | **Components signature ITSP** | `signal-indicator.blade.php` (pulse animation) · `corridor-card.blade.php` · `live-feed.blade.php` (ticker simulé) | Validation visuelle |
| **8** | **Polish final** | Stagger fade-up scroll · smooth scroll · hover lift cards · lazy loading images · font display swap · focus visible WCAG AA · responsive 5 breakpoints (375/768/1024/1440/1920) · Lighthouse Performance >85 + Accessibility >95 · console F12 propre | Validation finale + tag release |

**Source-of-truth copy** : draft 2 page d'attente 2026-05-08 (tagline + pitch 6 services + partenaires neutralisés + footer 3 bureaux) → réutilisé en Étapes 3-5 sans réécriture.

---

## 4. Risques techniques identifiés

| # | Risque | Impact | Mitigation |
|---|---|---|---|
| **R1** | **Swiper config complexe** : 4 slides avec animations distinctes (carte animée vs terminal typing vs dashboard PNG vs mosaïque), sync auto-rotate + pause hover + pagination minimaliste | Moyen — risque slider qui "saccade" ou animations désynchronisées | Coder chaque slide en composant Blade isolé. Utiliser Swiper effect `'fade'` avec `crossFade: true` au lieu de slide horizontal. Tester transitions 1→2 puis 2→3 séparément avant intégration. |
| **R2** | **Animation SVG world map performance** : 200+ points cyan qui pulsent simultanément peuvent provoquer jank / low-fps, surtout sur mobile entry-level | Élevé — section coverage est centrale, dégradation perf très visible | Limiter à 50-80 points visuellement (sub-sample des 200+ pays) animés via `requestAnimationFrame` ou CSS `animation` avec `will-change: transform, opacity`. Pause animation `prefers-reduced-motion`. Tester sur Chrome DevTools mobile throttle. |
| **R3** | **Code typing effect sync scroll observer** : `IntersectionObserver` doit déclencher le typing au scroll-into-view, et le typing doit pouvoir se "rejouer" si scroll out puis back, sans glitch | Moyen — section developer-first est signature, glitch ruine l'effet "wow" | Implémenter avec un état `'idle' / 'typing' / 'done'` explicite dans le component. Reset typing si `intersection.isIntersecting === false` ET `intersection.boundingClientRect.top > 0` (scrolled past). Préférer custom JS court à TypeIt.js (moins de dépendances). |
| **R4** | **Cohérence Brand Kit v1.2** : risque de retomber dans les couleurs obsolètes (`#2A4F9E`, `#00D9FF`) du brief original par mémoire musculaire ou copy/paste de doc tierce | Élevé — incohérence chromatique annule le travail S3 | Avant chaque commit Étape 2-8, exécuter `grep -ri "#2A4F9E\|#00D9FF" resources/` (devrait retourner zéro hit en code, hors mentions négation explicites). Référencer uniquement `var(--bs-primary)`, `var(--bs-info)`, `$dd-primary-500`, `$dd-tertiary-500` — jamais hard-code hex. |
| **R5** | **Boxicons disponibilité** : les 6 noms d'icônes prévus dans le brief amendement F (`bx-message-rounded`, `bx-message-detail`, `bx-phone-call`, `bx-numbers`, `bx-network-chart`, `bx-support`, `bx-microchip`, etc.) — vérifier qu'ils existent tous dans la version Boxicons chargée par `vite.icons.plugin.js` post-S9 | Moyen — icônes manquantes obligent à fallback générique, dégrade le feel premium | Avant Étape 4 (service grid), faire un check rapide en console : `document.querySelectorAll('.bx-message-detail').length` après chargement test page. Documenter le set de 6 icônes choisies dans `DESIGN_DECISIONS.md`. Fallback prévu : alternatives listées dans amendement F. |

**Risques non-techniques à signaler** :
- **Disponibilité assets** (Q1 photos, Q2 logos, Q3 témoignages) — bloque qualité finale si arbitrage Q1-Q3 retardé.
- **Validation PO par fatigue** — discipline mentionnée brief Section 7.2.4 : prendre 10 min réelles à chaque revue, pas valider sur description.

---

*Document créé en début de Sprint 1.5. À mettre à jour si situation imprévue ou décision PO changeante (jurisprudence Q14-Q21 du Sprint 0).*
