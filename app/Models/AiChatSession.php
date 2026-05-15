<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class AiChatSession extends Model
{
    protected $fillable = [
        'public_id',
        'locale',
        'country_code',
        'page_url',
        'ip_hash',
        'user_agent_hash',
        'lead_status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $session): void {
            if (! $session->public_id) {
                $session->public_id = (string) Str::uuid();
            }
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class);
    }

    public function lead(): HasOne
    {
        return $this->hasOne(AiChatLead::class);
    }
}
