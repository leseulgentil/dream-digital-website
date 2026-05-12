# Mode de travail — Finition autonome par blocs

Date d'activation : 2026-05-12
Projet : Dream Digital website
Branche cible : `feature/sprint-1-5-redesign`

## Décision

Le projet passe en mode **finition autonome par blocs**.

Ce mode remplace, pour la phase de finition, le workflow initial qui demandait une validation PO fréquente après presque chaque sous-etape. L'objectif est d'aller plus vite tout en gardant des garde-fous clairs.

Codex, Claude Code ou tout autre partenaire dev travaillant sur ce repo doit suivre ce document avant de reprendre le Sprint 1.5.

## Principe

L'agent dev travaille en autonomie sur des blocs cohérents de 2 a 4 heures, puis livre un rapport court :

- ce qui a ete change ;
- les fichiers principaux touches ;
- les commandes de verification lancees ;
- les risques ou arbitrages restants ;
- ce que le PO doit valider visuellement, si necessaire.

Le PO ne doit etre sollicite que pour les validations produit/design importantes, pas pour les corrections techniques courantes.

## Validations PO obligatoires

Demander une validation explicite seulement pour :

- direction visuelle majeure de la home ;
- hero final desktop/mobile ;
- navigation et footer publics ;
- contenus business sensibles : pricing, claims SLA, couverture pays, references clients ;
- passage du site de `noindex` vers indexable ;
- deploiement production public ;
- suppression irreversible de gros pans du template ou d'assets.

## Autonomie autorisee

L'agent peut avancer sans validation PO sur :

- correction de bugs ;
- refactor Blade/SCSS/JS sans changement produit majeur ;
- nettoyage des routes de demo, si elles restent protegees ou deplacees ;
- correction de `route:cache` ;
- securisation de helpers et cookies ;
- extraction de composants Blade ;
- deplacement du CSS inline vers SCSS ;
- optimisation Vite/build ;
- ajout ou ajustement de tests ;
- documentation technique courte ;
- corrections d'accessibilite et responsive.

## Garde-fous

- Ne jamais supprimer du contenu business sans remplacement ou justification.
- Ne jamais exposer publiquement les pages de demo Sneat.
- Ne jamais retirer `noindex` sans validation PO.
- Ne jamais modifier `master` comme filet de securite historique.
- Ne jamais committer `.env`, bases locales ou secrets.
- Respecter le Brand Kit Dream Digital v1.2.
- Respecter le brief initial Sprint 1.5 comme direction produit, mais appliquer le present document pour la methode de travail.
- En cas de conflit entre anciens documents demandant des validations frequentes et ce document, ce document prime pour la phase de finition.

## Plan de finition recommande

### Bloc 1 — Stabilisation technique

Objectif : rendre le projet sain pour une production Laravel.

- Corriger les noms de routes dupliques qui bloquent `php artisan route:cache`.
- Proteger, deplacer ou retirer les routes de demo exposees.
- Corriger `generatePrimaryColorCSS()` avec validation stricte hex ou retrait de la feature.
- Verifier `php artisan test`, `php artisan route:cache`, `npm.cmd run build`.
- Documenter les problemes d'environnement local separes du code projet.

### Bloc 2 — Refactor landing

Objectif : rendre la home maintenable.

- Extraire le hero de `landing-page.blade.php` en composants Blade.
- Deplacer le CSS inline vers SCSS dedie.
- Conserver le rendu actuel autant que possible.
- Garder le slider, terminal, dashboard preview et offices.

### Bloc 3 — Sections home publiques

Objectif : finir la vitrine CPaaS selon le brief Sprint 1.5.

- Ajouter ou finaliser trust strip, stats, services, developer-first, coverage, pricing teaser, testimonials, CTA final et footer riche.
- Utiliser les configs `config/dream-digital/*` quand le contenu existe.
- Mettre des placeholders propres seulement quand les assets PO manquent.

### Bloc 4 — Production readiness

Objectif : préparer la mise en ligne publique.

- Nettoyer SEO/meta titles/descriptions.
- Garder `noindex` tant que le PO n'a pas valide l'ouverture publique.
- Verifier responsive mobile/tablet/desktop.
- Reduire le build public si possible.
- Faire une passe accessibilite : contrastes, focus, reduced motion, aria labels.

### Bloc 5 — Validation finale et deploiement

Objectif : livrer proprement.

- Run final : tests Laravel, route cache, config cache, build Vite.
- Checklist prod : `.env`, APP_DEBUG, logs, Basic Auth, robots, backups, SSL.
- Rapport final court pour validation PO.

## Format de rapport par bloc

Chaque bloc doit se terminer par :

```text
Bloc termine : <nom>
Fichiers principaux : <liste courte>
Verifications : <commandes + resultat>
Risques restants : <liste courte>
Validation PO requise : oui/non + sujet exact
Prochain bloc recommande : <nom>
```
