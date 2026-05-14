<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sprint 1 Test - {{ $mode }}</title>
  <style>
    body {
      background: #f7faf9;
      color: #0e121c;
      font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      margin: 0;
    }

    main {
      margin: 40px auto;
      max-width: 980px;
      padding: 0 20px;
    }

    h1, h2 {
      letter-spacing: 0;
    }

    .panel {
      background: #fff;
      border: 1px solid rgba(14, 18, 28, .1);
      border-radius: 8px;
      margin-block: 20px;
      padding: 20px;
    }

    .badge {
      background: #e7f5f2;
      border: 1px solid rgba(20, 184, 166, .2);
      border-radius: 999px;
      color: #335f5f;
      display: inline-flex;
      font-weight: 700;
      margin: 0 8px 8px 0;
      padding: 6px 10px;
    }

    nav a {
      color: #335f5f;
      display: inline-block;
      font-weight: 700;
      margin: 0 12px 10px 0;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      border-bottom: 1px solid rgba(14, 18, 28, .1);
      padding: 12px;
      text-align: left;
      vertical-align: top;
    }

    code {
      background: #eef7f5;
      border-radius: 4px;
      padding: 2px 5px;
    }

    .dd-text-muted {
      color: #667085;
    }
  </style>
</head>
<body>
  <main>
    <h1>Sprint 1 - Page de test</h1>

    <section class="panel">
      <span class="badge">Mode: {{ $mode }}</span>
      <span class="badge">Locale: {{ app()->getLocale() }}</span>
      @if(isset($currentCountry) && $currentCountry)
        <span class="badge">Country: {{ $currentCountry->flag_emoji }} {{ $currentCountry->name }} ({{ $currentCountry->default_currency_code }})</span>
      @endif
    </section>

    <nav class="panel">
      <strong>Navigation test</strong><br>
      <a href="/fr">/fr</a>
      <a href="/en">/en</a>
      <a href="/cd/fr">/cd/fr</a>
      <a href="/cd/en">/cd/en</a>
      <a href="/cg/fr">/cg/fr</a>
      <a href="/ci/fr">/ci/fr</a>
      <a href="/ci/en">/ci/en</a>
      <a href="/_reset-country">Reset country</a>
    </nav>

    <section class="panel">
      <h2>Services en base</h2>
      <table>
        <thead>
          <tr>
            <th>Slug</th>
            <th>Nom</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          @forelse($services as $service)
            <tr>
              <td><code>{{ $service->slug }}</code></td>
              <td>{{ $service->name }}</td>
              <td>{{ $service->short_desc ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="3">Migrations/seeders pas encore executes.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </section>

    <section class="panel">
      <h2>Test PriceFormatter</h2>
      <p>Prix de reference: SMS @ 0.0089 USD.</p>
      <p>Affichage adaptatif: @price($price)</p>
    </section>
  </main>
</body>
</html>
