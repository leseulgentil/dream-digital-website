# BRIEF — Sprint 1 : Fondations multi-pays + i18n

> **Prérequis** : le brief `BRIEF_DD_DESANONYMIZATION.md` doit avoir été exécuté en amont. Le projet doit être propre, customisé aux couleurs Dream Digital, et exempt des résidus Sneat reconnaissables.

> **Durée estimée** : 1 semaine de travail Claude Code supervisé.

> **Branche Git** : `feature/sprint-1-foundations`

## Objectif global du Sprint 1

Mettre en place les **fondations techniques** qui permettront au site Dream Digital de gérer :

1. **3 langues** (FR par défaut, EN)
2. **4 contextes pays** : Global (USD), RDC (USD+CDF dual), Congo-Brazzaville (XAF), Côte d'Ivoire (XOF)
3. **Detection IP** sur la racine `/` avec redirection respectueuse du choix utilisateur
4. **URL pattern propre** : `/{country?}/{locale}/{section}/{page}`
5. **Modèles Eloquent** pour `Country`, `Service`, `ServicePrice`, `Page`
6. **Middleware central** `SetCountryAndLocale`
7. **Service de conversion de devises** avec cache Redis
8. **Helper d'affichage des prix** multi-devises

À la fin du Sprint 1, on doit pouvoir :
- Naviguer sur `/cd/fr/`, `/ci/en/`, `/fr`, `/en` et obtenir des pages de debug différentes
- Créer un `ServicePrice` en base et l'afficher avec le bon formatage devise
- Tester la geo-detection avec un IP de RDC simulé

**Aucune page vitrine "réelle" ne sera construite au Sprint 1** — ce sera l'objet du Sprint 2. Le Sprint 1 produit le **socle** sur lequel construire.

---

## 1. Migrations à créer

### 1.1 Table `countries`

```php
// database/migrations/2026_05_03_000001_create_countries_table.php
Schema::create('countries', function (Blueprint $table) {
    $table->id();
    $table->string('code', 8)->unique()->comment("'cd', 'cg', 'ci', 'global'");
    $table->string('name_fr');
    $table->string('name_en');
    $table->string('default_currency_code', 3)->comment("'USD', 'CDF', 'XAF', 'XOF'");
    $table->string('secondary_currency_code', 3)->nullable()->comment("Pour dual currency RDC : 'CDF' avec USD primary");
    $table->boolean('show_dual_currency')->default(false);
    $table->string('default_locale', 2);
    $table->json('available_locales')->comment("['fr', 'en']");
    $table->string('phone_prefix', 8)->comment("'+243'");
    $table->string('sales_email');
    $table->string('sales_phone')->nullable();
    $table->text('office_address')->nullable();
    $table->string('flag_emoji', 8)->comment("'🇨🇩'");
    $table->boolean('is_global')->default(false);
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### 1.2 Table `services`

```php
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique()->comment("'sms', 'voice', 'did', 'sip', 'dialo', 'esim'");
    $table->string('name_fr');
    $table->string('name_en');
    $table->string('icon')->comment("Tabler icon name, ex: 'ti-message-2'");
    $table->string('color_accent')->nullable()->comment("Hex color override");
    $table->text('short_desc_fr')->nullable();
    $table->text('short_desc_en')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### 1.3 Table `service_prices`

```php
Schema::create('service_prices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_id')->constrained()->cascadeOnDelete();
    $table->foreignId('country_id')->constrained()->cascadeOnDelete();
    $table->string('destination_country', 3)->nullable()->comment("ISO code pour SMS/voice par corridor, null pour service global");
    $table->string('label_fr')->comment("'SMS vers RDC', 'Numéro DID Kinshasa'");
    $table->string('label_en');
    $table->decimal('price_usd', 12, 6)->comment("Prix base USD, toujours rempli");
    $table->decimal('price_local', 12, 6)->nullable()->comment("Override manuel devise locale");
    $table->string('local_currency', 3)->nullable();
    $table->string('unit', 20)->comment("'sms', 'minute', 'month', 'year', 'fixed'");
    $table->boolean('use_manual_local')->default(false)->comment("true = utiliser price_local, false = convertir auto");
    $table->boolean('is_published')->default(true);
    $table->foreignId('updated_by')->nullable()->constrained('users');
    $table->timestamps();
    
    $table->index(['service_id', 'country_id', 'is_published']);
});
```

