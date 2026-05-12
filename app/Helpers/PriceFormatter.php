<?php

namespace App\Helpers;

use App\Models\Country;
use App\Models\ServicePrice;
use App\Services\CurrencyConverter;

class PriceFormatter
{
    public static function display(ServicePrice $price, ?Country $country = null): string
    {
        $country = $country ?? (app()->bound('current_country') ? app('current_country') : null);

        if (!$country || $country->is_global) {
            return self::formatUsd((float) $price->price_usd);
        }

        if ($country->show_dual_currency) {
            return self::formatDual($price, $country);
        }

        return self::formatLocal($price, $country);
    }

    private static function formatUsd(float $usd): string
    {
        return '$' . number_format($usd, 4, '.', '');
    }

    private static function formatDual(ServicePrice $price, Country $country): string
    {
        $usd = self::formatUsd((float) $price->price_usd);
        $local = self::resolveLocalValue($price, $country);

        if ($local === null || !$country->secondary_currency_code) {
            return $usd;
        }

        $localFormatted = number_format($local, 0, ',', ' ') . ' ' . $country->secondary_currency_code;

        return "<strong>{$usd}</strong> <span class=\"dd-text-muted\">~ {$localFormatted}</span>";
    }

    private static function formatLocal(ServicePrice $price, Country $country): string
    {
        $local = self::resolveLocalValue($price, $country);

        if ($local === null) {
            return self::formatUsd((float) $price->price_usd);
        }

        return number_format($local, 2, ',', ' ') . ' ' . $country->default_currency_code;
    }

    private static function resolveLocalValue(ServicePrice $price, Country $country): ?float
    {
        $targetCurrency = $country->show_dual_currency
            ? $country->secondary_currency_code
            : $country->default_currency_code;

        if (!$targetCurrency) {
            return null;
        }

        if (
            $price->use_manual_local
            && $price->price_local !== null
            && $price->local_currency === $targetCurrency
        ) {
            return (float) $price->price_local;
        }

        return app(CurrencyConverter::class)->convert((float) $price->price_usd, $targetCurrency);
    }
}
