<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AiKnowledgeSource extends Model
{
    public const TYPE_MANUAL = 'manual';

    public const TYPE_MARKDOWN = 'markdown';

    public const TYPE_CSV = 'csv';

    public const TYPE_PDF = 'pdf';

    public const TYPE_WEB_URL = 'web_url';

    public const TYPE_WEB_SITEMAP = 'web_sitemap';

    protected $fillable = [
        'ai_knowledge_web_source_id',
        'type',
        'title',
        'original_filename',
        'stored_path',
        'mime_type',
        'source_url',
        'content_hash',
        'fetched_at',
        'locale',
        'country_code',
        'status',
        'metadata',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'fetched_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (AiKnowledgeSource $source): void {
            if ($source->stored_path) {
                Storage::disk('local')->delete($source->stored_path);
            }
        });
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(AiKnowledgeChunk::class);
    }

    public function webSource(): BelongsTo
    {
        return $this->belongsTo(AiKnowledgeWebSource::class, 'ai_knowledge_web_source_id');
    }
}
