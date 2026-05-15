# Roadmap 5 sprints - Dream Digital

Date: 2026-05-15

## Objectif global

Amener Dream Digital d'un site corporate pre-lancement vers une plateforme publique exploitable: vitrine indexable, donnees entreprise pilotables en admin, CMS commercial, portail client V1, back-office metier telecom et socle production robuste.

## Sprint 2 - Preprod et ouverture publique controlee

Objectif: rendre le site ouvrable publiquement sans dette critique.

Livrables:

- Profil entreprise complet dans `/admin/company-profile`: 3 entites pays (`CD`, `CI`, `CG`) avec onglets et donnees FR/EN.
- Champs pilotables par entite: raison sociale, nom public, telephone public, WhatsApp, emails sales/support/security/privacy, adresse, ville/pays, coordonnees GPS, identifiants legaux, horaires, reseaux sociaux, image OpenGraph, confirmations legal/admin.
- `config('dream-digital.site')`, `security.txt`, SEO et `dd:launch-check` alimentes par le profil admin avec fallback `.env`.
- Preprod reelle avec migrations, seeders, cache config/routes/views, Nginx, Supervisor si queue database.
- `dd:launch-check --public` vert avant `DD_PUBLIC_INDEXABLE=true`.
- QA visuelle finale sur home, produits, pricing, coverage, blog, legal et admin.

Critere de sortie:

- Tests et builds passent.
- Telephone public, WhatsApp et GPS sont remplissables depuis l'admin pour chaque pays en FR/EN.
- `DD_PUBLIC_INDEXABLE=true` devient une decision PO/ops, pas un blocage technique.

## Sprint 3 - CMS commercial et contenu SEO

Objectif: permettre a Dream Digital de gerer son contenu public sans intervention dev.

Livrables:

- Formulaires CMS plus structures par type de page: marketing, produit, blog, legal, help.
- Repeater visuel pour sections au lieu du textarea JSON seul.
- Media library plus propre: preview, alt text, credit, suppression controlee, usage par page.
- Workflow editorial: draft, preview, publish, updated_by, historique revisions lisible.
- Enrichissement SEO: schema FAQ, article, product/service, meta image par page.
- Contenu de lancement consolide: pages produits, corridors, guides blog, FAQ.
- Formulaire contact public avec stockage des leads dans l'admin.

Critere de sortie:

- Un editor peut creer/modifier/publier une page ou un article complet sans toucher au code.

## Sprint 4 - Portail client V1

Objectif: donner aux clients un espace de base utilisable.

Livrables:

- Espace client separe de l'admin interne.
- Profil client: societe, contacts, pays, preferences, statut verification.
- Dashboard client: services actifs, solde/credit indicatif, derniers evenements.
- Demande d'acces service: SMS, Voice, eSIM, DID, SIP, Dialo.
- Tickets ou demandes commerciales simples.
- Auth client: reset password, verification email, garde-fous sessions.

Critere de sortie:

- Un client peut se connecter, consulter son compte et envoyer une demande actionnable.

## Sprint 5 - Back-office metier telecom

Objectif: transformer l'admin en outil d'exploitation, pas seulement en CMS.

Livrables:

- Gestion avancee des services, pays, corridors et tarifs.
- Import/export CSV des prix.
- Statuts route: actif, test, degraded, maintenance, retire.
- Audit log admin sur operations sensibles.
- RBAC plus fin par module et action.
- Leads entrants depuis formulaires publics avec qualification.
- Notifications internes pour demandes client.

Critere de sortie:

- L'equipe peut administrer les offres, tarifs et demandes sans passer par la base de donnees.

## Sprint 6 - Production hardening et croissance

Objectif: preparer la plateforme a recevoir trafic, clients et operations recurrentes.

Livrables:

- Backups automatiques VPS + base + storage avec rotation et test de restauration.
- Monitoring applicatif: logs, erreurs, requetes lentes, uptime, alertes.
- Performance Lighthouse production > 85 sur pages publiques prioritaires.
- Durcissement securite: CSP enforcement progressive, headers, audit dependencies, protection admin.
- CI/CD stabilise: tests, build, audit prod, dry-run deploy, rollback documente.
- Documentation runbook: incident, backup restore, deployment, rotation secrets.

Critere de sortie:

- Le site est operable sur la duree, avec procedure claire en cas d'incident.

## Ordre d'attaque immediat

1. Finir le profil entreprise multi-pays bilingue complet.
2. Faire passer `dd:launch-check` local en pre-launch hors confirmations ops externes.
3. Lancer une preprod propre.
4. Faire la QA visuelle finale.
5. Decider l'ouverture publique.
