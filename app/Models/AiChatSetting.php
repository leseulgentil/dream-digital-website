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
            'model' => (string) config('services.openai.model', 'gpt-5-mini'),
            'max_sources' => 5,
            'max_message_chars' => 1200,
            'provider' => 'openai',
            'fallback_contact_mode' => 'contact_form',
            'greetings' => [
                'fr' => 'Bonjour, comment puis-je aider ?',
                'en' => 'Hello, how can I help?',
            ],
            'system_prompt' => self::defaultSystemPrompt(),
            'display_rules' => [
                'pages' => ['*'],
            ],
        ]);
    }

    public static function defaultSystemPrompt(): string
    {
        return "Tu es l'assistant Dream Digital. Reponds uniquement avec les informations presentes dans la base de connaissances fournie. Si l'information n'est pas disponible, dis que Dream Digital ne peut pas confirmer et propose de contacter l'equipe. Ne cherche pas sur internet. N'invente pas de prix, delais, pays couverts, conditions contractuelles ou coordonnees.";
    }
}
