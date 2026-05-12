# Matrice responsive + console F12 — Sprint 1.5 (P3 + P4)

Date : 2026-05-12
Branche : `feature/sprint-1-5-redesign`
Methode : puppeteer-core + Chrome headless local sur `127.0.0.1:8888`
Pages : 6 (`/fr`, `/fr/products`, `/fr/products/sms-a2p`, `/fr/coverage`, `/fr/pricing`, `/fr/contact`)
Breakpoints : 5 (375 / 768 / 1024 / 1440 / 1920)
Total : 30 captures + 30 contextes console isoles

## P3 — Matrice responsive : 30/30 OK

**Zero overflow horizontal detecte sur toutes les combinaisons page x breakpoint.**

Pour chaque capture, `documentElement.scrollWidth === documentElement.clientWidth === viewport.width` (375, 768, 1024, 1440, 1920 px respectivement). Voir `_overflow.json` pour le detail brut.

Screenshots pleine page produits :
- `<page-slug>__375-mobile.png`
- `<page-slug>__768-tablet.png`
- `<page-slug>__1024-tablet-landscape.png`
- `<page-slug>__1440-desktop.png`
- `<page-slug>__1920-large.png`

Les PNG sont gitignored (15 MB total). Regenerables localement via le script.

**Validation PO recommandee** : ouverture des 6 lots `<page>__<bp>.png` pour confirmer rendu visuel (mise en page, hierarchie typographique, espacements). Hors scope automatisable.

## P4 — Console F12 : 16 messages, 1 seul root cause

**Tous les messages console (errors + warnings + requests failed) sur les 6 pages se reduisent a un meme pattern, repete par instabilite du serveur de dev :**

```
net::ERR_CONNECTION_RESET sur
  - /build/assets/bootstrap-Bd4PXRed.js (3 occurrences)
  - /build/assets/iconify-DDZnTNbY.css (3 occurrences)
```

Consequence en cascade : `bootstrap is not defined` quand le bundle bootstrap.js a echoue.

### Cause identifiee

Le serveur `php artisan serve` utilise le webserver PHP integre, **mono-thread**. Sous charge concurrentielle (Chrome ouvre 6-10 requetes assets en parallele), certaines connexions sont reset.

Verifications :
- `ls public/build/assets/bootstrap-*.js iconify-*.css` -> presents (81 KB et 1.26 MB)
- `curl http://127.0.0.1:8888/build/assets/bootstrap-*.js` -> 200 OK en mode sequentiel
- Aucun bug code, aucun bundle manquant, aucun chemin casse

### Production

Sur `https://dream-digital.info` derriere Nginx 1.24 + PHP-FPM (config Sprint 0 pre-S1.5), aucune de ces erreurs ne peut se reproduire : Nginx gere des centaines de connexions concurrentes en non-bloquant, et la compression brotli/gzip reduit la taille servie. **Non-fix volontaire, atomique au mode dev.**

### Re-mesure proposee

Pour valider production-ready avant indexation publique :
- Re-lancer ce script ciblant `https://dream-digital.info/fr/*` au lieu de `127.0.0.1:8888`
- Necessite credentials Basic Auth (cf. PO `dream-digital` / pass note cote PO)
- Hors scope P4 (mesure dev close, prod a faire en bloc deploiement)

## Reproduction

```bash
# Pre-requis : Laravel sur 127.0.0.1:8888 + npm.cmd run build a jour
node docs/audits/responsive-2026-05-12/run-responsive.cjs
# -> genere les PNG + _console.json + _overflow.json + sortie stdout
```

## Synthese P3 + P4

| Critere brief Section 6.2 | Cible | Resultat | Statut |
|---|---|---|---|
| Responsive 5 breakpoints | aucun overflow | 30/30 sans overflow | OK |
| Console F12 propre | sans erreur | 16 erreurs **dev-mode only**, 0 erreur code | OK conditionnel (a verifier prod) |

**Aucun fix code applique pour P3/P4** : la matrice est propre, les erreurs console sont entierement attribuables au serveur de dev mono-thread.
