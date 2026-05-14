<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Auto-load CMS-ready content configs from config/dream-digital/ into
        // the 'dream-digital.*' namespace so Blade can use
        // config('dream-digital.services.items') etc. without registering each
        // file manually. Sprint 1.5 — Étape 2 architecture decision.
        $configDir = config_path('dream-digital');
        if (is_dir($configDir)) {
            foreach (glob($configDir . '/*.php') as $file) {
                $name = basename($file, '.php');
                $this->mergeConfigFrom($file, "dream-digital.{$name}");
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('price', function ($expression) {
            return "<?php echo \\App\\Helpers\\PriceFormatter::display({$expression}); ?>";
        });
    }
}
