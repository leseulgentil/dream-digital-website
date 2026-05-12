<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConverter
{
    private const FALLBACK_RATES_USD = [
        'CDF' => 2800,
        'XAF' => 600,
        'XOF' => 600,
    ];

    public function convert(float $usd, string $toCurrency): ?float
    {
        $toCurrency = strtoupper($toCurrency);

        if ($toCurrency === 'USD') {
            return $usd;
        }

        $rate = $this->getRate($toCurrency);

        return $rate ? $usd * $rate : null;
    }

    private function getRate(string $currency): ?float
    {
        return Cache::remember("fx_USD_{$currency}", now()->addHours((int) env('FX_CACHE_HOURS', 6)), function () use ($currency) {
            if (isset(self::FALLBACK_RATES_USD[$currency])) {
                return self::FALLBACK_RATES_USD[$currency];
            }

            try {
                $response = Http::timeout(5)->get(env('FX_API_URL', 'https://api.frankfurter.app/latest'), [
                    'from' => 'USD',
                    'to' => $currency,
                ]);

                if ($response->successful() && $response->json("rates.{$currency}")) {
                    return (float) $response->json("rates.{$currency}");
                }
            } catch (\Throwable $e) {
                Log::warning('FX rate fetch failed', [
                    'currency' => $currency,
                    'error' => $e->getMessage(),
                ]);
            }

            return null;
        });
    }

    public function clearCache(): void
    {
        foreach (['CDF', 'XAF', 'XOF', 'EUR', 'KES', 'RWF'] as $currency) {
            Cache::forget("fx_USD_{$currency}");
        }
    }
}
