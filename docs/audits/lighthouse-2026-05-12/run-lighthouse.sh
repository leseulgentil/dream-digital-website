#!/usr/bin/env bash
set -u
cd "$(dirname "$0")/../../.."
OUTDIR=./docs/audits/lighthouse-2026-05-12
SUMMARY=$OUTDIR/_summary.csv
echo "page,perf,a11y,bp,seo" > "$SUMMARY"

run_one() {
  local path="$1" slug="$2"
  echo "[$(date +%H:%M:%S)] Auditing /$path -> $slug-mobile.json"
  node_modules/.bin/lighthouse "http://127.0.0.1:8888/$path" \
    --quiet \
    --output=json --output-path="$OUTDIR/$slug-mobile.json" \
    --chrome-flags="--headless=new --disable-gpu" \
    --only-categories=performance,accessibility,best-practices,seo \
    --form-factor=mobile --screenEmulation.mobile=true \
    --throttling-method=simulate \
    --max-wait-for-load=30000 >/dev/null 2>&1
  if [ -f "$OUTDIR/$slug-mobile.json" ]; then
    node -e "const r=require('$OUTDIR/$slug-mobile.json'); const c=r.categories; console.log('$path,'+Math.round(c.performance.score*100)+','+Math.round(c.accessibility.score*100)+','+Math.round(c['best-practices'].score*100)+','+Math.round(c.seo.score*100));" >> "$SUMMARY"
    echo "[$(date +%H:%M:%S)] DONE /$path"
  else
    echo "[$(date +%H:%M:%S)] FAIL /$path"
    echo "$path,FAIL,FAIL,FAIL,FAIL" >> "$SUMMARY"
  fi
}

# /fr already done in smoke test - copy its scores
if [ -f "$OUTDIR/fr-mobile.json" ]; then
  node -e "const r=require('$OUTDIR/fr-mobile.json'); const c=r.categories; console.log('fr,'+Math.round(c.performance.score*100)+','+Math.round(c.accessibility.score*100)+','+Math.round(c['best-practices'].score*100)+','+Math.round(c.seo.score*100));" >> "$SUMMARY"
fi

run_one "fr/products" "fr-products"
run_one "fr/products/sms-a2p" "fr-products-sms-a2p"
run_one "fr/developers" "fr-developers"
run_one "fr/solutions" "fr-solutions"
run_one "fr/coverage" "fr-coverage"
run_one "fr/pricing" "fr-pricing"
run_one "fr/company" "fr-company"
run_one "fr/contact" "fr-contact"

echo "[$(date +%H:%M:%S)] ALL DONE"
cat "$SUMMARY"
