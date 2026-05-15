<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiKnowledgeWebSource extends Model
{
    public const TYPE_URL = 'url';

    public const TYPE_SITEMAP = 'sitemap';

    public const TYPE_ENDPOINT_JSON = 'endpoint_json';

    public const FREQUENCY_MANUAL = 'manual';

    public const FREQUENCY_DAILY = 'daily';

    public const FREQUENCY_WEEKLY = 'weekly';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    protected $fillable = [
        'title',
        'type',
        'url',
        'locale',
        'country_code',
        'category',
        'frequency',
        'import_status',
        'status',
        'last_synced_at',
        'next_sync_at',
        'last_error',
        'metadata',
        'created_by_id',
    ];

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'next_sync_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function sources(): HasMany
    {
        return $this->hasMany(AiKnowledgeSource::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->whereIn('frequency', [self::FREQUENCY_DAILY, self::FREQUENCY_WEEKLY])
            ->where(function (Builder $query): void {
                $query->whereNull('next_sync_at')
                    ->orWhere('next_sync_at', '<=', now());
            });
    }

    public function nextSyncDate(): ?\Illuminate\Support\Carbon
    {
        return match ($this->frequency) {
            self::FREQUENCY_DAILY => now()->addDay(),
            self::FREQUENCY_WEEKLY => now()->addWeek(),
            default => null,
        };
    }
}
