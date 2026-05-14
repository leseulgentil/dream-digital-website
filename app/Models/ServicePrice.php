<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    protected $fillable = [
        'service_id',
        'country_id',
        'destination_country',
        'label_fr',
        'label_en',
        'price_usd',
        'price_local',
        'local_currency',
        'unit',
        'use_manual_local',
        'quality',
        'status_fr',
        'status_en',
        'is_published',
        'updated_by',
    ];

    protected $casts = [
        'price_usd' => 'decimal:6',
        'price_local' => 'decimal:6',
        'use_manual_local' => 'boolean',
        'quality' => 'integer',
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

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
