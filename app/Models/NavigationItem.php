<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    public const TYPE_LINK = 'link';
    public const TYPE_DROPDOWN = 'dropdown';
    public const TYPE_MEGA_SERVICES = 'mega_services';
    public const TYPE_MEGA_DEVELOPERS = 'mega_developers';
    public const TYPE_MEGA_SOLUTIONS = 'mega_solutions';
    public const TYPE_MEGA_COMPANY = 'mega_company';

    public const TYPES = [
        self::TYPE_LINK => 'Lien simple',
        self::TYPE_DROPDOWN => 'Sous-menu',
        self::TYPE_MEGA_SERVICES => 'Mega menu produits',
        self::TYPE_MEGA_DEVELOPERS => 'Mega menu developers',
        self::TYPE_MEGA_SOLUTIONS => 'Mega menu solutions',
        self::TYPE_MEGA_COMPANY => 'Mega menu societe',
    ];

    protected $fillable = [
        'parent_id',
        'menu_area',
        'label_fr',
        'label_en',
        'type',
        'url',
        'opens_new_tab',
        'is_active',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'opens_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('label_fr');
    }

    public function scopeMain(Builder $query): Builder
    {
        return $query->where('menu_area', 'main');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function label(string $locale): string
    {
        if ($locale === 'en' && filled($this->label_en)) {
            return $this->label_en;
        }

        return $this->label_fr;
    }
}
