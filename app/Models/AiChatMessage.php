<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatMessage extends Model
{
    protected $fillable = [
        'ai_chat_session_id',
        'role',
        'content',
        'source_chunk_ids',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'source_chunk_ids' => 'array',
            'metadata' => 'array',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id');
    }
}
