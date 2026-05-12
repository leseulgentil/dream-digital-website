<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'section',
        'country_id',
        'locale',
        'title',
        'meta_description',
        'meta_image_path',
        'content_blocks',
        'is_published',
        'published_at',
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

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
