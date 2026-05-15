<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    public const EDITORIAL_STATUSES = [
        self::STATUS_DRAFT => 'Brouillon',
        self::STATUS_IN_REVIEW => 'En revue',
        self::STATUS_PUBLISHED => 'Publie',
        self::STATUS_ARCHIVED => 'Archive',
    ];

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
        'editorial_status',
        'review_notes',
        'updated_by_id',
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

    public function revisions()
    {
        return $this->hasMany(PageRevision::class)->latest();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function editorialStatusLabel(): string
    {
        return self::EDITORIAL_STATUSES[$this->editorial_status] ?? self::EDITORIAL_STATUSES[self::STATUS_DRAFT];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
