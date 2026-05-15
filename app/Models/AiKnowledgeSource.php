<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class AiKnowledgeSource extends Model
{
    public const TYPE_MANUAL = 'manual';

    public const TYPE_MARKDOWN = 'markdown';

    public const TYPE_CSV = 'csv';

    public const TYPE_PDF = 'pdf';

    protected $fillable = [
        'type',
        'title',
        'original_filename',
        'stored_path',
        'mime_type',
        'locale',
        'country_code',
        'status',
        'metadata',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
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
}
