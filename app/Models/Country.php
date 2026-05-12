<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'code',
        'name_fr',
        'name_en',
        'default_currency_code',
        'secondary_currency_code',
        'show_dual_currency',
        'default_locale',
        'available_locales',
        'phone_prefix',
        'sales_email',
        'sales_phone',
        'office_address',
        'flag_emoji',
        'is_global',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'available_locales' => 'array',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeNonGlobal(Builder $query): Builder
    {
        return $query->where('is_global', false);
    }
}
