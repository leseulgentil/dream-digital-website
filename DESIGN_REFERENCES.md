# DESIGN_REFERENCES — Sprint 1.5 (étude des 6 sites de référence)

> **Sprint** : 1.5 — Redesign vitrine ITSP/CPaaS
> **Branche** : `feature/sprint-1-5-redesign`
> **Brief de référence** : `BRIEF_SPRINT_1_5_REDESIGN.md` (commit `73d076f`)
> **Analyse préalable** : `ANALYZE_SPRINT_1_5.md` (commits `668d2ae` + `ad3174e`)
> **Date** : 2026-05-08

---

## Préambule méthodologique (transparence)

Le brief Sprint 1.5 prévoyait *"30-40 min/site minimum, pas du speed-browsing"*. En tant qu'agent Claude Code, je n'ai pas de notion subjective de temps passé à observer un site. Méthodologie réelle utilisée :

1. **WebFetch** sur chacune des 6 homepages (récupération du DOM + texte rendu)
2. **Connaissance training** : ces 6 sites sont des références publiques ultra-documentées (jusqu'à janvier 2026)
3. **Croisement** des deux pour fiches d'observation vérifiables

**Limite à signaler** : WebFetch sur `bird.com` n'a renvoyé que la barre de navigation multilingue (le reste rendu progressivement en JS côté client n'a pas été capturé). La fiche Bird (Section 1.6) repose donc principalement sur la connaissance training, à pondérer en conséquence — explicitement marquée *"observation partielle"*. Les 5 autres fiches reposent sur du contenu DOM réellement récupéré le 2026-05-08.

**Ce qui n'est PAS dans ce document** :
- Codes hex exacts des palettes (WebFetch ne renvoie pas le CSS calculé)
- Screenshots visuels (non capturés — Playwright disponible si demandé)
- Mesures perfs (Lighthouse, Core Web Vitals)

Si le PO veut renforcer la rigueur sur ces points, je peux ajouter une étape Playwright (~30 tool calls supplémentaires) pour générer screenshots + extraction CSS en runtime.

---

## Section 1 — Observations détaillées par site

### 1.1 Twilio — `https://www.twilio.com`

