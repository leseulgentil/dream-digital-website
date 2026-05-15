<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatLead extends Model
{
    protected $fillable = [
        'ai_chat_session_id',
        'name',
        'email',
        'phone',
        'whatsapp',
        'company',
        'country_code',
        'need',
        'consent',
    ];

    protected function casts(): array
    {
        return [
            'consent' => 'boolean',
        ];
    }
}
