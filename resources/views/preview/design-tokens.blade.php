@extends('layouts/blankLayout')

@section('title', 'Design Tokens Preview — Dream Digital')

@section('page-style')
<style>
  /* ============================================================
     Design Tokens Preview — page-specific styles only
     Sprint 1.5 Étape 2 Phase 4 — internal dev page
     ============================================================ */
  .dtp-page {
    background: #FAFAFA;
    color: #0E121C;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    min-height: 100vh;
    padding: 0;
    margin: 0;
  }
  .dtp-container { max-width: 1200px; margin: 0 auto; padding: 64px 24px; }

  /* Header */
  .dtp-header { padding: 24px 0 56px; border-bottom: 1px solid #E4E7EE; margin-bottom: 64px; }
  .dtp-header h1 {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 800;
    letter-spacing: -0.02em;
    margin: 0 0 8px;
    color: #0E121C;
  }
  .dtp-header .subtitle {
    font-size: 1.125rem;
    color: #5A6275;
    margin: 0 0 4px;
  }
  .dtp-header .meta {
    font-size: 0.875rem;
    color: #7B8395;
    font-family: 'JetBrains Mono', monospace;
  }
  .dtp-header .badge-dir-a {
    display: inline-block;
    background: #14B8A6;
    color: white;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-left: 12px;
    vertical-align: middle;
  }

  /* Sections */
  .dtp-section { margin-bottom: 96px; }
  .dtp-section h2 {
    font-size: clamp(1.5rem, 2.5vw, 2rem);
    font-weight: 700;
    letter-spacing: -0.015em;
    margin: 0 0 8px;
    color: #0E121C;
  }
  .dtp-section .section-intro {
    font-size: 1rem;
    color: #5A6275;
    margin: 0 0 32px;
    max-width: 720px;
  }

  /* === Section 1: Colors === */
  .dtp-color-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
  }
  .dtp-color-grid.brand-shades { grid-template-columns: repeat(10, minmax(0, 1fr)); gap: 0; margin-bottom: 32px; }
  .dtp-color-card {
    background: white;
    border: 1px solid #E4E7EE;
    border-radius: 8px;
    overflow: hidden;
  }
  .dtp-color-swatch {
    width: 100%;
    height: 80px;
    display: block;
  }
  .dtp-color-shade-swatch {
    height: 64px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    padding: 6px 4px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.7rem;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.4);
  }
  .dtp-color-shade-swatch.lightish { color: #0E121C; text-shadow: none; }
  .dtp-color-meta { padding: 12px; font-size: 0.8rem; }
  .dtp-color-meta .token { font-family: 'JetBrains Mono', monospace; color: #424A5C; font-weight: 500; }
  .dtp-color-meta .hex { font-family: 'JetBrains Mono', monospace; color: #14B8A6; font-weight: 600; display: block; margin-top: 2px; }
  .dtp-color-meta .usage { color: #7B8395; margin-top: 4px; font-size: 0.75rem; }

  .dtp-shade-row { margin-bottom: 24px; }
  .dtp-shade-row .row-label {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.85rem;
    color: #424A5C;
    margin-bottom: 6px;
    font-weight: 600;
  }
  @media (max-width: 768px) { .dtp-color-grid.brand-shades { grid-template-columns: repeat(5, minmax(0, 1fr)); } }

  /* === Section 2: Typography === */
  .dtp-type-row {
    padding: 20px 0;
    border-bottom: 1px solid #E4E7EE;
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 32px;
    align-items: center;
  }
  .dtp-type-row:last-child { border-bottom: none; }
  .dtp-type-meta {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.78rem;
    color: #5A6275;
  }
  .dtp-type-meta .token { color: #424A5C; font-weight: 600; display: block; }
  .dtp-type-meta .clamp { color: #14B8A6; display: block; margin-top: 2px; }
  .dtp-type-meta .weight { color: #7B8395; display: block; margin-top: 2px; }
  .dtp-type-sample.display-1 { font-size: clamp(2.5rem, 6vw, 5rem); font-weight: 800; letter-spacing: -0.025em; line-height: 1.1; }
  .dtp-type-sample.display-2 { font-size: clamp(2rem, 4.5vw, 3.5rem); font-weight: 700; letter-spacing: -0.02em; line-height: 1.15; }
  .dtp-type-sample.h1 { font-size: clamp(1.75rem, 3.5vw, 2.5rem); font-weight: 700; letter-spacing: -0.015em; line-height: 1.2; }
  .dtp-type-sample.h2 { font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 700; line-height: 1.25; }
  .dtp-type-sample.h3 { font-size: 1.25rem; font-weight: 600; line-height: 1.3; }
  .dtp-type-sample.body-lg { font-size: 1.125rem; font-weight: 400; line-height: 1.6; }
  .dtp-type-sample.body { font-size: 1rem; font-weight: 400; line-height: 1.55; }
  .dtp-type-sample.body-sm { font-size: 0.875rem; font-weight: 400; line-height: 1.5; }
  .dtp-type-sample.caption { font-size: 0.75rem; font-weight: 400; line-height: 1.4; color: #7B8395; }
  .dtp-type-sample.mono-inline { font-family: 'JetBrains Mono', monospace; background: #EFF1F5; padding: 2px 8px; border-radius: 4px; font-size: 0.875rem; }
  .dtp-type-sample.mono-block { font-family: 'JetBrains Mono', monospace; background: #0F1428; color: #E1E4F0; padding: 16px; border-radius: 8px; font-size: 0.9rem; line-height: 1.6; }

  @media (max-width: 768px) { .dtp-type-row { grid-template-columns: 1fr; } }

  /* === Section 3: Spacing === */
  .dtp-spacing-grid { display: grid; gap: 24px; }
  .dtp-spacing-row { display: flex; align-items: center; gap: 24px; padding: 16px; background: white; border: 1px solid #E4E7EE; border-radius: 8px; }
  .dtp-spacing-row .label { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #424A5C; min-width: 160px; }
  .dtp-spacing-row .value { font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: #14B8A6; min-width: 220px; }
  .dtp-spacing-bar { flex: 1; background: linear-gradient(90deg, #14B8A6 0%, #14B8A6 var(--w), #E4E7EE var(--w), #E4E7EE 100%); height: 6px; border-radius: 3px; }

  .dtp-radius-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 16px; }
  .dtp-radius-card { background: white; border: 1px solid #E4E7EE; padding: 16px; }
  .dtp-radius-shape { background: #335F5F; height: 80px; margin-bottom: 12px; }
  .dtp-radius-card .meta { font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: #424A5C; }
  .dtp-radius-card .meta .px { color: #14B8A6; }

  /* === Section 4: Shadows === */
  .dtp-shadow-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 32px; padding: 24px 16px; }
  .dtp-shadow-box { background: white; height: 120px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 16px; }
  .dtp-shadow-box .name { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #424A5C; font-weight: 600; }
  .dtp-shadow-box .desc { font-size: 0.75rem; color: #7B8395; margin-top: 4px; text-align: center; }
  .shadow-sm { box-shadow: 0 1px 2px rgba(14, 18, 28, 0.04), 0 1px 3px rgba(14, 18, 28, 0.06); }
  .shadow-md { box-shadow: 0 4px 12px rgba(14, 18, 28, 0.06), 0 2px 4px rgba(14, 18, 28, 0.04); }
  .shadow-lg { box-shadow: 0 12px 32px rgba(14, 18, 28, 0.08), 0 4px 8px rgba(14, 18, 28, 0.04); }
  .shadow-xl { box-shadow: 0 24px 48px rgba(14, 18, 28, 0.10), 0 8px 16px rgba(14, 18, 28, 0.06); }
  .shadow-glow { box-shadow: 0 0 0 1px rgba(20, 184, 166, 0.4), 0 0 24px rgba(20, 184, 166, 0.25); }
  .shadow-focus { box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.25); }

  /* === Section 5: Animations === */
  .dtp-anim-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }
  .dtp-anim-card { background: white; border: 1px solid #E4E7EE; border-radius: 12px; padding: 24px; }
  .dtp-anim-card .anim-name { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #424A5C; font-weight: 600; margin-bottom: 8px; }
  .dtp-anim-card .anim-desc { font-size: 0.85rem; color: #7B8395; margin-bottom: 16px; line-height: 1.5; }
  .dtp-anim-card .anim-stage { height: 80px; display: flex; align-items: center; justify-content: center; background: #F7F8FA; border-radius: 8px; position: relative; overflow: hidden; }

  /* dd-pulse demo: signal indicator */
  .dtp-pulse-wrap { position: relative; width: 16px; height: 16px; }
  .dtp-pulse-dot { width: 16px; height: 16px; border-radius: 50%; background: #0EBE82; position: absolute; top: 0; left: 0; }
  .dtp-pulse-ring { width: 16px; height: 16px; border-radius: 50%; background: #0EBE82; position: absolute; top: 0; left: 0; animation: dd-pulse 2s ease-out infinite; }
  @keyframes dd-pulse { 0% { transform: scale(1); opacity: 0.6; } 100% { transform: scale(2.5); opacity: 0; } }

  /* dd-fade-up demo: card on click */
  .dtp-fade-up-trigger { font-size: 0.78rem; color: #14B8A6; cursor: pointer; user-select: none; font-family: 'JetBrains Mono', monospace; }
  .dtp-fade-up-target { padding: 12px 16px; background: #335F5F; color: white; border-radius: 6px; font-size: 0.85rem; opacity: 0; }
  .dtp-fade-up-target.play { animation: dd-fade-up 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
  @keyframes dd-fade-up { 0% { opacity: 0; transform: translateY(20px); } 100% { opacity: 1; transform: translateY(0); } }

  /* dd-hover-lift demo: card */
  .dtp-hover-lift {
    padding: 16px 20px;
    background: white;
    border: 1px solid #E4E7EE;
    border-radius: 8px;
    font-size: 0.85rem;
    color: #0E121C;
    cursor: pointer;
    transition: all 240ms cubic-bezier(0.2, 0.8, 0.2, 1);
    box-shadow: 0 1px 2px rgba(14, 18, 28, 0.04), 0 1px 3px rgba(14, 18, 28, 0.06);
  }
  .dtp-hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(14, 18, 28, 0.08), 0 4px 8px rgba(14, 18, 28, 0.04); }

  .dtp-easing-table { width: 100%; border-collapse: collapse; background: white; border: 1px solid #E4E7EE; border-radius: 8px; overflow: hidden; }
  .dtp-easing-table th, .dtp-easing-table td { padding: 12px 16px; text-align: left; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; }
  .dtp-easing-table th { background: #F7F8FA; color: #424A5C; font-weight: 600; }
  .dtp-easing-table tr { border-bottom: 1px solid #E4E7EE; }
  .dtp-easing-table tr:last-child { border-bottom: none; }
  .dtp-easing-table .duration-col { color: #14B8A6; }

  /* === Section 6: Code blocks === */
  .dtp-codeblock {
    background: #0F1428;
    color: #E1E4F0;
    padding: 24px;
    border-radius: 12px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    line-height: 1.6;
    overflow-x: auto;
    box-shadow: 0 12px 32px rgba(14, 18, 28, 0.15);
  }
  .dtp-codeblock .header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 16px;
    margin-bottom: 16px;
    border-bottom: 1px solid #1A1F2A;
  }
  .dtp-codeblock .dot { width: 12px; height: 12px; border-radius: 50%; background: #5A6275; }
  .dtp-codeblock .title-bar { color: #8A8FA3; font-size: 0.78rem; margin-left: 12px; }
  .dtp-codeblock .keyword { color: #F472B6; }
  .dtp-codeblock .string { color: #86EFAC; }
  .dtp-codeblock .num { color: #FBBF24; }
  .dtp-codeblock .fn { color: #14B8A6; font-weight: 500; }
  .dtp-codeblock .comment { color: #8A8FA3; font-style: italic; }
  .dtp-codeblock .line { display: block; }

  .dtp-codetokens { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-top: 24px; }
  .dtp-codetoken-card { background: white; border: 1px solid #E4E7EE; border-radius: 8px; padding: 16px; }
  .dtp-codetoken-card .swatch { width: 24px; height: 24px; border-radius: 4px; display: inline-block; vertical-align: middle; margin-right: 8px; }
  .dtp-codetoken-card .token { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 600; color: #424A5C; vertical-align: middle; }
  .dtp-codetoken-card .hex { font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; color: #7B8395; display: block; margin-top: 6px; }
  .dtp-codetoken-card .usage { font-size: 0.78rem; color: #7B8395; display: block; margin-top: 4px; }

  /* === Section 7: Icons Boxicons === */
  .dtp-icon-subtitle { font-size: 1.1rem; margin-top: 32px; margin-bottom: 16px; color: #424A5C; font-weight: 600; }
  .dtp-icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; }
  .dtp-icon-card {
    background: white;
    border: 1px solid #E4E7EE;
    border-radius: 8px;
    padding: 20px 16px;
    text-align: center;
    transition: all 240ms cubic-bezier(0.2, 0.8, 0.2, 1);
  }
  .dtp-icon-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(14, 18, 28, 0.06), 0 2px 4px rgba(14, 18, 28, 0.04); }
  .dtp-icon-tile {
    width: 80px;
    height: 80px;
    margin: 0 auto 12px;
    background: rgba(51, 95, 95, 0.1);  /* Petrol Teal 10% */
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .dtp-icon-tile i { font-size: 32px; color: #335F5F; }
  .dtp-icon-card .label { font-size: 0.9rem; font-weight: 600; color: #0E121C; margin-bottom: 4px; }
  .dtp-icon-card .bx-class { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #7B8395; }

  /* Footer info */
  .dtp-footer { padding: 32px 0; border-top: 1px solid #E4E7EE; color: #7B8395; font-size: 0.85rem; text-align: center; font-family: 'JetBrains Mono', monospace; }
</style>
@endsection

@php
  // Resolve current commit SHA without depending on shell_exec.
  // Reads .git/HEAD then resolves the ref file. Silent fallback to 'unknown'.
  $gitSha = 'unknown';
  try {
      $headFile = base_path('.git/HEAD');
      if (is_readable($headFile)) {
          $head = trim(file_get_contents($headFile));
          if (str_starts_with($head, 'ref: ')) {
              $refFile = base_path('.git/' . trim(substr($head, 5)));
              if (is_readable($refFile)) {
                  $gitSha = substr(trim(file_get_contents($refFile)), 0, 7);
              }
          } else {
              $gitSha = substr($head, 0, 7);
          }
      }
  } catch (\Throwable $e) {
      // silent
  }
@endphp

@section('content')
<div class="dtp-page">
  <div class="dtp-container">

    {{-- ═══════════════════════════════ HEADER ═══════════════════════════════ --}}
    <header class="dtp-header">
      <h1>Design System Preview <span class="badge-dir-a">Direction A</span></h1>
      <p class="subtitle">Sprint 1.5 — Brand Kit v1.2 + extensions Étape 2 (carrier-grade épuré, style Telnyx + Twilio)</p>
      <p class="meta">Build: <strong>{{ $gitSha }}</strong> · {{ now()->format('Y-m-d H:i') }} · branch: feature/sprint-1-5-redesign · internal dev page (noindex via commonMaster)</p>
    </header>

    {{-- ═══════════════════════════════ 1. COULEURS ═══════════════════════════════ --}}
    <section class="dtp-section" id="colors">
      <h2>1. Couleurs</h2>
      <p class="section-intro">Brand Kit v1.2 — système hiérarchisé Primary teal (~30%) + Secondary noir (~15%) + Tertiary cyan spot (≤5%) + Foundation neutres (~50%).</p>

      {{-- Couleurs principales --}}
      <div class="dtp-color-grid" style="margin-bottom: 48px;">
        <div class="dtp-color-card">
          <div class="dtp-color-swatch" style="background: #335F5F;"></div>
          <div class="dtp-color-meta">
            <span class="token">$dd-primary-500</span>
            <span class="hex">#335F5F</span>
            <span class="usage">Petrol Teal · Primary 30% (branding, sidebar admin, brand titles, focus rings)</span>
          </div>
        </div>
        <div class="dtp-color-card">
          <div class="dtp-color-swatch" style="background: #0E121C;"></div>
          <div class="dtp-color-meta">
            <span class="token">$dd-secondary</span>
            <span class="hex">#0E121C</span>
            <span class="usage">Action Black · Secondary 15% (CTAs critiques, headings, code block bg)</span>
          </div>
        </div>
        <div class="dtp-color-card">
          <div class="dtp-color-swatch" style="background: #14B8A6;"></div>
          <div class="dtp-color-meta">
            <span class="token">$dd-tertiary-500</span>
            <span class="hex">#14B8A6</span>
            <span class="usage">Tertiary Cyan · Spot ≤5% (live indicators, badges NEW, code fn signature, focus rings)</span>
          </div>
        </div>
        <div class="dtp-color-card">
          <div class="dtp-color-swatch" style="background: #FFFFFF; border: 1px solid #E4E7EE;"></div>
          <div class="dtp-color-meta">
            <span class="token">$dd-surface</span>
            <span class="hex">#FFFFFF</span>
            <span class="usage">Foundation · surfaces principales (~50%)</span>
          </div>
        </div>
      </div>

      {{-- Échelle Primary --}}
      <div class="dtp-shade-row">
        <div class="row-label">$dd-primary-* (Petrol Teal scale, 50→900)</div>
        <div class="dtp-color-grid brand-shades">
          @php
            $primaryShades = [
              ['50','#F0F4F4',true],['100','#DCE6E6',true],['200','#BCD0D0',true],['300','#9CBABA',true],
              ['400','#6E9999',false],['500','#335F5F',false],['600','#2A4F4F',false],['700','#224242',false],
              ['800','#1A3535',false],['900','#0F2222',false],
            ];
          @endphp
          @foreach($primaryShades as [$step, $hex, $light])
            <div class="dtp-color-shade-swatch {{ $light ? 'lightish' : '' }}" style="background: {{ $hex }};">{{ $step }}</div>
          @endforeach
        </div>
      </div>

      {{-- Échelle Tertiary Cyan --}}
      <div class="dtp-shade-row">
        <div class="row-label">$dd-tertiary-* (Tertiary Cyan scale, 50→900) — usage ≤5% par vue</div>
        <div class="dtp-color-grid brand-shades">
          @php
            $tertiaryShades = [
              ['50','#ECFDF7',true],['100','#CCF8EE',true],['200','#99F1DD',true],['300','#5EE3C6',true],
              ['400','#2DC9AB',false],['500','#14B8A6',false],['600','#0F9988',false],['700','#0F766E',false],
              ['800','#115E56',false],['900','#134E48',false],
            ];
          @endphp
          @foreach($tertiaryShades as [$step, $hex, $light])
            <div class="dtp-color-shade-swatch {{ $light ? 'lightish' : '' }}" style="background: {{ $hex }};">{{ $step }}</div>
          @endforeach
        </div>
      </div>

      {{-- Échelle Ink --}}
      <div class="dtp-shade-row">
        <div class="row-label">$dd-ink-* (Foundation grays, warm-tinted toward teal)</div>
        <div class="dtp-color-grid brand-shades">
          @php
            $inkShades = [
              ['50','#F7F8FA',true],['100','#E4E7EE',true],['200','#C9CFD9',true],['300','#A5ADBC',true],
              ['400','#7B8395',true],['500','#5A6275',false],['600','#424A5C',false],['700','#2D3340',false],
              ['800','#1A1F2A',false],['900','#0E121C',false],
            ];
          @endphp
          @foreach($inkShades as [$step, $hex, $light])
            <div class="dtp-color-shade-swatch {{ $light ? 'lightish' : '' }}" style="background: {{ $hex }};">{{ $step }}</div>
          @endforeach
        </div>
      </div>

      {{-- Couleurs sémantiques --}}
      <div class="dtp-shade-row">
        <div class="row-label">Couleurs sémantiques (status indicators only)</div>
        <div class="dtp-color-grid">
          <div class="dtp-color-card">
            <div class="dtp-color-swatch" style="background: #0EBE82;"></div>
            <div class="dtp-color-meta"><span class="token">$dd-success</span><span class="hex">#0EBE82</span><span class="usage">Delivered, completed</span></div>
          </div>
          <div class="dtp-color-card">
            <div class="dtp-color-swatch" style="background: #F2A93B;"></div>
            <div class="dtp-color-meta"><span class="token">$dd-warning</span><span class="hex">#F2A93B</span><span class="usage">Pending, throttled</span></div>
          </div>
          <div class="dtp-color-card">
            <div class="dtp-color-swatch" style="background: #EF4361;"></div>
            <div class="dtp-color-meta"><span class="token">$dd-danger</span><span class="hex">#EF4361</span><span class="usage">Failed, blocked</span></div>
          </div>
          <div class="dtp-color-card">
            <div class="dtp-color-swatch" style="background: #3A86FF;"></div>
            <div class="dtp-color-meta"><span class="token">$dd-info</span><span class="hex">#3A86FF</span><span class="usage">Info, neutral notice</span></div>
          </div>
        </div>
      </div>
    </section>

    {{-- ═══════════════════════════════ 2. TYPOGRAPHIE ═══════════════════════════════ --}}
    <section class="dtp-section" id="typography">
      <h2>2. Typographie</h2>
      <p class="section-intro">Inter (300-800) pour display + body · JetBrains Mono (400-600) pour code et données techniques. Stack chargée via Google Fonts en <code>commonMaster.blade.php:64</code>.</p>

      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-display-1</span><span class="clamp">clamp(2.5rem, 6vw, 5rem)</span><span class="weight">Inter 800 · letter-spacing -0.025em</span></div>
        <div class="dtp-type-sample display-1">L'infrastructure</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-display-2</span><span class="clamp">clamp(2rem, 4.5vw, 3.5rem)</span><span class="weight">Inter 700 · -0.02em</span></div>
        <div class="dtp-type-sample display-2">Voice. SMS. eSIM. And More.</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-h1</span><span class="clamp">clamp(1.75rem, 3.5vw, 2.5rem)</span><span class="weight">Inter 700 · -0.015em</span></div>
        <div class="dtp-type-sample h1">Une infrastructure télécom globale</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-h2</span><span class="clamp">clamp(1.5rem, 2.5vw, 2rem)</span><span class="weight">Inter 700</span></div>
        <div class="dtp-type-sample h2">Section title — services Dream Digital</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-h3</span><span class="clamp">1.25rem (20px)</span><span class="weight">Inter 600</span></div>
        <div class="dtp-type-sample h3">SMS A2P · Voice Wholesale · DID</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-body-lg</span><span class="clamp">1.125rem (18px)</span><span class="weight">Inter 400 · line-height 1.6</span></div>
        <div class="dtp-type-sample body-lg">Lead paragraph. Notifications, OTP, marketing — sous une seule plateforme avec une exigence carrier-grade.</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-body</span><span class="clamp">1rem (16px)</span><span class="weight">Inter 400 · 1.55</span></div>
        <div class="dtp-type-sample body">Body par défaut. Routes premium vers plus de 200 pays, monitoring en temps réel, support du concaténé et de l'unicode. Trunks SIP haute disponibilité avec encryption TLS/SRTP et failover automatique.</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-body-sm</span><span class="clamp">0.875rem (14px)</span><span class="weight">Inter 400 · 1.5</span></div>
        <div class="dtp-type-sample body-sm">Body small. Tarifs par destination, support 24/7, intégration directe IPBX cloud.</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-caption</span><span class="clamp">0.75rem (12px)</span><span class="weight">Inter 400 · gray 400</span></div>
        <div class="dtp-type-sample caption">CAPTION · LABEL · UPPERCASE TRACKING WIDE</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-font-family-mono</span><span class="clamp">JetBrains Mono</span><span class="weight">400-500-600 inline</span></div>
        <div><span class="dtp-type-sample mono-inline">curl -X POST /v1/sms/send</span> code inline — utilisable dans le body courant.</div>
      </div>
      <div class="dtp-type-row">
        <div class="dtp-type-meta"><span class="token">$dd-font-family-mono</span><span class="clamp">JetBrains Mono block</span><span class="weight">dark on Action Black</span></div>
        <div class="dtp-type-sample mono-block">$ dream-digital agents create<br>✓ provisioning runtime ...<br>✓ ready: agent_id = ag_a8b2589f</div>
      </div>
    </section>

    {{-- ═══════════════════════════════ 3. ESPACEMENTS ═══════════════════════════════ --}}
    <section class="dtp-section" id="spacing">
      <h2>3. Espacements et layout</h2>
      <p class="section-intro">Multiples de 4px pour cohérence stricte · clamp() pour fluid scaling responsive · containers max 1280px (large) et 960px (narrow éditorial).</p>

      <h3 style="font-size:1.1rem;margin-top:32px;margin-bottom:16px;color:#424A5C;">Section padding (vertical breathing)</h3>
      <div class="dtp-spacing-grid">
        @php
          $spacings = [
            ['$dd-section-py-sm','clamp(3rem, 6vw, 5rem)','48-80px','25'],
            ['$dd-section-py-md','clamp(5rem, 10vw, 8rem)','80-128px','50'],
            ['$dd-section-py-lg','clamp(6rem, 14vw, 12rem)','96-192px','100'],
          ];
        @endphp
        @foreach($spacings as [$token, $clamp, $px, $w])
          <div class="dtp-spacing-row">
            <span class="label">{{ $token }}</span>
            <span class="value">{{ $clamp }}</span>
            <span style="font-size:0.85rem;color:#7B8395;min-width:80px;">{{ $px }}</span>
            <div class="dtp-spacing-bar" style="--w: {{ $w }}%;"></div>
          </div>
        @endforeach
      </div>

      <h3 style="font-size:1.1rem;margin-top:32px;margin-bottom:16px;color:#424A5C;">Containers</h3>
      <div class="dtp-spacing-grid">
        <div class="dtp-spacing-row">
          <span class="label">$dd-container-max</span>
          <span class="value">1280px</span>
          <span style="font-size:0.85rem;color:#7B8395;">Hero, sections principales</span>
        </div>
        <div class="dtp-spacing-row">
          <span class="label">$dd-container-narrow</span>
          <span class="value">960px</span>
          <span style="font-size:0.85rem;color:#7B8395;">Contenu éditorial (blog, à propos, légal)</span>
        </div>
      </div>

      <h3 style="font-size:1.1rem;margin-top:32px;margin-bottom:16px;color:#424A5C;">Border-radius (carrier-grade industrial)</h3>
      <div class="dtp-radius-grid">
        @php
          $radii = [
            ['$dd-radius-xs','4px','0.25rem'],['$dd-radius-sm','6px','0.375rem'],['$dd-radius-md','8px','0.5rem'],
            ['$dd-radius-lg','12px','0.75rem'],['$dd-radius-xl','16px','1rem'],['$dd-radius-2xl','24px','1.5rem'],
          ];
        @endphp
        @foreach($radii as [$token, $px, $rem])
          <div class="dtp-radius-card">
            <div class="dtp-radius-shape" style="border-radius: {{ $rem }};"></div>
            <div class="meta">
              <div>{{ $token }}</div>
              <div><span class="px">{{ $px }}</span> · {{ $rem }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ═══════════════════════════════ 4. OMBRES ═══════════════════════════════ --}}
    <section class="dtp-section" id="shadows">
      <h2>4. Ombres</h2>
      <p class="section-intro">Philosophie "tonal layers" : ombres légères pour hover ou éléments flottants, jamais dramatiques. Glow Tertiary Cyan pour éléments featured.</p>
      <div class="dtp-shadow-grid" style="background:#FAFAFA;">
        <div class="dtp-shadow-box shadow-sm"><span class="name">$dd-shadow-sm</span><span class="desc">subtle border alternative</span></div>
        <div class="dtp-shadow-box shadow-md"><span class="name">$dd-shadow-md</span><span class="desc">cards, dropdowns</span></div>
        <div class="dtp-shadow-box shadow-lg"><span class="name">$dd-shadow-lg</span><span class="desc">hover lift, modals</span></div>
        <div class="dtp-shadow-box shadow-xl"><span class="name">$dd-shadow-xl</span><span class="desc">major popovers</span></div>
        <div class="dtp-shadow-box shadow-focus"><span class="name">$dd-shadow-focus</span><span class="desc">focus rings (a11y)</span></div>
        <div class="dtp-shadow-box shadow-glow"><span class="name">$dd-shadow-glow</span><span class="desc">featured spotlight (Tertiary Cyan)</span></div>
      </div>
    </section>

    {{-- ═══════════════════════════════ 5. ANIMATIONS ═══════════════════════════════ --}}
    <section class="dtp-section" id="animations">
      <h2>5. Animations</h2>
      <p class="section-intro">Easings, durations et keyframes nommés. Hover les cards ci-dessous ou clique sur les triggers pour visualiser.</p>

      <h3 style="font-size:1.1rem;margin-top:32px;margin-bottom:16px;color:#424A5C;">Easings & durations</h3>
      <table class="dtp-easing-table">
        <thead>
          <tr><th>Token</th><th>Valeur</th><th>Usage</th></tr>
        </thead>
        <tbody>
          <tr><td>$dd-ease-out</td><td>cubic-bezier(0.2, 0.8, 0.2, 1)</td><td>Hover states, transitions standard</td></tr>
          <tr><td>$dd-ease-in-out</td><td>cubic-bezier(0.4, 0, 0.2, 1)</td><td>Modals, slide transitions</td></tr>
          <tr><td>$dd-ease-bounce</td><td>cubic-bezier(0.68, -0.55, 0.265, 1.55)</td><td>Playful interactions (rare)</td></tr>
          <tr><td>$dd-duration-fast</td><td><span class="duration-col">140ms</span></td><td>Hover states</td></tr>
          <tr><td>$dd-duration-normal</td><td><span class="duration-col">240ms</span></td><td>Transitions standard</td></tr>
          <tr><td>$dd-duration-slow</td><td><span class="duration-col">480ms</span></td><td>Section entries</td></tr>
          <tr><td>$dd-duration-slower</td><td><span class="duration-col">800ms</span></td><td>Hero animations</td></tr>
        </tbody>
      </table>

      <h3 style="font-size:1.1rem;margin-top:48px;margin-bottom:16px;color:#424A5C;">Keyframes nommés</h3>
      <div class="dtp-anim-grid">
        <div class="dtp-anim-card">
          <div class="anim-name">@keyframes dd-pulse</div>
          <div class="anim-desc">Cercle qui pulse (live status indicators). Animation 2s ease-out infinite.</div>
          <div class="anim-stage">
            <div class="dtp-pulse-wrap">
              <div class="dtp-pulse-ring"></div>
              <div class="dtp-pulse-dot"></div>
            </div>
          </div>
        </div>

        <div class="dtp-anim-card">
          <div class="anim-name">@keyframes dd-fade-up</div>
          <div class="anim-desc">Translation verticale + fade-in (stagger scroll-triggered). Click pour rejouer.</div>
          <div class="anim-stage">
            <div class="dtp-fade-up-target play" id="fadeup-demo">opacity 0 → 1 · translateY(20px → 0)</div>
          </div>
          <div style="text-align:center;margin-top:12px;"><span class="dtp-fade-up-trigger" onclick="document.getElementById('fadeup-demo').classList.remove('play'); setTimeout(()=>document.getElementById('fadeup-demo').classList.add('play'),20);">▶ replay</span></div>
        </div>

        <div class="dtp-anim-card">
          <div class="anim-name">@keyframes dd-hover-lift</div>
          <div class="anim-desc">Lift vertical + élévation shadow. Hover la card ci-dessous.</div>
          <div class="anim-stage">
            <div class="dtp-hover-lift">↑ Hover-me to lift</div>
          </div>
        </div>
      </div>
    </section>

    {{-- ═══════════════════════════════ 6. CODE BLOCKS ═══════════════════════════════ --}}
    <section class="dtp-section" id="code-blocks">
      <h2>6. Code blocks (section Developer-First)</h2>
      <p class="section-intro">Tokens spécifiques pour terminaux et code snippets de la home — fond <code>#0F1428</code> dark navy + syntax highlighting calibrée Brand Kit v1.2.</p>

      <div class="dtp-codeblock">
        <div class="header">
          <span class="dot"></span><span class="dot" style="background:#5A6275;"></span><span class="dot" style="background:#5A6275;"></span>
          <span class="title-bar">curl · POST /v1/sms/send</span>
        </div>
        <span class="line"><span class="comment"># Send a transactional SMS via Dream Digital API</span></span>
        <span class="line">$ <span class="fn">curl</span> -X POST <span class="string">"https://api.dream-digital.info/v1/sms/send"</span> \</span>
        <span class="line">    -H <span class="string">"Authorization: Bearer dd_live_xxxxxxxx"</span> \</span>
        <span class="line">    -H <span class="string">"Content-Type: application/json"</span> \</span>
        <span class="line">    -d '{</span>
        <span class="line">      <span class="string">"to"</span>: <span class="string">"+243812345678"</span>,</span>
        <span class="line">      <span class="string">"from"</span>: <span class="string">"DreamDigital"</span>,</span>
        <span class="line">      <span class="string">"text"</span>: <span class="string">"Votre code OTP : 749210"</span></span>
        <span class="line">    }'</span>
        <span class="line"></span>
        <span class="line"><span class="comment"># Response</span></span>
        <span class="line">{</span>
        <span class="line">  <span class="string">"id"</span>: <span class="string">"sms_a8b2589fc01"</span>,</span>
        <span class="line">  <span class="string">"status"</span>: <span class="string">"queued"</span>,</span>
        <span class="line">  <span class="string">"to"</span>: <span class="string">"+243812345678"</span>,</span>
        <span class="line">  <span class="string">"segments"</span>: <span class="num">1</span>,</span>
        <span class="line">  <span class="string">"cost_usd"</span>: <span class="num">0.0089</span>,</span>
        <span class="line">  <span class="string">"created_at"</span>: <span class="string">"2026-05-08T14:23:01Z"</span></span>
        <span class="line">}</span>
      </div>

      <div class="dtp-codetokens">
        <div class="dtp-codetoken-card"><span class="swatch" style="background:#0F1428;"></span><span class="token">$dd-code-bg</span><span class="hex">#0F1428</span><span class="usage">Background dark navy</span></div>
        <div class="dtp-codetoken-card"><span class="swatch" style="background:#E1E4F0;"></span><span class="token">$dd-code-text</span><span class="hex">#E1E4F0</span><span class="usage">Texte par défaut</span></div>
        <div class="dtp-codetoken-card"><span class="swatch" style="background:#F472B6;"></span><span class="token">$dd-code-keyword</span><span class="hex">#F472B6</span><span class="usage">Mots-clés langage, clés JSON</span></div>
        <div class="dtp-codetoken-card"><span class="swatch" style="background:#86EFAC;"></span><span class="token">$dd-code-string</span><span class="hex">#86EFAC</span><span class="usage">Strings entre guillemets</span></div>
        <div class="dtp-codetoken-card"><span class="swatch" style="background:#FBBF24;"></span><span class="token">$dd-code-num</span><span class="hex">#FBBF24</span><span class="usage">Nombres littéraux</span></div>
        <div class="dtp-codetoken-card"><span class="swatch" style="background:#14B8A6;"></span><span class="token">$dd-code-fn</span><span class="hex">#14B8A6</span><span class="usage">⚠ Tertiary Cyan v1.2 — fonctions, signature DD (PAS le #00D9FF obsolète)</span></div>
        <div class="dtp-codetoken-card"><span class="swatch" style="background:#8A8FA3;"></span><span class="token">$dd-code-comment</span><span class="hex">#8A8FA3</span><span class="usage">Commentaires inline / blocs</span></div>
      </div>
    </section>

    {{-- ═══════════════════════════════ 7. ICONS BOXICONS ═══════════════════════════════ --}}
    <section class="dtp-section" id="icons">
      <h2>7. Iconographie (Boxicons)</h2>
      <p class="section-intro">Iconset Boxicons (chargé via <code>vite.icons.plugin.js</code> post-S9, décision Q13). Icônes ci-dessous lues directement depuis les configs CMS-ready <code>config/dream-digital/services.php</code> + <code>industries.php</code> — démonstration de l'architecture content-via-config en action.</p>

      <h3 class="dtp-icon-subtitle">Services (6 — depuis services.php)</h3>
      <div class="dtp-icon-grid">
        @foreach(config('dream-digital.services.items', []) as $service)
          <div class="dtp-icon-card">
            <div class="dtp-icon-tile"><i class="bx {{ $service['icon'] }}"></i></div>
            <div class="label">{{ $service['name']['fr'] }}</div>
            <div class="bx-class">{{ $service['icon'] }}</div>
          </div>
        @endforeach
      </div>

      <h3 class="dtp-icon-subtitle">Industries / Cas d'usage (4 — depuis industries.php)</h3>
      <div class="dtp-icon-grid">
        @foreach(config('dream-digital.industries.items', []) as $industry)
          <div class="dtp-icon-card">
            <div class="dtp-icon-tile"><i class="bx {{ $industry['icon'] }}"></i></div>
            <div class="label">{{ $industry['name']['fr'] }}</div>
            <div class="bx-class">{{ $industry['icon'] }}</div>
          </div>
        @endforeach
      </div>
    </section>

    {{-- ═══════════════════════════════ FOOTER ═══════════════════════════════ --}}
    <div class="dtp-footer">
      End of preview · Build <strong>{{ $gitSha }}</strong> · Source : <code>resources/assets/vendor/scss/_custom-variables/_dream-digital.scss</code> + <code>dream-digital/_dd-code-blocks.scss</code> · Q21 jurisprudence respectée (single override hook Sneat)
    </div>

  </div>
</div>
@endsection
