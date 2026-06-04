<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiKnowledgeChunk extends Model
{
    protected $fillable = [
        'ai_knowledge_source_id',
        'title',
        'content',
        'locale',
        'country_code',
        'category',
        'status',
        'priority',
        'embedding',
        'embedding_model',
        'embedding_hash',
        'embedded_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'embedding' => 'array',
            'embedded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(AiKnowledgeSource::class, 'ai_knowledge_source_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}
