<?php

namespace Database\Seeders;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use Illuminate\Database\Seeder;

class AiCoreKnowledgeSeeder extends Seeder
{
    /**
     * @var array<string, array<int, array{title: string, category: string, priority: int, content: string}>>
     */
    private const CHUNKS = [
        'fr' => [
            [
                'title' => 'Services Dream Digital',
                'category' => 'faq',
                'priority' => 120,
                'content' => <<<'TEXT'
Dream Digital propose des services telecom et CPaaS pour operateurs, entreprises, plateformes et revendeurs.

Services principaux:
- SMS Wholesale: routes SMS A2P grossiste pour operateurs, agregateurs et plateformes CPaaS, avec SMPP/API, DLR temps reel, supervision qualite par destination et volumes negocies.
- SMS Retail: campagnes SMS, OTP, notifications client, sender IDs, unicode et concatenation pour equipes marketing, fintech, retail et support.
- Voice Wholesale: terminaison voix wholesale pour operateurs, integrateurs et plateformes, qualite carrier-grade, suivi ASR/ACD, tarification par destination et volume.
- Voice Retail: offres voix pour entreprises et centres de contact, numerotation DID, SIP trunking, appels sortants fiables et mise en service accompagnee.
- eSIMZone: plateforme eSIM data pour voyageurs et entreprises, avec forfaits locaux, regionaux ou globaux selon disponibilite, activation QR et catalogue sur esimzone.fr.
- DIALO: plateforme call center omnicanale pour voix, WhatsApp, email, chat et SMS, avec routage, IVR, enregistrement, analytics, agents et superviseurs.

Quand un visiteur demande "Quels services proposez-vous ?", repondre avec cette liste et proposer de preciser le besoin: SMS, voix, eSIM ou centre de contact.
TEXT,
            ],
            [
                'title' => 'Couverture Dream Digital',
                'category' => 'faq',
                'priority' => 110,
                'content' => <<<'TEXT'
Dream Digital couvre plus de 200 destinations pour les services SMS et accompagne des cas d'usage internationaux.

La societe opere depuis l'Afrique francophone avec des points operationnels a Kinshasa, Abidjan et Brazzaville. Une representation commerciale Europe est prevue en France. Les services sont concus pour des besoins locaux, regionaux et globaux selon le produit: SMS, voix, DID/SIP, eSIM et DIALO.

Pour une question comme "Quels pays couvrez-vous ?", repondre que Dream Digital annonce une couverture SMS de plus de 200 destinations et demander le pays, le service et le volume souhaite pour confirmer la disponibilite exacte.
TEXT,
            ],
            [
                'title' => 'Demander un devis Dream Digital',
                'category' => 'support',
                'priority' => 100,
                'content' => <<<'TEXT'
Pour demander un devis Dream Digital, le visiteur peut laisser ses coordonnees ou contacter l'equipe commerciale.

Informations utiles a collecter:
- service concerne: SMS Wholesale, SMS Retail, Voice Wholesale, Voice Retail, eSIMZone ou DIALO;
- pays ou destinations cibles;
- volumes mensuels ou usage prevu;
- besoin technique: API, SMPP, SIP, DID, call center, WhatsApp, email, chat, SMS, eSIM;
- nom, entreprise, email, telephone ou WhatsApp.

Pour une question comme "Comment demander un devis ?", expliquer ces informations et inviter le visiteur a parler a un conseiller Dream Digital.
TEXT,
            ],
        ],
        'en' => [
            [
                'title' => 'Dream Digital services',
                'category' => 'faq',
                'priority' => 120,
                'content' => <<<'TEXT'
Dream Digital provides telecom and CPaaS services for operators, enterprises, platforms and resellers.

Main services:
- SMS Wholesale: wholesale A2P SMS routes for operators, aggregators and CPaaS platforms, with SMPP/API, real-time DLR, route quality monitoring and negotiated volume.
- SMS Retail: SMS campaigns, OTP, customer notifications, sender IDs, unicode and concatenation for marketing, fintech, retail and support teams.
- Voice Wholesale: wholesale voice termination for operators, integrators and platforms, carrier-grade quality, ASR/ACD monitoring, destination and volume pricing.
- Voice Retail: voice offers for enterprises and contact centers, DID numbering, SIP trunking, reliable outbound calls and guided onboarding.
- eSIMZone: data eSIM platform for travelers and companies, with local, regional or global plans depending on availability, QR activation and catalog on esimzone.fr.
- DIALO: omnichannel call center platform for voice, WhatsApp, email, chat and SMS, with routing, IVR, recording, analytics, agents and supervisors.

When a visitor asks "What services do you offer?", answer with this list and invite them to specify the need: SMS, voice, eSIM or contact center.
TEXT,
            ],
            [
                'title' => 'Dream Digital coverage',
                'category' => 'faq',
                'priority' => 110,
                'content' => <<<'TEXT'
Dream Digital covers more than 200 destinations for SMS services and supports international use cases.

The company operates from Francophone Africa with operational points in Kinshasa, Abidjan and Brazzaville. A European commercial representation is planned in France. Services are designed for local, regional and global needs depending on the product: SMS, voice, DID/SIP, eSIM and DIALO.

For a question like "Which countries do you cover?", answer that Dream Digital announces SMS coverage in 200+ destinations and ask for the country, service and expected volume to confirm exact availability.
TEXT,
            ],
            [
                'title' => 'Dream Digital quote process',
                'category' => 'support',
                'priority' => 100,
                'content' => <<<'TEXT'
To request a Dream Digital quote, the visitor can leave contact details or contact the sales team.

Useful information to collect:
- service needed: SMS Wholesale, SMS Retail, Voice Wholesale, Voice Retail, eSIMZone or DIALO;
- target countries or destinations;
- monthly volumes or expected usage;
- technical need: API, SMPP, SIP, DID, call center, WhatsApp, email, chat, SMS, eSIM;
- name, company, email, phone or WhatsApp.

For a question like "How can I request a quote?", explain these details and invite the visitor to talk to a Dream Digital advisor.
TEXT,
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::CHUNKS as $locale => $chunks) {
            $source = AiKnowledgeSource::query()->updateOrCreate([
                'type' => AiKnowledgeSource::TYPE_MANUAL,
                'title' => $locale === 'en' ? 'Dream Digital core knowledge' : 'Base centrale Dream Digital',
                'locale' => $locale,
                'country_code' => 'global',
            ], [
                'status' => 'published',
                'source_url' => "https://dream-digital.info/{$locale}",
                'metadata' => [
                    'seed_key' => 'dream-digital-core',
                    'managed_by' => self::class,
                ],
            ]);

            $activeTitles = [];

            foreach ($chunks as $chunk) {
                $activeTitles[] = $chunk['title'];

                AiKnowledgeChunk::query()->updateOrCreate([
                    'ai_knowledge_source_id' => $source->id,
                    'title' => $chunk['title'],
                ], [
                    'content' => $chunk['content'],
                    'locale' => $locale,
                    'country_code' => 'global',
                    'category' => $chunk['category'],
                    'status' => 'published',
                    'priority' => $chunk['priority'],
                    'expires_at' => null,
                ]);
            }

            $source->chunks()
                ->whereNotIn('title', $activeTitles)
                ->delete();
        }
    }
}
