<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiChatSetting extends Model
{
    protected $fillable = [
        'enabled',
        'model',
        'max_sources',
        'max_message_chars',
        'provider',
        'fallback_contact_mode',
        'greetings',
        'system_prompt',
        'display_rules',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'greetings' => 'array',
            'display_rules' => 'array',
        ];
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'enabled' => false,
            'model' => 'gpt-5.4-mini',
            'max_sources' => 5,
            'max_message_chars' => 1200,
            'provider' => 'openai',
            'fallback_contact_mode' => 'contact_form',
            'system_prompt' => self::defaultSystemPrompt(),
        ]);
    }

    public static function defaultSystemPrompt(): string
    {
        return 'Tu es l assistant IA de Dream Digital. Reponds strictement en francais et utilise uniquement les informations presentes dans la base de connaissances fournie. Ne navigue pas sur Internet, ne consulte aucune source externe et n invente jamais de faits, prix, couvertures, delais ou fonctionnalites. Si la base de connaissances ne contient pas la reponse, dis-le clairement et propose de contacter l equipe Dream Digital via le formulaire de contact.';
    }
}