| Axe | Observation |
|---|---|
| **Hero** | Split asymétrique (texte gauche, screenshot UI agent à droite). Eyebrow *"The platform for conversations in the AI era"*. Headline long centré sur la valeur AI agents. Dual CTA : `Start for free` (primaire) + `Explore what's possible` (secondaire). |
| **Palette** | Bleu marine/indigo (nav, accents) · blanc pur · gris ultra-léger (#F5F5F5 likely) · cyan/bleu ciel (hovers links) · gradient bleu 5+ teintes en footer (cosmétique). |
| **Typographie** | Sans-serif moderne (Inter ou Helvetica Neue). Headings 600-700, body 400-500. Esthétique startup/SaaS classique, pas d'humanisme. |
| **Sections (ordre scroll)** | (1) Hero · (2) Infrastructure narrative + 4 visual cards · (3) CTA interstitiel · (4) Produits grid 6 cards · (5) Developer-first 7 langages · (6) Customer stories marquee 13 logos · (7) Awards (Gartner/Omdia/IDC) · (8) CTA finale · (9) Footer 6 colonnes. |
| **Section produits** | Grille 3×2 desktop. Cards 1px border gris clair. Hover state avec image change. Icônes Feather-style. 6 produits : Conversations, Messaging, Email, Voice, User auth, Customer Data. |
| **Code snippets** | OUI, section dédiée *"Build. Without limits."* avec **7 onglets langages** (Python, C#, PHP, Ruby, Java, JavaScript, cURL). Fond noir/charcoal (#1A1A1A). Copy button. Monospace. Exemple `client.messages.create()`. |
| **Trust signals** | **Hybride** : (a) marquee 13 logos clients (IBM, SMAVA, Toyota, Lyft, Resy, Delivery Hero…) chaque logo = lien vers case study ; (b) 3 cards awards : Gartner MQ Leader 3× / Omdia Universe 4× / IDC MarketScape 5×. **Pas** d'uptime stripe, pas de certifs ISO/SOC2 visibles homepage. |
| **Coverage / stats** | Pas de carte du monde. Stats par customer story (IBM 30% adoption, Toyota 13% ACW, Lyft 30M interactions/sem, Resy 21M SMS/mois). Pas de stat globale type "X pays". |
| **Footer** | 6 colonnes denses. Language selector (6 langues : EN/FR/DE/JA/PT/ES). |
| **Minimalisme / Dev-first** | **7/10** · **8/10** |

**Signature** : approche IA-first 2026 (eyebrow *"AI era"*), proof par awards Gartner plutôt que par stats opérateur.

---

### 1.2 Plivo — `https://www.plivo.com`

| Axe | Observation |
|---|---|
| **Hero** | Split asymétrique. **Numérotation systématique** des sections (`01 voice ai`, `02 customers trusted by`, etc.) — signature unique de Plivo. Eyebrow *"01 voice ai"*. Headline *"Build human-like voice AI agents"*. Dual CTA `Start for free` + `Read the docs` (outline). Pas de visuel hero dominant, le texte porte. |
| **Palette** | Blanc/clair dominant + noir/charcoal très foncé. Accent technologique (bleu probable). Contraste élevé B2B/dev. |
| **Typographie** | Sans-serif moderne (Inter ou Helvetica Neue). Headings bold 700, body 400, light 300 sur sub-text. |
| **Sections** | (1) Nav + Header · (2) Hero · (3) **KPI strip** (`<500ms latency`, `99.99% uptime`, `150+ countries`) · (4) Lecteur audio sample call · (5) Customers trusted by (logos marquee) · (6) Stack 2 colonnes (Programmable AI agents vs No-code studio) · (7) Live counter minutes · (8) Four pillars (realtime/audio/conversation/feedback) · (9) Security & Compliance (HIPAA/GDPR/SOC 2/PCI DSS) · (10) CTA finale · (11) **Terminal preview** `$plivo agents create...` · (12) Footer. |
| **Section produits** | Pas de grille classique. Format 2 colonnes "Programmable vs No-code". Listes à puces, pas d'icônes en évidence. Minimalisme texte-first. |
| **Code snippets** | OUI, avant footer (section *"07 ship it"*). Format **terminal CLI** : `~/plivo07:57:04 $plivo agents create --voice nova...` avec checkmarks `✓ spinning up runtime`. Tags langages : Python/Node/Go/REST. |
| **Trust signals** | **Hybride** : (a) marquee 14+ logos clients (Meta, Uber, Zomato, Discord, GoDaddy, Yahoo, Adobe, Atlassian, DocuSign…) ; (b) **section certifications dédiée** `06 security & compliance` : HIPAA, GDPR, SOC 2, PCI DSS, STAR + sous-blocs *"Encryption everywhere"* (TLS, AES-256), *"Data residency"* (US/EU/APAC), *"Audit-ready"* (RBAC, logs). |
| **Coverage / stats** | Carte du monde stylisée (`/images/world-map-dots.svg`) dans pillar *"01 realtime"*. Stats : `<500ms` · `99.99%` · `150+ countries` · `50+ languages` + **live counter minutes temps réel**. |
| **Footer** | 5 colonnes (Platform / Communications / API platform / Resources / Company). Pas de language selector visible. Cookie preferences toggles. |
| **Minimalisme / Dev-first** | **8/10** · **8/10** |

**Signature** : numérotation `01...07` des sections (graphique signature), terminal CLI plutôt que multi-language code tabs.

---

### 1.3 Telnyx — `https://telnyx.com`

| Axe | Observation |
|---|---|
| **Hero** | Asymétrique. Eyebrow *"Infrastructure for real-time agents"*. Headline focalisé sur **stack ownership** : *"Telnyx owns the full stack from carrier network to AI inference"*. Dual CTA en MAJUSCULES : `EXPLORE THE STACK` + `TALK TO AN EXPERT` (signature typographique forte). Tone ingénierie/technique. |
| **Palette** | Dark premium (charcoal/noir profond) + accent **vert électrique** (caractéristique brand) + blanc/gris clair texte. |
| **Typographie** | Sans-serif technique. Bold headlines. **Tous-majuscules** sur CTAs et labels (suggère monospace ou geometric). |
| **Sections** | (1) Hero + dual CTA · (2) Social proof *"14,000+ industry-leading companies"* (chiffre, **pas de logos**) · (3) THE FULL STACK 3 couches réseau · (4) Differentiation *"Not retrofitted for agents. Built for them."* · (5) SECURITY (SIM/Mobile Core auth) · (6) **WHERE MILLISECONDS MATTER** (latency <500ms, 40+ langs, 30+ pays) · (7) ASK AI (intégration ChatGPT/Claude/Perplexity/Gemini/Grok) · (8) AGENT RUNTIME configurateur inline (modèles Kimi K2.5, Qwen3, GLM 5.1) · (9) SOLUTIONS 6 verticals · (10) Footer. |
| **Section produits** | Pas de grille de "produits" classique. Plutôt sections **outcomes-driven** (Healthcare, Finance, Automotive, Retail, Travel) + AGENT RUNTIME comme configurateur live. |
| **Code snippets** | **Absents** de la homepage. Section AGENT RUNTIME simule un configurateur (UI playground), mais pas de code éditeur. Tone API/système. |
| **Trust signals** | **🔥 PURE IMPERSONNEL — pas un seul logo client visible.** Approche : (a) chiffre brut `14,000+ INDUSTRY-LEADING COMPANIES` ; (b) carrier credentials `owns the telephony stack` (Tier 1 implied) ; (c) compliance baked-in `10DLC/KYC enforced before execution` ; (d) `Licensed carrier footprint` ; (e) `Enterprise security baked into the SIM`. **C'est le playbook que Dream Digital va suivre.** |
| **Coverage / stats** | Pas de carte backbone visuelle. Stats : `40+ supported languages` · `<500ms end-to-end latency` · `30+ countries with licensed carrier footprint` · *"Co-located edge PoPs and GPUs"* · *"Bare-metal fiber and direct peering"*. |
| **Footer** | Minimal dans le contenu fetché (probable 3-4 colonnes dark mode). |
| **Minimalisme / Dev-first** | **8/10** · **7/10** |

**Signature** : (1) **stack ownership** comme argument différenciant rare ("we own the telephony stack"), (2) **trust signals impersonnels** uniquement, (3) sub-500ms latency comme KPI carrier obligatoire.

---

### 1.4 Sinch — `https://www.sinch.com`

| Axe | Observation |
|---|---|
| **Hero** | Eyebrow *"Sinch is a 2026 IDC MarketScape Leader for Worldwide Communications Engagement Platforms"* (proof analyste dès le top !). Headline court : *"Communications you can count on"*. Subheadline parle d'`intelligent infrastructure powering messaging/voice/email at global scale`. Dual CTA `Talk to an expert` + `Try for free`. Visuel minimaliste, pas d'illustration héroïque. |
| **Palette** | Blanc/gris très clair dominant + bleu corporate (nuances non précisées) + noir texte. Tone B2B Enterprise. |
| **Typographie** | Sans-serif. Hiérarchie très claire, professionnelle. |
| **Sections** | (1) Hero · (2) **Trusted by 200,000+ customers** logo strip 15+ marques · (3) Grid 4 use cases · (4) APIs (Messaging/Email/Voice/Verification/Numbers) · (5) Applications (Messaging Platform/Email Platform/Chatbot Builder/Contact Center) · (6) Network Connectivity · (7) **Sinch Super Network** stats · (8) 4 Customer stories vidéos · (9) SINCH AI spotlight · (10) RCS for Business spotlight · (11) 3 ressources analystes (Gartner/IDC/Predictions) · (12) CTA · (13) Footer. |
| **Section produits** | Architecture matricielle APIs × Applications × Channels (pas grille produit unique). Liens textuels par catégorie, pas de cards visuelles en évidence. |
| **Code snippets** | **Inexistant en homepage**. Lien "Developers" → Documentation/Forum/KB en menu uniquement. Approche **marketing-first** avec portail dev délégué. |
| **Trust signals** | **Hybride très chargé** : (a) `200,000+ customers` + 15+ logos (Adobe, Google, Uber, Clarins, Amex, HSBC, Ticketmaster, PayPal, AAA, BT, T-Mobile, Virgin Media, Deutsche Telekom, Etsy, Lyft, TUI, Club Med, Bolt) ; (b) badges Meta Partner / MEF (A2P CoC) / 3Core2 UKAS ; (c) **stats opérateur héroïques** : `600+ direct operator connects` · `160M+ active phone numbers` · `100B+ calls/year` · `700B+ texts and emails/year` ; (d) Gartner MQ 2025 Leader + IDC MarketScape 2026 Leader. |
| **Coverage / stats** | Pas de carte mondiale. Coverage par stats numériques + 3 contacts régionaux footer (Atlanta / Stockholm / Singapore). |
| **Footer** | Très enrichi — 4 colonnes + contact régional + social + legal + sitemap. |
| **Minimalisme / Dev-first** | **7/10** · **4/10** |

**Signature** : preuve par analystes (Gartner/IDC top fold), stats opérateur **massives** (100B calls/year), positionnement **"infrastructure B2B Enterprise solide"**, pas plateforme hackable.

---

### 1.5 Bandwidth — `https://www.bandwidth.com`

| Axe | Observation |
|---|---|
| **Hero** | Stack vertical classique. Eyebrow `300+ five-star reviews`. Headline *"Voice, Messaging, and Emergency. Trusted by the best."*. Subheadline *"AI-ready enterprise integrations and APIs."*. CTA principal `Talk to sales` + `Request trial` (nav header). Visuel = **3 icônes flat** (téléphone violet / alerte orange / bulle message vert). |
| **Palette** | Violet/mauve (icônes brand) · orange (icône Emergency) · vert (icône Messaging) · blanc/gris clair fond · charbon texte. Carrier sober. |
| **Typographie** | Sans-serif moderne, géométrique. Hiérarchie h1>h2>body. Sensation institutionnelle/enterprise. |
| **Sections** | (1) Header sticky · (2) Hero · (3) **Logo strip 11 marques** (Google, Microsoft, Zoom, Genesys, 8x8, Dialpad, AWS, Uber, RingCentral, Webex, Doosan) · (4) 3 featured cards (Integrations / SIP / Messaging API) · (5) `Built on trust` carousel 4 témoignages clients · (6) Stats réseau (93% global, 65+ pays, CSAT 97.6%, uptime 99.9%) · (7) Integration grid 9 cards · (8) Awards carousel 15+ certifications · (9) CTA finale · (10) Footer riche. |
| **Section produits** | Mix : 3 featured cards + grille 9 intégrations (icône + lien). Carrier-first focus : Webex, Teams (×2), Google, Genesys, Five9, Zoom. Bandwidth produits : Voice API, Messaging, Call Verification, SIP trunking. **Très peu de "SMS basique"** — accent wholesale/enterprise/API. |
| **Code snippets** | **Pas en homepage**. Liens Documentation + Code samples en top nav. Developer-first **secondaire**. |
| **Trust signals** | **Hybride très chargé** : (a) **11 logos clients** (Google, Microsoft, Zoom...) ; (b) **4 témoignages vidéo** clients avec quotes CTO-level (Pennymac 50% savings 2M calls/mo, Attentive 2.2B texts, Wyndham 9200 properties 95 pays) ; (c) stats opérateur language carrier pur : `93% global economy covered` · `65+ PSTN countries` · `97.6% CSAT` · `99.9% uptime` ; (d) **ISO 27001:2022** (footer) ; (e) **15 awards** (G2, Stevie Gold/Silver, Internet Telephony, UC Today, Omdia, IDC, Capterra, BPTW…). |
| **Coverage / stats** | Pas de carte interactive en home (renvoi `/coverage/`). Stats : `93% global economy` · `65+ PSTN countries` · `97.6% CSAT` · `99.9% uptime`. |
| **Footer** | Très riche, 5 colonnes. **Détail intéressant** : liens directs *"Twilio Alternative"* + *"Sinch Alternative"* — positionnement concurrentiel agressif. |
| **Minimalisme / Dev-first** | **7/10** · **4/10** |

**Signature** : crédibilité opérateur télécom historique, language carrier pur (`93% global economy`, `Tier 1 footprint`), positionnement direct vs concurrents (liens *"X Alternative"*).

---

### 1.6 Bird — `https://bird.com` *(observation partielle — voir préambule)*

> ⚠️ **WebFetch limité** : seule la barre de navigation multilingue (11 langues) a été capturée par le fetch. Les sections principales sont rendues progressivement en JS côté client, non parsées. La fiche ci-dessous repose principalement sur la **connaissance training** et reste à valider visuellement avant arbitrage.

| Axe | Observation (à valider) |
|---|---|
| **Hero** | (Training) Bird (ex-MessageBird) est connu pour des illustrations vectorielles très soignées en hero — souvent un oiseau stylisé brand mascot + scène de communication multi-canal. Headline marketing-first. CTA `Talk to sales` ou `Get started`. |
| **Palette** | (Training) Palette plus colorée que les concurrents — souvent rouge/corail brand (logo Bird), avec accents vert/jaune. Plus "marketing CX" que "carrier dark". |
| **Typographie** | (Training) Sans-serif moderne, custom corporate font (probable). Gros poids sur display. |
| **Sections (training)** | Hero illustré · Logo strip clients · Sections par canal (Email, SMS, WhatsApp, Voice) · AI Agents spotlight · Témoignages · Pricing teaser · Footer. |
| **Section produits** | Multi-channel CX : Email, SMS, WhatsApp Business, Voice, AI Agents. Format cards visuelles avec illustrations custom. |
| **Code snippets** | (Training) Présents mais secondaires — Bird est CX-first, pas dev-first comme Twilio/Plivo. |
| **Trust signals** | (Training) Hybride avec logos clients enterprise. |
| **Coverage** | (Training) Stats type "X messages delivered" + langues supportées. |
| **Footer** | Très international (11 langues confirmées par WebFetch). |
| **Minimalisme / Dev-first** | (Training) **5/10** · **3/10** — le moins minimaliste et le moins dev-first des 6, plus "marketing CX". |

**Signature (training)** : illustrations vectorielles custom (signature visuelle forte), positionnement CX/marketing plutôt que carrier/dev, multi-channel as central narrative.

---

## Section 2 — Synthèse comparative

### 2.1 Tableau axes principaux

| Site | Minimalisme | Dev-first | Carrier-grade | Tone | Sections |
|---|---|---|---|---|---|
| **Twilio** | 7/10 | 8/10 | Moyen (multi-cloud) | Tech moderne 2026, AI-era | 9 |
| **Plivo** | 8/10 | 8/10 | Moyen | Numérotation signature, dev-friendly | 12 |
| **Telnyx** | 8/10 | 7/10 | **MAX (Tier 1, stack ownership)** | Ingénierie premium dark | 10 |
| **Sinch** | 7/10 | 4/10 | Élevé (600+ operators) | Enterprise B2B classique | 13 |
| **Bandwidth** | 7/10 | 4/10 | **MAX (carrier US 93% global)** | Opérateur historique sérieux | 10 |
| **Bird** | 5/10 (estim.) | 3/10 (estim.) | Faible | Marketing CX coloré | ~10 |

→ **Spectre clair** : Telnyx + Bandwidth = **carrier-grade dark/sérieux** · Twilio + Plivo = **dev-first équilibré** · Sinch = **enterprise B2B classique** · Bird = **marketing CX**.

### 2.2 🎯 Trust signals — comment chacun gère SANS / AVEC logos clients (focus PO)

| Site | Logos clients homepage ? | Approche substituée | Force premium |
|---|---|---|---|
| **Telnyx** | ❌ **Aucun** | **Pure impersonnel** : chiffre brut "14,000+ companies" + carrier credentials + compliance baked-in + stats latency/PoPs | ⭐⭐⭐⭐⭐ |
| **Twilio** | ✅ 13 logos marquee | Hybride logos + Awards (Gartner/Omdia/IDC) | ⭐⭐⭐ |
| **Plivo** | ✅ 14+ logos marquee | Hybride logos + Section certifs dédiée (HIPAA/GDPR/SOC 2/PCI DSS/STAR) | ⭐⭐⭐ |
| **Sinch** | ✅ 15+ logos | Hybride logos + Stats opérateur massives (160M+ phones, 100B+ calls/year) + Gartner/IDC | ⭐⭐⭐⭐ (logos enterprise très fort) |
| **Bandwidth** | ✅ 11 logos + 4 vidéos témoignages | Hybride logos + Stats opérateur + ISO 27001 + 15 awards | ⭐⭐⭐⭐ |
| **Bird** | ✅ (probable) | Hybride logos + CX storytelling | ⭐⭐ |

**🔑 Conclusion clé pour Dream Digital** :

L'approche `trust signals impersonnels SANS logos clients` choisie par le PO (refus juridique des placeholders Rawbank/Vodacom/etc.) **est exactement le playbook Telnyx** — qui est aussi le site le plus carrier-grade, le plus premium et le plus aligné avec un positionnement ITSP/Tier-1. **Cette décision n'est pas une contrainte par défaut, c'est un alignement positionnel fort.**

Telnyx remplace les logos par 5 mécanismes substitutifs combinés :
1. **Chiffre brut social proof** : *"14,000+ industry-leading companies"* (sans nommer)
2. **Carrier credentials** : *"owns the telephony stack"*, *"licensed carrier footprint"*
3. **Compliance baked-in** : *"10DLC/KYC enforced before execution"*
4. **Stats opérateur** : *"<500ms latency"*, *"30+ countries"*, *"40+ languages"*, *"co-located edge PoPs"*
5. **Differentiation messaging** : *"Not retrofitted for agents. Built for them."*

→ **Recommandation** : Dream Digital reproduit ces 5 mécanismes en Étape 4 (trust strip + stats strip + section coverage + section dev-first).

### 2.3 Sections home les plus communes (présence sur 5/6 sites observés)

| Section | Twilio | Plivo | Telnyx | Sinch | Bandwidth | Bird (estim.) | Verdict pour DD |
|---|:-:|:-:|:-:|:-:|:-:|:-:|---|
| Hero split + CTAs | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **Obligatoire** |
| Trust signals (logos OU stats) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | **Obligatoire** (impersonnel pour DD) |
| Section produits cards/grille | ✅ | ⚠️ texte | ⚠️ verticals | ⚠️ matricielle | ✅ | ✅ | **Obligatoire** |
| Code snippets / dev-first | ✅ | ✅ | ⚠️ configurateur | ❌ | ❌ | ⚠️ | **Recommandé** (différenciation Twilio/Plivo) |
| Stats / KPIs (latency/uptime/pays) | ⚠️ par client | ✅ | ✅ | ✅ | ✅ | ⚠️ | **Obligatoire** |
| Coverage map | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | **Optionnel** mais différenciant si bien fait |
| CTA finale | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ | **Recommandé** |
| Footer riche 5+ colonnes | ✅ | ✅ | ⚠️ | ✅ | ✅ | ✅ | **Obligatoire** |
| Témoignages explicites (cards) | ❌ marquee | ❌ | ❌ | ✅ vidéos | ✅ vidéos | ✅ | **Décision PO : SUPPRIMÉ V1, remplacé par "Cas d'usage par industrie"** |

---

## Section 3 — 3 directions visuelles proposées pour Dream Digital

Trois propositions distinctes alignées avec Brand Kit v1.2 (Petrol Teal `#335F5F` primary, Action Black `#0E121C` secondary, Tertiary Cyan `#14B8A6` spot, Inter typo). Le PO arbitre une (ou hybride) avant Étape 2.

---

### 🅰 DIRECTION A — *"Carrier-grade épuré"* (style Telnyx + Twilio)

**Description** : approche premium dark/clair contraste, look ingénierie/Tier-1. Identité forte basée sur la sobriété. Pas de fioritures graphiques, le contenu et les chiffres parlent. Le hero met en avant un terminal de code OU une carte mondiale épurée. Trust signals 100% impersonnels (pas un logo client). Section developer-first centrale avec code snippets multi-langages. Stats opérateur en évidence.

**Palette dominante** : Action Black `#0E121C` en background hero/footer/code blocks (signature dark) · Petrol Teal `#335F5F` en accents primaires (titres, brand, focus rings) · Tertiary Cyan `#14B8A6` en spots éditoriaux uniquement (badges live, fonctions code, KPIs animés) · blanc pur et warm grays pour les sections claires.

**Mood** : sobriété carrier-grade, ingénierie, confiance institutionnelle. *"Vous achetez de l'infrastructure, pas du marketing."*

**Points forts** :
- Crédibilité opérateur télécom maximale (alignement Tier-1)
- Cohérence forte avec décisions PO (trust signals impersonnels = playbook Telnyx)
- Code preview central → différenciation vs Sinch/Bandwidth qui sont marketing-first
- Brand Kit v1.2 (Petrol Teal + Action Black) parfaitement adapté à cette palette dark/sober

**Risques** :
- Peut paraître froid ou austère pour visiteurs non-techniques (banque retail décideur)
- Demande un copywriting très précis (pas de remplissage marketing fluffy)
- Sections dark exigent attention sur contraste WCAG AA (déjà couvert par Brand Kit)

**Hero exemple** :
> Section dark fullwidth `#0E121C`. Eyebrow petite caps Tertiary Cyan `Voice. SMS. eSIM. And More.`. Headline 800 weight Inter blanc *"L'infrastructure télécom qui connecte les entreprises modernes à 200+ pays."* (display-1 clamp). Sous-titre gris clair sur 2 lignes. CTAs : `Échanger avec notre équipe` (Petrol Teal solid) + `Voir la doc API (bientôt)` (ghost outline cyan). À droite : terminal animé `curl POST` qui type au scroll. Bullets bottom : `✓ Couverture 200+ pays   ✓ Bureaux RDC · CI · CG`.

---

### 🅱 DIRECTION B — *"CPaaS accessible"* (style Plivo + Sinch hybride)

**Description** : approche sobre clair dominant, ton enterprise ouvert et didactique. Le hero est un split avec illustration claire ou screenshot dashboard preview (pas de dark). Sections segmentées comme Plivo avec numérotation `01`...`07` (signature visuelle forte adoptable). Trust signals impersonnels mais avec stats massives en chiffres animés (counters). Code snippets présents mais en variant clair (terminal blanc/gris pas dark). Tone plus pédagogique.

**Palette dominante** : blanc pur + warm grays foundation (~60%) · Petrol Teal `#335F5F` primary partout (titres, CTAs, brand, sidebar admin) · Action Black `#0E121C` réservé aux hero CTAs critiques uniquement · Tertiary Cyan `#14B8A6` pour live indicators / badges NEW / focus rings (~5%).

**Mood** : accessibilité, clarté, didactique, ouverture. *"Pas besoin d'être ingénieur télécom pour démarrer chez nous."*

**Points forts** :
- Plus accessible / accueillant pour décideurs non-techniques (banques, retail)
- Lecture facile du contenu, hiérarchie claire
- Numérotation `01`-`07` (à la Plivo) = signature graphique mémorable
- Plus rapide à produire que Direction A (moins de polish dark mode requis)

**Risques** :
- Moins premium / carrier que Direction A
- Risque de paraître "encore un autre site CPaaS clean B2B" sans différenciation
- Plus difficile de soutenir le positionnement carrier-grade Tier-1

**Hero exemple** :
> Section claire fullwidth blanc/warm gray. Eyebrow Petrol Teal 500 weight `01 Plateforme CPaaS`. Headline 700 weight Action Black *"L'infrastructure télécom qui connecte les entreprises modernes à 200+ pays."*. Sous-titre charcoal détaillant les 6 services. CTAs : `Échanger avec notre équipe` (Petrol Teal solid arrondi) + `Voir la doc API (bientôt)` (ghost). À droite : screenshot dashboard preview stylisé (KPIs SMS, latency, DLR). Bullets bottom mêmes que Direction A.

---

### 🅲 DIRECTION C — *"Premium éditorial"* (style Stripe + touch Bird)

**Description** : approche design éditorial soigné, qui assume l'effort artistique. Illustrations vectorielles custom (Africa-orientée mais sans drapeaux folkloriques — formes géométriques abstraites évoquant une carte ou des connexions). Sections généreusement espacées. Typographie display weight 800 spectaculaire. Animations subtiles partout. Code preview ET illustrations en parallèle. Trust signals impersonnels avec compositions graphiques élaborées (pas juste du texte sur fond).

**Palette dominante** : blanc + warm grays + Petrol Teal `#335F5F` (~25%) · gradients **subtils** Petrol Teal vers Tertiary Cyan dans certaines sections signature · Action Black pour zones contrastées (hero CTA banner finale par exemple) · gros usage de **whitespace généreux** entre sections (~15-20% de la palette = vide structurel, comme Stripe).

**Mood** : design éditorial premium, mémorable, différenciation visuelle forte. *"On a soigné chaque pixel comme un magazine de haute couture."*

**Points forts** :
- Différenciation maximale vs concurrents (Stripe-tier visual quality)
- Mémorable, partageable, raconte une histoire
- Positionne Dream Digital comme acteur design-conscious (rare dans secteur ITSP/wholesale)

**Risques** :
- **Effort de production très élevé** — illustrations custom = coût ressources design important
- Brief Sprint 1.5 prévoit 4-6 jours, Direction C demande probablement +2-3 jours supplémentaires
- Risque "trop joli pour être un opérateur télécom sérieux" — pourrait diluer le positionnement carrier-grade
- Demande un illustrateur dédié OU d'utiliser une bibliothèque type Storyset/Undraw (qualité moindre)

**Hero exemple** :
> Section claire avec illustration vectorielle custom dominante. Eyebrow Petrol Teal small caps. Headline 800 weight Inter, **très grand** (display-1 jusqu'à 80px), légère animation fade-up au load. Sous-titre généreux line-height 1.55. CTAs Petrol Teal + ghost outline. À droite : illustration vectorielle custom — par exemple lignes courbes Petrol Teal connectant des points cyan répartis sur un globe abstrait stylisé (pas une carte réaliste, juste une métaphore visuelle de la couverture monde). Animations subtiles : les lignes se tracent au load, les points pulsent légèrement.

---

## Section 4 — Recommandation Claude Code

Mon avis honnête, à arbitrer par le PO.

### Direction recommandée : 🅰 **"Carrier-grade épuré"**

**Arguments forts** :

1. **Alignement positionnement carrier-grade Tier-1** : le brief Section 2.1 et l'amendement A demandent explicitement *"Confiance carrier-grade : on est un vrai opérateur, pas une app qui fait du SMS"* + *"sobriété carrier-grade"*. Direction A délivre ça nativement. Direction B serait OK mais moins fort. Direction C risque la dilution.

2. **Cohérence Brand Kit v1.2** : Action Black `#0E121C` (secondary, ~15%) trouve son emploi naturel comme background dark dans Direction A — alors qu'il serait sous-utilisé en Direction B/C où la foundation reste claire. Petrol Teal en contraste sur dark = visuellement très fort.

3. **Cohérence avec décisions PO arbitrage 2026-05-08** : trust signals impersonnels = playbook Telnyx (Direction A). Pas de logos clients, pas de témoignages — Direction A est *bâtie sur ces contraintes*, pas malgré elles.

4. **Différenciation marché** : Telnyx + Twilio (Direction A) sont les sites les plus mémorables des 5 observés. Sinch / Bandwidth (Direction B) sont plus "interchangeables". Direction C demande un effort design hors budget temps Sprint 1.5.

5. **Faisabilité 4-6 jours** : Direction A = polish sober + code preview animé + carte stats = tenable. Direction C = +2-3 jours minimum pour illustrations custom.

**Hybridation possible** : si le PO trouve Direction A trop austère pour la cible africaine francophone (où la convivialité compte), un compromis viable serait **A à 80% + emprunts B à 20%** :
- Garder le hero dark de Direction A
- Mais alléger les sections produits + cas d'usage en clair façon Direction B (ne pas tout faire en dark)
- Garder la numérotation `01`-`07` de Plivo comme accent de section (signature visuelle légère)

**Ce que je NE recommande pas** :
- ❌ **Direction C pure** : effort de production hors budget, risque de dilution carrier-grade
- ❌ **Tout-en-Direction-B** : trop "interchangeable" pour un opérateur qui vise la crédibilité Tier-1

**Mais c'est le PO qui tranche.** Ce sprint est subjectif, et la cible ultime (clients banque/retail africains francophones + 60%+ clients hors Afrique selon amendement A) peut justifier un mix différent que ma recommandation pure.

---

## Décision attendue PO

Pour débloquer Étape 2 (système de design), j'ai besoin que tu choisisses :

- **A pure** → Direction Carrier-grade épuré, dark hero + Telnyx playbook
- **B pure** → CPaaS accessible, clair dominant, numérotation Plivo
- **C pure** → Premium éditorial, illustrations custom (attention : ETA +2-3j)
- **A + B 80/20** *(mon hybride suggéré)* → dark hero + sections claires + numérotation
- **Autre hybride** → précise (ex: B pure + code preview de A en spot, etc.)

Une fois validé, je passe à Étape 2 : implémenter les tokens Brand Kit v1.2 dans `_typography.scss`, `_spacing.scss`, `_animations.scss` + page demo `/preview/components/design-tokens` pour validation visuelle.

---

*Document créé en Phase 4 du Bloc 2 du Sprint 1.5. À mettre à jour si la direction validée évolue, ou si screenshots Playwright sont ajoutés en Phase complémentaire.*
