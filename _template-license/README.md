# ThemeSelection commercial license — local storage

This folder is the local-only storage location for the commercial license proof of purchase of the **Sneat Dashboard PRO Laravel** template, acquired from ThemeSelection.

## What goes here

- The PDF invoice or receipt issued by ThemeSelection
- Any license key file or activation email
- The original `LICENSE` / `LICENSE.md` shipped inside the bought template archive

## What does NOT go here

- Anything related to Dream Digital's own intellectual property
- Production secrets (those go in environment variables, see [SECURITY.md](../SECURITY.md))

## Why a `.gitignore` inside the folder

The `.gitignore` in this directory ignores **all files** dropped here except itself and this README. That keeps the folder visible in the repository (so future maintainers know where the proof of purchase lives), while preventing the actual license documents from being committed and leaked.

## Where to find the original license

If the local folder is empty, the proof of purchase can be retrieved from :

- The ThemeSelection account dashboard : https://themeselection.com/account/
- The original purchase confirmation email from `support@themeselection.com`

## Compliance note

Dream Digital owns a **regular commercial license** for Sneat Dashboard PRO Laravel. The desanonymization work performed in `BRIEF_DD_DESANONYMIZATION.md` is a standard customization step on top of that license, not a license circumvention — it removes vendor branding so the template can be safely deployed under the Dream Digital brand, which the commercial license expressly permits.
