<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'slug',
        'name_fr',
        'name_en',
        'icon',
        'color_accent',
        'short_desc_fr',
        'short_desc_en',
        'is_active',
        'sort_order',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
