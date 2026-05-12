// dd-cookie-consent.js
// Banner consentement cookies V1 -- acknowledgment unique car le site
// n'utilise que des cookies strictement necessaires (Art. 82 LIL :
// consentement explicite non requis, mais transparence + dismiss
// preserves UX RGPD-friendly).
//
// Stockage : localStorage cle 'dd-cookie-acknowledged' = '1'
// Premiere visite -> banner visible. Apres clic 'Compris' -> hidden
// pour les visites suivantes. Reset possible en supprimant la cle.

'use strict';

(function () {
  const STORAGE_KEY = 'dd-cookie-acknowledged';
  const banner = document.getElementById('ddCookieBanner');
  const btn = document.getElementById('ddCookieAck');

  if (!banner || !btn) return;

  let acknowledged = false;
  try {
    acknowledged = window.localStorage.getItem(STORAGE_KEY) === '1';
  } catch (e) {
    // localStorage indisponible (private mode, cookies bloques) -- on
    // affiche tout de meme le banner, le clic ferme via classlist sans
    // persistence.
  }

  if (acknowledged) {
    banner.hidden = true;
    return;
  }

  banner.hidden = false;

  btn.addEventListener('click', function () {
    try {
      window.localStorage.setItem(STORAGE_KEY, '1');
    } catch (e) {
      // silencieux
    }
    banner.hidden = true;
  });
})();