### 1.4 Table `pages`

```php
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->comment("'sms', 'about', 'contact'");
    $table->string('section')->comment("'products', 'solutions', 'company', 'developers', 'pricing', 'legal'");
    $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete()->comment("NULL = page commune à tous pays");
    $table->string('locale', 2);
    $table->string('title');
    $table->string('meta_description', 500)->nullable();
    $table->string('meta_image_path')->nullable()->comment("OpenGraph image path");
    $table->json('content_blocks')->nullable()->comment("Structure JSON modulaire : hero, features, faq, etc.");
    $table->boolean('is_published')->default(true);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    
    $table->unique(['slug', 'section', 'country_id', 'locale'], 'pages_uniqueness');
});
```

---

## 2. Modèles Eloquent

### 2.1 `app/Models/Country.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Country extends Model
{
    protected $fillable = [
        'code', 'name_fr', 'name_en', 'default_currency_code',
        'secondary_currency_code', 'show_dual_currency',
        'default_locale', 'available_locales', 'phone_prefix',
        'sales_email', 'sales_phone', 'office_address',
        'flag_emoji', 'is_global', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'available_locales' => AsCollection::class,
        'show_dual_currency' => 'boolean',
        'is_global' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function servicePrices()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'en' ? $this->name_en : $this->name_fr;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNonGlobal($query)
    {
        return $query->where('is_global', false);
    }
}
```

### 2.2 `app/Models/Service.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'slug', 'name_fr', 'name_en', 'icon', 'color_accent',
        'short_desc_fr', 'short_desc_en', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function prices()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'en' ? $this->name_en : $this->name_fr;
    }

    public function getShortDescAttribute(): ?string
    {
        return app()->getLocale() === 'en' ? $this->short_desc_en : $this->short_desc_fr;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
```

### 2.3 `app/Models/ServicePrice.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    protected $fillable = [
        'service_id', 'country_id', 'destination_country',
        'label_fr', 'label_en', 'price_usd', 'price_local',
        'local_currency', 'unit', 'use_manual_local',
        'is_published', 'updated_by',
    ];

    protected $casts = [
        'price_usd' => 'decimal:6',
        'price_local' => 'decimal:6',
        'use_manual_local' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getLabelAttribute(): string
    {
        return app()->getLocale() === 'en' ? $this->label_en : $this->label_fr;
    }
}
```

### 2.4 `app/Models/Page.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug', 'section', 'country_id', 'locale',
        'title', 'meta_description', 'meta_image_path',
        'content_blocks', 'is_published', 'published_at',
    ];

    protected $casts = [
        'content_blocks' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
```

---

## 3. Seeders

### 3.1 `database/seeders/CountrySeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'code' => 'global', 'name_fr' => 'International', 'name_en' => 'International',
                'default_currency_code' => 'USD', 'show_dual_currency' => false,
                'default_locale' => 'fr', 'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+1', 'sales_email' => 'sales@dream-digital.info',
                'flag_emoji' => '🌍', 'is_global' => true, 'sort_order' => 0,
            ],
            [
                'code' => 'cd', 'name_fr' => 'République Démocratique du Congo', 'name_en' => 'Democratic Republic of Congo',
                'default_currency_code' => 'USD', 'secondary_currency_code' => 'CDF',
                'show_dual_currency' => true,
                'default_locale' => 'fr', 'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+243', 'sales_email' => 'sales.cd@dream-digital.info',
                'sales_phone' => '+243 ...', 'office_address' => 'Kinshasa, RDC',
                'flag_emoji' => '🇨🇩', 'is_global' => false, 'sort_order' => 1,
            ],
            [
                'code' => 'cg', 'name_fr' => 'République du Congo', 'name_en' => 'Republic of Congo',
                'default_currency_code' => 'XAF', 'show_dual_currency' => false,
                'default_locale' => 'fr', 'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+242', 'sales_email' => 'sales.cg@dream-digital.info',
                'office_address' => 'Brazzaville, Congo',
                'flag_emoji' => '🇨🇬', 'is_global' => false, 'sort_order' => 2,
            ],
            [
                'code' => 'ci', 'name_fr' => "Côte d'Ivoire", 'name_en' => 'Ivory Coast',
                'default_currency_code' => 'XOF', 'show_dual_currency' => false,
                'default_locale' => 'fr', 'available_locales' => ['fr', 'en'],
                'phone_prefix' => '+225', 'sales_email' => 'sales.ci@dream-digital.info',
                'office_address' => "Abidjan, Côte d'Ivoire",
                'flag_emoji' => '🇨🇮', 'is_global' => false, 'sort_order' => 3,
            ],
        ];

        foreach ($countries as $data) {
            Country::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
```

### 3.2 `database/seeders/ServiceSeeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['slug' => 'sms', 'name_fr' => 'SMS A2P', 'name_en' => 'A2P SMS', 'icon' => 'ti-message-2', 'sort_order' => 1],
            ['slug' => 'voice', 'name_fr' => 'Voice Wholesale', 'name_en' => 'Voice Wholesale', 'icon' => 'ti-phone', 'sort_order' => 2],
            ['slug' => 'did', 'name_fr' => 'Numéros DID', 'name_en' => 'DID Numbers', 'icon' => 'ti-hash', 'sort_order' => 3],
            ['slug' => 'sip-trunking', 'name_fr' => 'SIP Trunking', 'name_en' => 'SIP Trunking', 'icon' => 'ti-network', 'sort_order' => 4],
            ['slug' => 'dialo', 'name_fr' => 'Dialo Contact Center', 'name_en' => 'Dialo Contact Center', 'icon' => 'ti-headset', 'sort_order' => 5],
            ['slug' => 'esim', 'name_fr' => 'eSIM Zone', 'name_en' => 'eSIM Zone', 'icon' => 'ti-device-sim', 'sort_order' => 6],
        ];

        foreach ($services as $data) {
            Service::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
```

### 3.3 Mettre à jour `DatabaseSeeder.php`

```php
public function run(): void
{
    $this->call([
        CountrySeeder::class,
        ServiceSeeder::class,
        // ServicePriceSeeder::class viendra au Sprint 5
    ]);
}
```

---

## 4. Middleware central : `SetCountryAndLocale`

### 4.1 Création

```bash
php artisan make:middleware SetCountryAndLocale
```

### 4.2 Implémentation

```php
<?php

namespace App\Http\Middleware;

use App\Models\Country;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class SetCountryAndLocale
{
    private const SUPPORTED_LOCALES = ['fr', 'en'];

    public function handle(Request $request, Closure $next)
    {
        $segments = $request->segments();
        $country = null;
        $locale = config('app.fallback_locale', 'fr');

        // Cas 1 : URL commence par /fr ou /en (mode Global)
        if (in_array($segments[0] ?? null, self::SUPPORTED_LOCALES)) {
            $locale = $segments[0];
            $country = Country::where('is_global', true)->where('is_active', true)->first();
        }
        // Cas 2 : URL commence par un code pays (cd, cg, ci...)
        elseif (isset($segments[0]) && Country::where('code', $segments[0])->where('is_active', true)->where('is_global', false)->exists()) {
            $country = Country::where('code', $segments[0])->first();
            $locale = (in_array($segments[1] ?? '', self::SUPPORTED_LOCALES)) 
                ? $segments[1] 
                : $country->default_locale;
            
            // Vérifier que la locale est disponible pour ce pays
            if (!$country->available_locales->contains($locale)) {
                $locale = $country->default_locale;
            }
        }
        // Cas 3 : racine sans préfixe — laisser passer (GeoDetectController gère)
        else {
            return $next($request);
        }

        // Configurer Laravel
        App::setLocale($locale);
        \Carbon\Carbon::setLocale($locale);

        // Bind dans le container pour récupération via app('current_country')
        app()->instance('current_country', $country);
        app()->instance('current_locale', $locale);

        // Partager avec toutes les vues
        View::share('currentCountry', $country);
        View::share('currentLocale', $locale);

        return $next($request);
    }
}
```

### 4.3 Enregistrement

Dans `bootstrap/app.php` (Laravel 12) :

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetCountryAndLocale::class,
    ]);
})
```

---

## 5. GeoDetectController — gestion de la racine `/`

### 5.1 Installation du package GeoIP

```bash
composer require torann/geoip
php artisan vendor:publish --provider="Torann\GeoIP\GeoIPServiceProvider" --tag=config
```

Configurer en mode database avec MaxMind GeoLite2 (gratuit) :

```bash
php artisan geoip:update
```

Note : dans `.env` :

```
GEOIP_SERVICE=maxmind_database
GEOIP_DATABASE_PATH=/path/to/GeoLite2-Country.mmdb
```

### 5.2 Controller

```bash
php artisan make:controller GeoDetectController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class GeoDetectController extends Controller
{
    public function index(Request $request)
    {
        // 1. Si l'utilisateur a déjà un cookie de préférence, respecter
        if ($pref = $request->cookie('dd_country_pref')) {
            // Sécurité : valider que le code pays existe et est actif
            $country = Country::where('code', $pref)->where('is_active', true)->first();
            if ($country) {
                $locale = $country->default_locale;
                return redirect("/{$pref}/{$locale}");
            }
        }

        // 2. Détection IP via GeoIP
        try {
            $ipCountryCode = strtolower(geoip($request->ip())->iso_code ?? '');
        } catch (\Exception $e) {
            $ipCountryCode = '';
        }

        // 3. Mapping des pays Dream Digital
        $supportedCountryCodes = Country::where('is_active', true)
            ->where('is_global', false)
            ->pluck('code')
            ->toArray();

        if (in_array($ipCountryCode, $supportedCountryCodes)) {
            $country = Country::where('code', $ipCountryCode)->first();
            $locale = $country->default_locale;
            
            // Cookie 90 jours pour respecter le choix futur
            return redirect("/{$ipCountryCode}/{$locale}")
                ->cookie('dd_country_pref', $ipCountryCode, 60 * 24 * 90);
        }

        // 4. Sinon, version Global FR par défaut
        return redirect('/fr');
    }

    /**
     * Reset cookie + redirect vers Global. Appelé depuis le sélecteur
     * "Voir le site Global" dans le header.
     */
    public function resetToGlobal(Request $request)
    {
        $locale = $request->query('locale', 'fr');
        if (!in_array($locale, ['fr', 'en'])) {
            $locale = 'fr';
        }
        
        return redirect("/{$locale}")
            ->withoutCookie('dd_country_pref');
    }
}
```

### 5.3 Routes (web.php — squelette Sprint 1)

```php
<?php

use App\Http\Controllers\GeoDetectController;
use App\Http\Controllers\Sprint1TestController;
use Illuminate\Support\Facades\Route;

// Racine : geo-detection
Route::get('/', [GeoDetectController::class, 'index']);
Route::get('/_reset-country', [GeoDetectController::class, 'resetToGlobal'])->name('reset-country');

// Mode Global : /fr ou /en
Route::prefix('{locale}')
    ->where(['locale' => 'fr|en'])
    ->group(function () {
        Route::get('/', [Sprint1TestController::class, 'global']);
        Route::get('/test', [Sprint1TestController::class, 'test']);
    });

// Mode pays : /cd/fr, /ci/en, etc.
Route::prefix('{country}/{locale}')
    ->where(['country' => 'cd|cg|ci', 'locale' => 'fr|en'])
    ->group(function () {
        Route::get('/', [Sprint1TestController::class, 'country']);
        Route::get('/test', [Sprint1TestController::class, 'test']);
    });
```

### 5.4 Controller de test Sprint 1

```bash
php artisan make:controller Sprint1TestController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePrice;
use Illuminate\Http\Request;

class Sprint1TestController extends Controller
{
    public function global()
    {
        $services = Service::active()->get();
        return view('sprint1.test-page', [
            'mode' => 'Global',
            'services' => $services,
        ]);
    }

    public function country()
    {
        $services = Service::active()->get();
        return view('sprint1.test-page', [
            'mode' => 'Country: ' . app('current_country')->name,
            'services' => $services,
        ]);
    }

    public function test()
    {
        return view('sprint1.test-page', [
            'mode' => 'Test page',
            'services' => Service::active()->get(),
        ]);
    }
}
```

---

## 6. CurrencyConverter (service)

### 6.1 Implémentation

```php
<?php
// app/Services/CurrencyConverter.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConverter
{
    private const CACHE_HOURS = 6;
    private const API_URL = 'https://api.frankfurter.app/latest';
    
    /**
     * Frankfurter ne supporte pas toutes les devises africaines.
     * Pour CDF, XAF, XOF, on utilise des taux fixes ou une autre source si besoin.
     * À la date de rédaction (2026), Frankfurter supporte XAF et XOF mais pas CDF.
     * Pour CDF, on utilise un fallback configuré ou exchangerate.host.
     */
    private const FALLBACK_RATES_USD = [
        'CDF' => 2800,    // 1 USD ≈ 2800 CDF (à ajuster, le taux peut bouger)
        'XAF' => 600,     // 1 USD ≈ 600 XAF
        'XOF' => 600,     // 1 USD ≈ 600 XOF (parité fixe avec EUR via XOF)
    ];

    public function convert(float $usd, string $toCurrency): ?float
    {
        if ($toCurrency === 'USD') {
            return $usd;
        }

        $rate = $this->getRate($toCurrency);
        return $rate ? $usd * $rate : null;
    }

    private function getRate(string $currency): ?float
    {
        return Cache::remember("fx_USD_{$currency}", now()->addHours(self::CACHE_HOURS), function () use ($currency) {
            // Tentative API Frankfurter
            try {
                $response = Http::timeout(5)->get(self::API_URL, [
                    'from' => 'USD',
                    'to' => $currency,
                ]);

                if ($response->successful() && $response->json("rates.{$currency}")) {
                    return (float) $response->json("rates.{$currency}");
                }
            } catch (\Exception $e) {
                Log::warning('FX rate fetch failed (Frankfurter)', [
                    'currency' => $currency,
                    'error' => $e->getMessage(),
                ]);
            }

            // Fallback : taux fixe configuré
            if (isset(self::FALLBACK_RATES_USD[$currency])) {
                Log::info('Using fallback FX rate', ['currency' => $currency]);
                return self::FALLBACK_RATES_USD[$currency];
            }

            // Aucun taux disponible
            return null;
        });
    }

    public function clearCache(): void
    {
        foreach (['CDF', 'XAF', 'XOF', 'EUR', 'KES', 'RWF'] as $cur) {
            Cache::forget("fx_USD_{$cur}");
        }
    }
}
```

> **Note importante sur les devises** : Frankfurter ne supporte pas toutes les devises africaines en 2026. Il faut prévoir un fallback. Pour le MVP, les fallbacks codés en dur sont suffisants. À l'avenir, on pourra basculer sur `exchangerate.host` ou un service payant comme `OpenExchangeRates` pour avoir CDF/XAF/XOF avec des taux plus précis et à jour.

---

## 7. PriceFormatter (helper)

### 7.1 Implémentation

```php
<?php
// app/Helpers/PriceFormatter.php

namespace App\Helpers;

use App\Models\Country;
use App\Models\ServicePrice;
use App\Services\CurrencyConverter;

class PriceFormatter
{
    /**
     * Formate un ServicePrice selon le pays courant et sa stratégie d'affichage.
     * 
     * Règles :
     * - Pays Global : USD seul ($0.0089)
     * - RDC (dual currency) : USD principal + CDF en complément ($0.0089 ≈ 25 CDF)
     * - Autres pays : devise locale prioritairement (5.40 XOF), USD en fallback
     */
    public static function display(ServicePrice $price, ?Country $country = null): string
    {
        $country = $country ?? (app()->bound('current_country') ? app('current_country') : null);
        
        // Si pas de pays défini, USD par défaut
        if (!$country) {
            return self::formatUsd($price->price_usd);
        }

        // Pays Global : USD seul
        if ($country->is_global) {
            return self::formatUsd($price->price_usd);
        }

        // RDC : dual currency obligatoire
        if ($country->show_dual_currency) {
            return self::formatDual($price, $country);
        }

        // Autres pays : devise locale
        return self::formatLocal($price, $country);
    }

    private static function formatUsd(float $usd): string
    {
        return '$' . number_format($usd, 4);
    }

    private static function formatDual(ServicePrice $price, Country $country): string
    {
        $usdFmt = self::formatUsd($price->price_usd);
        
        // Calcul de la valeur CDF
        $cdfValue = self::resolveLocalValue($price, $country);
        
        if ($cdfValue !== null) {
            $cdfFmt = number_format($cdfValue, 0, ',', ' ') . ' ' . $country->secondary_currency_code;
            return "<strong>{$usdFmt}</strong> <span class='dd-text-muted'>≈ {$cdfFmt}</span>";
        }

        return $usdFmt;
    }

    private static function formatLocal(ServicePrice $price, Country $country): string
    {
        $localValue = self::resolveLocalValue($price, $country);
        
        if ($localValue !== null) {
            return number_format($localValue, 2, ',', ' ') . ' ' . $country->default_currency_code;
        }

        // Fallback USD si aucune conversion possible
        return self::formatUsd($price->price_usd);
    }

    private static function resolveLocalValue(ServicePrice $price, Country $country): ?float
    {
        // Devise visée pour ce pays
        $targetCurrency = $country->show_dual_currency 
            ? $country->secondary_currency_code 
            : $country->default_currency_code;

        // Cas 1 : prix manuel saisi pour cette devise
        if ($price->use_manual_local 
            && $price->price_local !== null 
            && $price->local_currency === $targetCurrency) {
            return (float) $price->price_local;
        }

        // Cas 2 : conversion auto via CurrencyConverter
        return app(CurrencyConverter::class)->convert(
            (float) $price->price_usd, 
            $targetCurrency
        );
    }
}
```

### 7.2 Directive Blade pour usage facile

Dans `app/Providers/AppServiceProvider.php`, méthode `boot()` :

```php
use App\Helpers\PriceFormatter;
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    Blade::directive('price', function ($expression) {
        return "<?php echo \\App\\Helpers\\PriceFormatter::display({$expression}); ?>";
    });
}
```

Usage en Blade :

```blade
@price($servicePrice)
{{-- ou avec un pays explicite : --}}
@price($servicePrice, $someCountry)
```

---

## 8. Vue de test Sprint 1

### 8.1 `resources/views/sprint1/test-page.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Sprint 1 Test — {{ $mode }}</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 900px; margin: 40px auto; padding: 20px; }
        h1 { color: #1F4E79; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; background: #EAF1F8; color: #1F4E79; margin-right: 8px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ddd; }
        .dd-text-muted { color: #6c757d; }
        nav a { margin-right: 12px; }
    </style>
</head>
<body>
    <h1>Sprint 1 — Page de test</h1>
    
    <div>
        <span class="badge">Mode: {{ $mode }}</span>
        <span class="badge">Locale: {{ app()->getLocale() }}</span>
        @if(isset($currentCountry))
            <span class="badge">Country: {{ $currentCountry->flag_emoji }} {{ $currentCountry->name }} ({{ $currentCountry->default_currency_code }})</span>
        @endif
    </div>

    <nav style="margin: 20px 0; padding: 12px; background: #f5f5f5; border-radius: 4px;">
        <strong>Navigation test :</strong><br>
        <a href="/fr">/fr (Global FR)</a>
        <a href="/en">/en (Global EN)</a>
        <a href="/cd/fr">/cd/fr (RDC FR)</a>
        <a href="/cd/en">/cd/en (RDC EN)</a>
        <a href="/cg/fr">/cg/fr (Congo-Brazzaville FR)</a>
        <a href="/ci/fr">/ci/fr (Côte d'Ivoire FR)</a>
        <a href="/ci/en">/ci/en (Côte d'Ivoire EN)</a>
        <br>
        <a href="/_reset-country">↻ Reset country preference cookie</a>
    </nav>

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
            @foreach($services as $service)
                <tr>
                    <td><code>{{ $service->slug }}</code></td>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->short_desc ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Test PriceFormatter</h2>
    <p>Prix de test : SMS @ 0.0089 USD</p>
    @php
        // Mock un ServicePrice pour test
        $mockPrice = new \App\Models\ServicePrice([
            'price_usd' => 0.0089,
            'use_manual_local' => false,
        ]);
    @endphp
    <p>Affichage adaptatif : @price($mockPrice)</p>
</body>
</html>
```

---

## 9. Variables d'environnement à ajouter dans `.env`

```dotenv
# Locales
APP_FALLBACK_LOCALE=fr
APP_AVAILABLE_LOCALES=fr,en

# GeoIP (Torann/GeoIP)
GEOIP_SERVICE=maxmind_database
GEOIP_DATABASE_PATH=/var/lib/maxmind/GeoLite2-Country.mmdb

# Currency conversion
FX_API_URL=https://api.frankfurter.app/latest
FX_CACHE_HOURS=6

# Sales emails par pays
SALES_EMAIL_GLOBAL=sales@dream-digital.info
SALES_EMAIL_CD=sales.cd@dream-digital.info
SALES_EMAIL_CG=sales.cg@dream-digital.info
SALES_EMAIL_CI=sales.ci@dream-digital.info

# Phoenix SMS API (à utiliser plus tard, on peut déjà préparer)
PHOENIX_BASE_URL=https://to-be-confirmed-by-almuqeet.com
PHOENIX_ADMIN_USERNAME=
PHOENIX_ADMIN_PASSWORD=
PHOENIX_DRY_RUN=false
```

---

## 10. Tests minimaux à écrire

### 10.1 `tests/Feature/CountrySwitchingTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Country;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CountrySwitchingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CountrySeeder::class);
        $this->seed(\Database\Seeders\ServiceSeeder::class);
    }

    public function test_global_fr_route_works()
    {
        $response = $this->get('/fr');
        $response->assertStatus(200);
        $this->assertEquals('fr', app()->getLocale());
    }

    public function test_global_en_route_works()
    {
        $response = $this->get('/en');
        $response->assertStatus(200);
        $this->assertEquals('en', app()->getLocale());
    }

    public function test_country_route_with_locale_works()
    {
        $response = $this->get('/cd/fr');
        $response->assertStatus(200);
        $this->assertEquals('fr', app()->getLocale());
    }

    public function test_invalid_country_returns_404()
    {
        $response = $this->get('/xx/fr');
        $response->assertStatus(404);
    }

    public function test_invalid_locale_falls_back()
    {
        $response = $this->get('/cd/zz');
        // Le middleware doit avoir fallback vers la locale par défaut du pays
        $response->assertStatus(200);
    }

    public function test_root_redirects_to_fr_for_unknown_geo()
    {
        $response = $this->get('/');
        $response->assertRedirect('/fr');
    }

    public function test_country_cookie_is_respected()
    {
        $response = $this->withCookie('dd_country_pref', 'cd')->get('/');
        $response->assertRedirect('/cd/fr');
    }

    public function test_reset_country_clears_cookie_and_redirects()
    {
        $response = $this->get('/_reset-country?locale=en');
        $response->assertRedirect('/en');
    }
}
```

### 10.2 `tests/Unit/PriceFormatterTest.php`

```php
<?php

namespace Tests\Unit;

use App\Helpers\PriceFormatter;
use App\Models\Country;
use App\Models\ServicePrice;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriceFormatterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CountrySeeder::class);
        $this->seed(\Database\Seeders\ServiceSeeder::class);
    }

    public function test_global_country_displays_usd_only()
    {
        $global = Country::where('is_global', true)->first();
        $price = new ServicePrice(['price_usd' => 0.0089]);
        
        $output = PriceFormatter::display($price, $global);
        $this->assertStringContainsString('$', $output);
        $this->assertStringContainsString('0.0089', $output);
    }

    public function test_rdc_displays_dual_currency()
    {
        $rdc = Country::where('code', 'cd')->first();
        $price = new ServicePrice(['price_usd' => 0.0089, 'use_manual_local' => false]);
        
        $output = PriceFormatter::display($price, $rdc);
        $this->assertStringContainsString('$', $output);
        $this->assertStringContainsString('CDF', $output);
    }

    public function test_civ_displays_xof()
    {
        $civ = Country::where('code', 'ci')->first();
        $price = new ServicePrice(['price_usd' => 0.0089, 'use_manual_local' => false]);
        
        $output = PriceFormatter::display($price, $civ);
        $this->assertStringContainsString('XOF', $output);
    }

    public function test_manual_local_price_is_used_when_set()
    {
        $civ = Country::where('code', 'ci')->first();
        $price = new ServicePrice([
            'price_usd' => 0.0089,
            'price_local' => 5.40,
            'local_currency' => 'XOF',
            'use_manual_local' => true,
        ]);
        
        $output = PriceFormatter::display($price, $civ);
        $this->assertStringContainsString('5,40', $output);
    }
}
```

---

## 11. Procédure de démarrage Claude Code

### 11.1 Prompt initial à donner à Claude Code

> Bonjour Claude Code. Tu vas m'aider à implémenter le **Sprint 1 — Fondations multi-pays + i18n** pour le site Dream Digital.
> 
> Contexte : le projet est basé sur le template Sneat Pro Laravel 12 qui a déjà été désanonymisé (voir `BRIEF_DD_DESANONYMIZATION.md`). On part maintenant sur la mise en place des fondations techniques.
> 
> **Lis attentivement le fichier `BRIEF_SPRINT_1.md` à la racine**, puis crée un fichier `ANALYZE_SPRINT_1.md` qui contient :
> 
> 1. Ta compréhension du sprint (objectifs, ce qu'on construit, ce qu'on ne construit pas)
> 2. Les questions ou clarifications dont tu as besoin avant de commencer (notamment : couleurs Dream Digital exactes, base URL Phoenix, etc.)
> 3. Ton plan d'attaque en sous-tâches numérotées dans l'ordre d'exécution
> 4. Les risques techniques que tu identifies (par ex : devises non supportées par Frankfurter, GeoIP database pas encore téléchargée)
> 5. La liste des dépendances à installer (`composer require`, `npm install`)
> 
> **N'écris aucun code et ne touche à aucun fichier avant que je valide ton ANALYZE_SPRINT_1.md.**
> 
> Une fois validé, tu attaques sous-tâche par sous-tâche, avec un commit Git après chaque sous-tâche terminée et testée. Pour chaque commit, fais :
> 1. Vérification que les migrations passent : `php artisan migrate:fresh --seed`
> 2. Vérification que les tests passent : `php artisan test`
> 3. Vérification visuelle navigateur : ouvre `/`, `/fr`, `/cd/fr`, `/ci/fr`, etc. et confirme que ça rend correctement
> 
> Tu ne passes à la sous-tâche suivante que si la précédente est verte.

### 11.2 Critères d'acceptance Sprint 1

À la fin du Sprint 1, on doit pouvoir cocher **toutes** ces cases :

- [ ] Migrations `countries`, `services`, `service_prices`, `pages` exécutées sans erreur
- [ ] Seeders peuplent 4 pays (global, cd, cg, ci) et 6 services
- [ ] `php artisan migrate:fresh --seed` est idempotent (peut être relancé sans casser)
- [ ] Route `/` redirige vers `/fr` (utilisateur anonyme sans cookie)
- [ ] Route `/` redirige vers `/cd/fr` (avec cookie `dd_country_pref=cd`)
- [ ] Route `/_reset-country` supprime le cookie et redirige vers `/fr`
- [ ] Route `/fr` rend la page de test avec mode "Global"
- [ ] Route `/cd/fr` rend la page de test avec mode "Country: RDC"
- [ ] Route `/cd/en` rend en EN avec pays RDC
- [ ] Route `/ci/fr` rend en FR avec pays CIV
- [ ] Route `/xx/fr` retourne 404 (pays inconnu)
- [ ] La directive Blade `@price($servicePrice)` fonctionne et formate correctement selon le pays
- [ ] Tests `CountrySwitchingTest` et `PriceFormatterTest` passent à 100%
- [ ] Cache Redis fonctionne (`php artisan cache:clear` puis recharge → la première requête appelle Frankfurter, la deuxième utilise le cache)
- [ ] Aucun warning ni erreur dans `storage/logs/laravel.log`

---

## 12. Notes pour la suite

À la fin du Sprint 1, on enchaînera sur le **Sprint 2** qui s'appuiera sur ces fondations :

- Sprint 2 : Layout vitrine + i18n complète + Mega Menu
- Sprint 3 : Pages Produits + Slider home
- Sprint 4 : Pages Solutions + Développeurs
- Sprint 5 : Module Pricing admin + pages tarifs publiques
- Sprint 6 : Hub Société + pages légales
- Sprint 7 : Auth, blog, finitions
- Sprint 8 : QA, SEO, déploiement

Chaque sprint aura son propre brief Markdown.

---

**FIN DU BRIEF SPRINT 1**

Pour toute question pendant l'exécution, mettre à jour `ANALYZE_SPRINT_1.md` et signaler les blocages au product owner avant de continuer.
