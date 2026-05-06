# SECURITY — Dream Digital website

## Secrets management

The `.env` file contains application secrets (`APP_KEY`, database credentials, mail credentials, third-party API keys) and **MUST NEVER be committed**.

### What's enforced
- `.env` and `.env.*` are in `.gitignore` (only `.env.example` is allowed to be committed)
- Local SQLite database files (`/dream_digital`, `/database/*.sqlite`) are also gitignored
- A historical incident (2026-05-05) where `.env` had been committed in `7b5803f` was resolved by rewriting the history of `feature/desanonymization` with `git filter-repo`. The `APP_KEY` was rotated immediately after.

### Onboarding checklist for a new developer
1. Clone the repository
2. Copy `.env.example` to `.env` : `cp .env.example .env`
3. Generate a fresh app key locally : `php artisan key:generate`
4. Fill in environment-specific secrets (DB password, mail credentials, API keys) — **do NOT paste production secrets** ; ask the technical lead for credentials scoped to your environment
5. Run `php artisan migrate --seed` to populate the local SQLite DB
6. Verify before any commit : `git status` must NOT list `.env` ; if it does, stop and remove it from the index (`git rm --cached .env`)

### What to do if a secret leaks
1. **Rotate immediately** the leaked secret (`APP_KEY` → `php artisan key:generate` ; DB password → change at the DB ; API keys → revoke and reissue at the provider)
2. Purge from history with `git filter-repo --path <leaked-file> --invert-paths --refs <branch> --force`
3. Force-push to all remotes (after coordinating with the team — history rewrite breaks every clone)
4. Document the incident in this file with date and resolution

### Reference
- `git filter-repo` is the recommended tool (modern replacement for the deprecated `git filter-branch`). Install : `python -m pip install --user git-filter-repo`. Invocation : `python -m git_filter_repo ...`
