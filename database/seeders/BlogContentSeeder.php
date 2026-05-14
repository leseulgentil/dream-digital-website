<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BlogContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->articles() as $article) {
            foreach (['fr', 'en'] as $locale) {
                $content = $article[$locale];

                Page::updateOrCreate(
                    [
                        'slug' => $article['slug'],
                        'section' => 'blog',
                        'country_id' => null,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $content['title'],
                        'meta_description' => $content['meta_description'],
                        'meta_image_path' => $article['image']['url'],
                        'content_blocks' => [
                            'seo_title' => $content['seo_title'],
                            'eyebrow' => $content['eyebrow'],
                            'lead' => $content['lead'],
                            'author' => 'Dream Digital',
                            'reading_time' => $content['reading_time'],
                            'image_alt' => $content['image_alt'],
                            'image_credit' => $article['image']['credit'],
                            'image_source_url' => $article['image']['source_url'],
                            'tags' => $content['tags'],
                            'seo_focus_keywords' => $content['tags'],
                            'content_status' => 'final-draft',
                            'faq' => $this->faqFor($content, $locale),
                            'sections' => $this->enrichSections($content['sections'], $content, $locale),
                        ],
                        'is_published' => true,
                        'published_at' => Carbon::parse($article['published_at']),
                    ],
                );
            }
        }

        $this->command?->info('Blog pages seeded: ' . Page::where('section', 'blog')->count() . ' entries.');
    }

    private function articles(): array
    {
        $images = [
            'network' => [
                'url' => 'https://images.unsplash.com/photo-1752742111841-f490c48aa668?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=70&w=1600',
                'source_url' => 'https://unsplash.com/photos/servers-illuminate-a-futuristic-cityscape-with-a-data-center-ISP9CdRYS28',
                'credit' => 'Photo Unsplash / Markus Stickling',
            ],
            'fiber' => [
                'url' => 'https://images.unsplash.com/photo-1744868562210-fffb7fa882d9?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=70&w=1600',
                'source_url' => 'https://unsplash.com/photos/yellow-and-green-cables-are-neatly-connected-yhJVLxcquEY',
                'credit' => 'Photo Unsplash / Albert Stoynov',
            ],
            'developers' => [
                'url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=70&w=1600',
                'source_url' => 'https://unsplash.com/s/photos/developers',
                'credit' => 'Photo Unsplash / Annie Spratt',
            ],
            'code' => [
                'url' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=70&w=1600',
                'source_url' => 'https://unsplash.com/s/photos/developers',
                'credit' => 'Photo Unsplash / Ilya Pavlov',
            ],
            'esim' => [
                'url' => 'https://images.unsplash.com/photo-1759978227971-70ae29f87859?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=70&w=1600',
                'source_url' => 'https://unsplash.com/photos/smartphone-displaying-esim-app-with-travel-deals-de1lTVrsAbc',
                'credit' => 'Photo Unsplash / Airalo',
            ],
            'contact_center' => [
                'url' => 'https://images.unsplash.com/photo-1560264280-88b68371db39?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=70&w=1600',
                'source_url' => 'https://unsplash.com/s/photos/call-center-team',
                'credit' => 'Photo Unsplash / Arlington Research',
            ],
            'payment' => [
                'url' => 'https://images.unsplash.com/photo-1743696398209-6b693d480862?auto=format&fit=crop&fm=jpg&ixlib=rb-4.1.0&q=70&w=1600',
                'source_url' => 'https://unsplash.com/photos/mobile-payment-is-being-made-with-a-smartphone-cUxf_E0824k',
                'credit' => 'Photo Unsplash / Vagaro',
            ],
        ];

        return [
            [
                'slug' => 'sms-a2p-otp-afrique-francophone',
                'published_at' => '2026-05-01 09:00:00',
                'image' => $images['network'],
                'fr' => [
                    'eyebrow' => 'SMS A2P',
                    'title' => 'SMS A2P et OTP en Afrique francophone : fiabiliser la delivrabilite',
                    'seo_title' => 'SMS A2P et OTP Afrique francophone - delivrabilite CPaaS',
                    'meta_description' => 'Guide SEO Dream Digital sur la delivrabilite SMS A2P, les OTP bancaires, les routes premium et le monitoring DLR en Afrique francophone.',
                    'lead' => 'Un OTP utile est un OTP recu vite, sur la bonne route et avec un statut de livraison exploitable par vos equipes.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Infrastructure reseau pour SMS A2P et OTP',
                    'tags' => ['SMS A2P', 'OTP', 'CPaaS', 'Afrique francophone'],
                    'sections' => [
                        ['heading' => 'Pourquoi l OTP demande une route premium', 'body' => "Les parcours banque, fintech, e-commerce et SaaS ne tolerent pas les SMS incertains. Un code de verification arrive trop tard cree un abandon, une relance support et parfois une transaction perdue.\n\nLa route premium n est pas seulement une question de prix. Elle combine couverture locale, filtrage du sender ID, supervision des erreurs operateur et capacite a basculer rapidement si une destination se degrade."],
                        ['heading' => 'Les indicateurs a suivre', 'body' => "Les equipes doivent suivre le taux de livraison, la latence, les erreurs permanentes, les erreurs temporaires et les statuts DLR. Ces mesures donnent une lecture utile de la qualite reelle, au lieu de se limiter au volume envoye.\n\nUn bon tableau de bord separe les usages transactionnels, marketing et alerting. Les OTP exigent une politique plus stricte que les campagnes relationnelles."],
                        ['heading' => 'L approche Dream Digital', 'body' => "Dream Digital structure les routes SMS A2P par corridor et par usage. Les flux critiques passent en priorite sur des partenaires suivis, avec remontes DLR et analyse des incidents.\n\nCette approche aide les clients a stabiliser leurs conversions sans multiplier les integrations operateur dans leurs propres systemes."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'A2P SMS',
                    'title' => 'A2P SMS and OTP in Francophone Africa: improving deliverability',
                    'seo_title' => 'A2P SMS and OTP in Francophone Africa - CPaaS deliverability',
                    'meta_description' => 'Dream Digital SEO guide about A2P SMS deliverability, banking OTP, premium routes and DLR monitoring across Francophone Africa.',
                    'lead' => 'A useful OTP is received quickly, travels through the right route and gives your teams a usable delivery status.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Network infrastructure for A2P SMS and OTP',
                    'tags' => ['A2P SMS', 'OTP', 'CPaaS', 'Francophone Africa'],
                    'sections' => [
                        ['heading' => 'Why OTP requires a premium route', 'body' => "Banking, fintech, e-commerce and SaaS journeys cannot rely on uncertain SMS delivery. A verification code that arrives late creates drop-off, support work and sometimes a lost transaction.\n\nA premium route is not only a pricing decision. It combines local coverage, sender ID control, operator error monitoring and the ability to switch quickly when one destination degrades."],
                        ['heading' => 'Metrics worth tracking', 'body' => "Teams should monitor delivery rate, latency, permanent errors, temporary errors and DLR statuses. These measures show actual quality instead of only reporting submitted volume.\n\nA useful dashboard separates transactional, marketing and alerting traffic. OTP flows need stricter rules than relationship campaigns."],
                        ['heading' => 'Dream Digital approach', 'body' => "Dream Digital structures A2P SMS routes by corridor and use case. Critical traffic is prioritized through monitored partners, with DLR feedback and incident analysis.\n\nThis helps clients stabilize conversions without multiplying operator integrations inside their own systems."],
                    ],
                ],
            ],
            [
                'slug' => 'voice-wholesale-qualite-routes',
                'published_at' => '2026-05-02 09:00:00',
                'image' => $images['fiber'],
                'fr' => [
                    'eyebrow' => 'Voice Wholesale',
                    'title' => 'Voice Wholesale : mesurer la qualite avant de negocier le prix',
                    'seo_title' => 'Voice Wholesale - qualite ASR ACD routes internationales',
                    'meta_description' => 'Comprendre ASR, ACD, PDD, codecs et supervision pour acheter de la voix wholesale sans sacrifier la qualite client.',
                    'lead' => 'Dans la voix internationale, le prix minute ne suffit pas. La qualite se voit dans les appels qui aboutissent et restent clairs.',
                    'reading_time' => '6 min',
                    'image_alt' => 'Cables fibre optique pour routes voix internationales',
                    'tags' => ['Voice Wholesale', 'ASR', 'ACD', 'SIP'],
                    'sections' => [
                        ['heading' => 'Les KPI voix a surveiller', 'body' => "ASR, ACD et PDD donnent une premiere lecture de la performance. Un ASR bas peut signaler une route instable, une mauvaise categorisation destination ou une saturation partenaire.\n\nL ACD aide a detecter les appels qui coupent trop vite. Le PDD indique le delai avant sonnerie, un detail tres sensible pour les centres d appels et les services commerciaux."],
                        ['heading' => 'Codecs et compatibilite', 'body' => "La qualite audio depend aussi du codec, du transcodage et de la stabilite IP. G.711, G.729 ou OPUS n ont pas les memes compromis entre bande passante et clarte.\n\nUne bonne architecture SIP documente les codecs acceptes, les IP autorisees, le failover et les regles de routage par destination."],
                        ['heading' => 'Negocier avec des donnees', 'body' => "Dream Digital aide a comparer les routes par destination, volume et niveau de qualite. Le but est de construire un mix de routes premium et economiques sans perdre la maitrise du service.\n\nLa conversation prix devient alors une conversation SLA, risque et experience utilisateur."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'Voice Wholesale',
                    'title' => 'Voice Wholesale: measure route quality before negotiating price',
                    'seo_title' => 'Voice Wholesale - ASR ACD international route quality',
                    'meta_description' => 'Understand ASR, ACD, PDD, codecs and monitoring to buy wholesale voice without sacrificing customer experience.',
                    'lead' => 'In international voice, the minute price is not enough. Quality shows in calls that connect and remain clear.',
                    'reading_time' => '6 min',
                    'image_alt' => 'Fiber optic cables for international voice routes',
                    'tags' => ['Voice Wholesale', 'ASR', 'ACD', 'SIP'],
                    'sections' => [
                        ['heading' => 'Voice KPIs to monitor', 'body' => "ASR, ACD and PDD provide the first read on performance. A low ASR can reveal an unstable route, poor destination classification or partner saturation.\n\nACD helps detect calls that drop too quickly. PDD shows the delay before ringing, a detail that matters for contact centers and sales teams."],
                        ['heading' => 'Codecs and compatibility', 'body' => "Audio quality also depends on codec choice, transcoding and IP stability. G.711, G.729 and OPUS do not make the same trade-offs between bandwidth and clarity.\n\nA reliable SIP architecture documents accepted codecs, authorized IPs, failover and routing rules by destination."],
                        ['heading' => 'Negotiate with data', 'body' => "Dream Digital helps compare routes by destination, volume and quality level. The goal is to build a mix of premium and cost-efficient routes without losing control of service.\n\nPrice discussions then become discussions about SLA, risk and user experience."],
                    ],
                ],
            ],
            [
                'slug' => 'did-numbers-presence-locale',
                'published_at' => '2026-05-03 09:00:00',
                'image' => $images['contact_center'],
                'fr' => [
                    'eyebrow' => 'DID Numbers',
                    'title' => 'DID Numbers : creer une presence locale sans ouvrir un bureau',
                    'seo_title' => 'DID Numbers entreprise - numeros locaux et presence internationale',
                    'meta_description' => 'Comment les numeros DID aident les entreprises a recevoir des appels locaux, router le support et rassurer les clients internationaux.',
                    'lead' => 'Un numero local peut transformer la perception d une marque, surtout quand le support doit etre proche du client.',
                    'reading_time' => '4 min',
                    'image_alt' => 'Equipe support utilisant des numeros DID',
                    'tags' => ['DID Numbers', 'Support client', 'VoIP'],
                    'sections' => [
                        ['heading' => 'Le role d un numero local', 'body' => "Un DID donne a une entreprise un point d entree local, meme si les agents travaillent ailleurs. Il reduit la friction pour les clients et facilite les campagnes par pays.\n\nPour une equipe commerciale, disposer de numeros locaux aide aussi a distinguer les marches, les files d appels et les horaires de traitement."],
                        ['heading' => 'Routage et supervision', 'body' => "Un numero DID doit etre connecte a une logique claire: IVR, horaires, file, enregistrement, fallback et reporting. Sans ces regles, le numero existe mais l experience reste fragile.\n\nLes indicateurs essentiels sont le taux de decrochage, les appels manques, la duree moyenne et les heures de saturation."],
                        ['heading' => 'Une brique pour le CPaaS', 'body' => "Integres avec SIP Trunking et centre de contact, les DID deviennent une brique de presence internationale. Dream Digital les positionne comme un outil de confiance, pas comme un simple inventaire de numeros.\n\nL objectif est de connecter la proximite commerciale a une infrastructure programmable."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'DID Numbers',
                    'title' => 'DID Numbers: create a local presence without opening an office',
                    'seo_title' => 'DID Numbers for business - local numbers and international presence',
                    'meta_description' => 'How DID numbers help businesses receive local calls, route support and reassure international customers.',
                    'lead' => 'A local number can change how a brand is perceived, especially when support must feel close to the customer.',
                    'reading_time' => '4 min',
                    'image_alt' => 'Support team using DID numbers',
                    'tags' => ['DID Numbers', 'Customer support', 'VoIP'],
                    'sections' => [
                        ['heading' => 'The role of a local number', 'body' => "A DID gives a company a local entry point even when agents work elsewhere. It reduces friction for customers and makes country-level campaigns easier.\n\nFor a sales team, local numbers also help separate markets, call queues and business hours."],
                        ['heading' => 'Routing and monitoring', 'body' => "A DID number needs a clear logic: IVR, opening hours, queue, recording, fallback and reporting. Without those rules, the number exists but the experience remains fragile.\n\nKey indicators include answer rate, missed calls, average duration and saturation periods."],
                        ['heading' => 'A CPaaS building block', 'body' => "Combined with SIP Trunking and contact center tools, DID numbers become a building block for international presence. Dream Digital treats them as a trust tool, not only as number inventory.\n\nThe objective is to connect commercial proximity with programmable infrastructure."],
                    ],
                ],
            ],
            [
                'slug' => 'sip-trunking-entreprises-multisites',
                'published_at' => '2026-05-04 09:00:00',
                'image' => $images['fiber'],
                'fr' => [
                    'eyebrow' => 'SIP Trunking',
                    'title' => 'SIP Trunking pour entreprises multisites : les points a verrouiller',
                    'seo_title' => 'SIP Trunking multisite - securite, codecs et failover',
                    'meta_description' => 'Guide SIP Trunking pour entreprises multisites : securite IP, codecs, capacity planning, failover et supervision des appels.',
                    'lead' => 'Le SIP Trunking simplifie la voix d entreprise, mais seulement si la securite, le routage et le capacity planning sont poses des le depart.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Infrastructure fibre pour SIP Trunking multisite',
                    'tags' => ['SIP Trunking', 'VoIP', 'Entreprise'],
                    'sections' => [
                        ['heading' => 'Securiser les interconnexions', 'body' => "La premiere base consiste a limiter les IP autorisees, durcir l authentification et documenter les plages de trafic attendues. Une ouverture trop large expose l entreprise a la fraude voix.\n\nLes journaux d appels doivent etre conserves et lisibles pour diagnostiquer les tentatives suspectes, les pics inhabituels et les erreurs de routage."],
                        ['heading' => 'Prevoir la capacite', 'body' => "Un site support, un standard et une equipe commerciale n ont pas les memes profils d appels. Le capacity planning doit separer les heures de pointe, les campagnes sortantes et les besoins d urgence.\n\nUne bonne regle consiste a definir les canaux par site puis a mettre en place un seuil d alerte avant saturation."],
                        ['heading' => 'Failover et continuite', 'body' => "Le failover SIP doit etre teste, pas seulement dessine. Une route secondaire doit accepter les codecs, les numeros et les regles de securite prevues.\n\nDream Digital accompagne ce travail pour transformer le trunk SIP en service mesurable et exploitable par les equipes IT."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'SIP Trunking',
                    'title' => 'SIP Trunking for multi-site companies: what to lock down',
                    'seo_title' => 'Multi-site SIP Trunking - security, codecs and failover',
                    'meta_description' => 'SIP Trunking guide for multi-site companies: IP security, codecs, capacity planning, failover and call monitoring.',
                    'lead' => 'SIP Trunking simplifies enterprise voice only when security, routing and capacity planning are set from the start.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Fiber infrastructure for multi-site SIP Trunking',
                    'tags' => ['SIP Trunking', 'VoIP', 'Enterprise'],
                    'sections' => [
                        ['heading' => 'Secure interconnections', 'body' => "The first baseline is to restrict authorized IPs, harden authentication and document expected traffic ranges. A trunk that is too open exposes the company to voice fraud.\n\nCall logs should be retained and readable to diagnose suspicious attempts, unusual peaks and routing errors."],
                        ['heading' => 'Plan capacity', 'body' => "A support site, switchboard and sales team do not have the same call profile. Capacity planning should separate peak hours, outbound campaigns and emergency needs.\n\nA useful rule is to define channels by site and create an alert threshold before saturation."],
                        ['heading' => 'Failover and continuity', 'body' => "SIP failover must be tested, not only documented. A secondary route should support the expected codecs, numbers and security rules.\n\nDream Digital supports this work so a SIP trunk becomes a measurable service that IT teams can operate."],
                    ],
                ],
            ],
            [
                'slug' => 'contact-center-omnichannel-dialo',
                'published_at' => '2026-05-05 09:00:00',
                'image' => $images['contact_center'],
                'fr' => [
                    'eyebrow' => 'Dialo Contact Center',
                    'title' => 'Centre de contact omnicanal : pourquoi voix, SMS et WhatsApp doivent se parler',
                    'seo_title' => 'Centre de contact omnicanal - voix SMS WhatsApp et analytics',
                    'meta_description' => 'Bonnes pratiques pour un centre de contact omnicanal : routage intelligent, voix, SMS, WhatsApp, supervision et analytics.',
                    'lead' => 'Un centre de contact moderne doit suivre la conversation client, pas seulement le canal utilise a un instant donne.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Plateau de centre de contact omnicanal',
                    'tags' => ['Contact center', 'Omnicanal', 'Dialo'],
                    'sections' => [
                        ['heading' => 'Unifier le contexte client', 'body' => "Quand un client commence par WhatsApp puis appelle, l agent doit retrouver le contexte. Sinon l entreprise paie deux interactions pour resoudre une seule demande.\n\nL omnicanal n est pas une accumulation de canaux. C est une maniere de garder l historique, le statut et la priorite au meme endroit."],
                        ['heading' => 'Routage et priorites', 'body' => "Les files doivent etre pensees par competence, urgence et valeur metier. Un incident paiement n a pas la meme priorite qu une demande generale.\n\nLa supervision doit montrer les files qui saturent, les agents disponibles et les conversations qui risquent de sortir du SLA."],
                        ['heading' => 'La couche telecom compte', 'body' => "Le logiciel de centre de contact depend de routes voix fiables, de numeros entrants, de SMS transactionnels et parfois d API messaging. Dream Digital relie ces briques pour eviter les silos.\n\nLe resultat attendu est une experience client plus continue et une equipe operationnelle plus lisible."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'Dialo Contact Center',
                    'title' => 'Omnichannel contact center: why voice, SMS and WhatsApp must connect',
                    'seo_title' => 'Omnichannel contact center - voice SMS WhatsApp and analytics',
                    'meta_description' => 'Best practices for an omnichannel contact center: smart routing, voice, SMS, WhatsApp, supervision and analytics.',
                    'lead' => 'A modern contact center should follow the customer conversation, not only the channel used at one moment.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Omnichannel contact center floor',
                    'tags' => ['Contact center', 'Omnichannel', 'Dialo'],
                    'sections' => [
                        ['heading' => 'Unify customer context', 'body' => "When a customer starts on WhatsApp and then calls, the agent should recover the context. Otherwise the company pays for two interactions to solve one request.\n\nOmnichannel is not a pile of channels. It is a way to keep history, status and priority in one place."],
                        ['heading' => 'Routing and priorities', 'body' => "Queues should be designed by skill, urgency and business value. A payment incident does not have the same priority as a general question.\n\nSupervision should show saturated queues, available agents and conversations that risk missing SLA."],
                        ['heading' => 'The telecom layer matters', 'body' => "Contact center software depends on reliable voice routes, inbound numbers, transactional SMS and sometimes messaging APIs. Dream Digital connects these building blocks to avoid silos.\n\nThe expected result is a smoother customer experience and a clearer operating model for teams."],
                    ],
                ],
            ],
            [
                'slug' => 'esim-connectivite-voyageurs-equipes',
                'published_at' => '2026-05-06 09:00:00',
                'image' => $images['esim'],
                'fr' => [
                    'eyebrow' => 'eSIM Zone',
                    'title' => 'eSIM pour voyageurs et equipes terrain : reduire la friction de connectivite',
                    'seo_title' => 'eSIM entreprise - connectivite voyageurs et equipes terrain',
                    'meta_description' => 'Comment l eSIM simplifie la connectivite internationale des voyageurs, commerciaux, techniciens terrain et equipes hybrides.',
                    'lead' => 'L eSIM transforme la connectivite mobile en un service activable avant le depart, mesurable et plus simple a administrer.',
                    'reading_time' => '4 min',
                    'image_alt' => 'Smartphone affichant une application eSIM',
                    'tags' => ['eSIM', 'Mobile data', 'Voyage'],
                    'sections' => [
                        ['heading' => 'Pourquoi les equipes choisissent l eSIM', 'body' => "Les cartes SIM physiques creent de la logistique: achat local, distribution, perte, changement de forfait et support. L eSIM reduit ces points de friction en rendant le profil mobile activable a distance.\n\nPour les commerciaux, consultants et techniciens, cela signifie moins de temps perdu a chercher une connexion fiable."],
                        ['heading' => 'Cas d usage B2B', 'body' => "Les entreprises utilisent l eSIM pour les voyages, les missions terrain, les appareils de test et les operations temporaires. Le besoin commun est de controler le cout et d eviter les mauvaises surprises de roaming.\n\nUn portail clair permet de suivre les pays, les forfaits et les dates d activation."],
                        ['heading' => 'Vers une connectivite programmable', 'body' => "Avec eSIM Zone, Dream Digital prepare une experience orientee service: choix du pays, activation, support et suivi. L objectif est de rendre la data mobile aussi facile a piloter qu une API.\n\nC est une extension naturelle du catalogue CPaaS et telecom."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'eSIM Zone',
                    'title' => 'eSIM for travelers and field teams: reducing connectivity friction',
                    'seo_title' => 'Business eSIM - connectivity for travelers and field teams',
                    'meta_description' => 'How eSIM simplifies international connectivity for travelers, sales teams, field technicians and hybrid workers.',
                    'lead' => 'eSIM turns mobile connectivity into a service that can be activated before travel, measured and administered more easily.',
                    'reading_time' => '4 min',
                    'image_alt' => 'Smartphone showing an eSIM application',
                    'tags' => ['eSIM', 'Mobile data', 'Travel'],
                    'sections' => [
                        ['heading' => 'Why teams choose eSIM', 'body' => "Physical SIM cards create logistics: local purchase, distribution, loss, plan changes and support. eSIM reduces these friction points by making the mobile profile remotely activatable.\n\nFor salespeople, consultants and technicians, this means less time lost searching for reliable connectivity."],
                        ['heading' => 'B2B use cases', 'body' => "Companies use eSIM for travel, field missions, test devices and temporary operations. The common need is to control cost and avoid roaming surprises.\n\nA clear portal helps track countries, packages and activation dates."],
                        ['heading' => 'Toward programmable connectivity', 'body' => "With eSIM Zone, Dream Digital prepares a service-oriented experience: country choice, activation, support and monitoring. The objective is to make mobile data as easy to operate as an API.\n\nIt is a natural extension of the CPaaS and telecom catalogue."],
                    ],
                ],
            ],
            [
                'slug' => 'webhooks-dlr-monitoring-temps-reel',
                'published_at' => '2026-05-07 09:00:00',
                'image' => $images['code'],
                'fr' => [
                    'eyebrow' => 'API Telecom',
                    'title' => 'Webhooks DLR : transformer les statuts telecom en decisions produit',
                    'seo_title' => 'Webhooks DLR SMS - monitoring temps reel API telecom',
                    'meta_description' => 'Comprendre les webhooks DLR SMS, les statuts de livraison, les retries et le monitoring temps reel pour les produits CPaaS.',
                    'lead' => 'Un webhook DLR bien traite permet de savoir quoi relancer, quoi corriger et quoi expliquer au client final.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Code API pour webhooks DLR SMS',
                    'tags' => ['Webhooks', 'DLR', 'API SMS'],
                    'sections' => [
                        ['heading' => 'Le DLR comme signal produit', 'body' => "Le Delivery Receipt n est pas seulement un detail technique. Il signale si un message est livre, rejete, expire ou en attente. Pour un produit, ce signal peut declencher une relance, une notification alternative ou une alerte support.\n\nSans DLR exploitable, les equipes naviguent a l aveugle apres l envoi."],
                        ['heading' => 'Retries et idempotence', 'body' => "Un webhook doit accepter les retries sans creer de doublons. L idempotence repose sur un identifiant message stable, une signature et une logique de mise a jour prudente.\n\nIl faut aussi distinguer les erreurs temporaires des erreurs finales, afin de ne pas relancer inutilement."],
                        ['heading' => 'Monitoring en temps reel', 'body' => "Dream Digital pousse les webhooks DLR comme une brique d observabilite. Les statuts nourrissent le support, la BI et les alertes operationnelles.\n\nCette boucle transforme l API SMS en un service mesurable et pilotable."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'Telecom API',
                    'title' => 'DLR webhooks: turning telecom statuses into product decisions',
                    'seo_title' => 'SMS DLR webhooks - real-time telecom API monitoring',
                    'meta_description' => 'Understand SMS DLR webhooks, delivery statuses, retries and real-time monitoring for CPaaS products.',
                    'lead' => 'A well-handled DLR webhook tells teams what to retry, what to fix and what to explain to end users.',
                    'reading_time' => '5 min',
                    'image_alt' => 'API code for SMS DLR webhooks',
                    'tags' => ['Webhooks', 'DLR', 'SMS API'],
                    'sections' => [
                        ['heading' => 'DLR as a product signal', 'body' => "A Delivery Receipt is not only a technical detail. It shows whether a message is delivered, rejected, expired or pending. For a product, this signal can trigger a retry, an alternative notification or a support alert.\n\nWithout usable DLR, teams are blind after submission."],
                        ['heading' => 'Retries and idempotency', 'body' => "A webhook must accept retries without creating duplicates. Idempotency relies on a stable message identifier, a signature and cautious update logic.\n\nTeams also need to separate temporary errors from final errors to avoid unnecessary retries."],
                        ['heading' => 'Real-time monitoring', 'body' => "Dream Digital treats DLR webhooks as an observability block. Statuses feed support, BI and operational alerts.\n\nThis loop turns the SMS API into a measurable and controllable service."],
                    ],
                ],
            ],
            [
                'slug' => 'pricing-telecom-multi-pays-corridors',
                'published_at' => '2026-05-08 09:00:00',
                'image' => $images['payment'],
                'fr' => [
                    'eyebrow' => 'Pricing telecom',
                    'title' => 'Pricing telecom multi-pays : pourquoi le corridor change tout',
                    'seo_title' => 'Pricing telecom multi-pays - corridors SMS voix et eSIM',
                    'meta_description' => 'Pourquoi les tarifs SMS, voix, DID, SIP et eSIM varient selon les corridors, la qualite, les volumes et les SLA.',
                    'lead' => 'Un prix telecom n est jamais abstrait. Il depend du pays, de la destination, du volume, de la qualite et du risque operationnel.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Paiement mobile illustrant le pricing telecom',
                    'tags' => ['Pricing', 'Corridors', 'Telecom'],
                    'sections' => [
                        ['heading' => 'Le corridor comme unite de pricing', 'body' => "Un corridor decrit une origine, une destination et un canal. Envoyer un SMS local, terminer un appel international ou fournir une eSIM n utilise pas les memes partenaires ni les memes contraintes.\n\nC est pourquoi un tarif moyen global peut masquer les vrais couts et creer de mauvaises decisions commerciales."],
                        ['heading' => 'Qualite et volume', 'body' => "Les volumes aident a negocier, mais la qualite reste decisive. Une route moins chere peut augmenter les echecs, les tickets support et les abandons.\n\nLe bon pricing compare donc le cout complet: prix unitaire, taux d echec, latence, support et garantie attendue."],
                        ['heading' => 'Publier sans figer', 'body' => "Dream Digital construit un module de pricing publie par service et par pays. Les tarifs indicatifs peuvent etre visibles, tout en laissant les corridors premium negocies selon le besoin client.\n\nCette approche rend le site plus transparent sans enfermer l equipe commerciale."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'Telecom pricing',
                    'title' => 'Multi-country telecom pricing: why corridors change everything',
                    'seo_title' => 'Multi-country telecom pricing - SMS voice and eSIM corridors',
                    'meta_description' => 'Why SMS, voice, DID, SIP and eSIM prices vary by corridor, quality, volume and SLA expectations.',
                    'lead' => 'A telecom price is never abstract. It depends on country, destination, volume, quality and operational risk.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Mobile payment illustrating telecom pricing',
                    'tags' => ['Pricing', 'Corridors', 'Telecom'],
                    'sections' => [
                        ['heading' => 'Corridor as the pricing unit', 'body' => "A corridor describes an origin, a destination and a channel. Sending a local SMS, terminating an international call or providing an eSIM does not rely on the same partners or constraints.\n\nA global average price can hide real costs and create poor commercial decisions."],
                        ['heading' => 'Quality and volume', 'body' => "Volumes help negotiation, but quality remains decisive. A cheaper route can increase failures, support tickets and user drop-off.\n\nGood pricing compares total cost: unit price, failure rate, latency, support and expected guarantee."],
                        ['heading' => 'Publish without freezing', 'body' => "Dream Digital builds a pricing module published by service and country. Indicative prices can be visible while premium corridors remain negotiable according to customer needs.\n\nThis makes the website more transparent without locking the sales team."],
                    ],
                ],
            ],
            [
                'slug' => 'cpaas-fintech-banques-otp',
                'published_at' => '2026-05-09 09:00:00',
                'image' => $images['payment'],
                'fr' => [
                    'eyebrow' => 'Fintech',
                    'title' => 'CPaaS pour fintechs et banques : securiser les parcours OTP',
                    'seo_title' => 'CPaaS fintech banque - OTP SMS API et securite',
                    'meta_description' => 'Comment une plateforme CPaaS aide les fintechs et banques a securiser OTP, alertes transactionnelles et notifications client.',
                    'lead' => 'Les institutions financieres ont besoin de canaux rapides, auditables et resilients pour proteger l experience client.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Paiement mobile pour article CPaaS fintech',
                    'tags' => ['CPaaS', 'Fintech', 'Banque', 'OTP'],
                    'sections' => [
                        ['heading' => 'Securite et experience', 'body' => "Un parcours OTP doit proteger sans bloquer inutilement. Trop de friction fait perdre des clients, trop peu de controle augmente le risque.\n\nLe CPaaS aide a combiner SMS, voix, webhooks et monitoring pour trouver cet equilibre."],
                        ['heading' => 'Audit et tracabilite', 'body' => "Les banques doivent expliquer ce qui a ete envoye, quand, vers quel numero et avec quel statut. Les logs et DLR deviennent donc des preuves operationnelles.\n\nUne plateforme bien concue facilite les exports, les alertes et le diagnostic en cas de reclamation."],
                        ['heading' => 'Resilience multi-canal', 'body' => "Si une route SMS se degrade, une strategie alternative peut passer par un autre corridor, un appel vocal ou une notification applicative. La resilience doit etre prevue avant l incident.\n\nDream Digital positionne le CPaaS comme une couche de continuite pour les services financiers."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'Fintech',
                    'title' => 'CPaaS for fintechs and banks: securing OTP journeys',
                    'seo_title' => 'CPaaS for fintech and banking - OTP SMS API and security',
                    'meta_description' => 'How a CPaaS platform helps fintechs and banks secure OTP, transactional alerts and customer notifications.',
                    'lead' => 'Financial institutions need fast, auditable and resilient channels to protect customer experience.',
                    'reading_time' => '5 min',
                    'image_alt' => 'Mobile payment for CPaaS fintech article',
                    'tags' => ['CPaaS', 'Fintech', 'Banking', 'OTP'],
                    'sections' => [
                        ['heading' => 'Security and experience', 'body' => "An OTP journey must protect without blocking unnecessarily. Too much friction loses customers, too little control increases risk.\n\nCPaaS helps combine SMS, voice, webhooks and monitoring to find that balance."],
                        ['heading' => 'Audit and traceability', 'body' => "Banks must explain what was sent, when, to which number and with which status. Logs and DLR therefore become operational evidence.\n\nA well-designed platform makes exports, alerts and diagnosis easier in case of customer claims."],
                        ['heading' => 'Multi-channel resilience', 'body' => "If an SMS route degrades, an alternative strategy can use another corridor, a voice call or an app notification. Resilience should be designed before the incident.\n\nDream Digital positions CPaaS as a continuity layer for financial services."],
                    ],
                ],
            ],
            [
                'slug' => 'migration-legacy-telecom-api-moderne',
                'published_at' => '2026-05-10 09:00:00',
                'image' => $images['developers'],
                'fr' => [
                    'eyebrow' => 'Transformation telecom',
                    'title' => 'Migrer d un fournisseur telecom legacy vers une API moderne',
                    'seo_title' => 'Migration telecom legacy vers API CPaaS moderne',
                    'meta_description' => 'Plan de migration telecom : audit des flux, sandbox, parallele run, webhooks, securite et bascule progressive vers une API CPaaS.',
                    'lead' => 'Une migration telecom reussie avance par blocs: comprendre les flux, tester, doubler les routes puis basculer sans interrompre le service.',
                    'reading_time' => '6 min',
                    'image_alt' => 'Equipe technique travaillant sur une migration API telecom',
                    'tags' => ['API telecom', 'Migration', 'CPaaS'],
                    'sections' => [
                        ['heading' => 'Auditer les flux existants', 'body' => "Avant de coder, il faut lister les usages: OTP, campagnes, alertes, appels entrants, appels sortants et exports. Chaque flux a ses contraintes de latence, de statut et de support.\n\nCet audit evite de migrer seulement l endpoint en oubliant les processus autour."],
                        ['heading' => 'Tester en sandbox', 'body' => "La sandbox doit reproduire les cas utiles: succes, erreur temporaire, erreur finale, webhook duplique et timeout. Les equipes peuvent ainsi renforcer leur logique avant le trafic reel.\n\nLes tests doivent inclure les formats de numero, les pays prioritaires et les politiques de retry."],
                        ['heading' => 'Basculer progressivement', 'body' => "Le parallele run limite le risque. Une partie du trafic passe sur la nouvelle API pendant que l ancienne route reste disponible.\n\nDream Digital recommande une bascule par service, pays et volume, avec indicateurs visibles pour decider objectivement de la suite."],
                    ],
                ],
                'en' => [
                    'eyebrow' => 'Telecom transformation',
                    'title' => 'Migrating from a legacy telecom provider to a modern API',
                    'seo_title' => 'Legacy telecom migration to a modern CPaaS API',
                    'meta_description' => 'Telecom migration plan: traffic audit, sandbox, parallel run, webhooks, security and gradual move to a CPaaS API.',
                    'lead' => 'A successful telecom migration moves by blocks: understand flows, test, run routes in parallel and switch without service interruption.',
                    'reading_time' => '6 min',
                    'image_alt' => 'Technical team working on a telecom API migration',
                    'tags' => ['Telecom API', 'Migration', 'CPaaS'],
                    'sections' => [
                        ['heading' => 'Audit existing flows', 'body' => "Before coding, teams should list use cases: OTP, campaigns, alerts, inbound calls, outbound calls and exports. Each flow has its own latency, status and support constraints.\n\nThis audit avoids migrating only the endpoint while forgetting the surrounding process."],
                        ['heading' => 'Test in sandbox', 'body' => "The sandbox should reproduce useful cases: success, temporary error, final error, duplicated webhook and timeout. Teams can then strengthen their logic before live traffic.\n\nTests should include number formats, priority countries and retry policies."],
                        ['heading' => 'Switch progressively', 'body' => "A parallel run reduces risk. Part of the traffic goes through the new API while the former route remains available.\n\nDream Digital recommends switching by service, country and volume, with visible indicators to decide the next step objectively."],
                    ],
                ],
            ],
        ];
    }

    private function enrichSections(array $sections, array $content, string $locale): array
    {
        $sections = collect($sections)
            ->map(fn (array $section) => array_merge($section, [
                'body_html' => $section['body_html'] ?? $this->bodyToHtml($section['body'] ?? ''),
            ]))
            ->values()
            ->all();

        $tags = implode(', ', $content['tags'] ?? []);

        $sections[] = $locale === 'fr'
            ? [
                'heading' => 'Checklist operationnelle avant de lancer',
                'body' => "Avant la mise en production, validez le pays cible, le volume attendu, le SLA, les regles de fallback, les alertes support et les rapports attendus par le metier.\n\nCette checklist transforme un sujet telecom en plan d execution clair: un responsable, une date de test, des KPI et une decision de go/no-go documentee.",
                'body_html' => '<p>Avant la mise en production, validez le pays cible, le volume attendu, le SLA, les regles de fallback, les alertes support et les rapports attendus par le metier.</p><ul><li>Responsable technique et business nommes</li><li>KPI et seuils d alerte definis</li><li>Scenario de fallback teste</li><li>Decision de go/no-go documentee</li></ul>',
            ]
            : [
                'heading' => 'Operational checklist before launch',
                'body' => "Before going live, validate the target country, expected volume, SLA, fallback rules, support alerts and reports expected by the business.\n\nThis checklist turns a telecom topic into a clear execution plan: one owner, a test date, KPIs and a documented go/no-go decision.",
                'body_html' => '<p>Before going live, validate the target country, expected volume, SLA, fallback rules, support alerts and reports expected by the business.</p><ul><li>Technical and business owners assigned</li><li>KPIs and alert thresholds defined</li><li>Fallback scenario tested</li><li>Go/no-go decision documented</li></ul>',
            ];

        $sections[] = $locale === 'fr'
            ? [
                'heading' => 'Comment Dream Digital peut aider',
                'body' => "Dream Digital peut cadrer le besoin, comparer les routes, preparer un test pilote et fournir les donnees de suivi utiles aux equipes produit, support et commerciales.\n\nPour accelerer l echange, partagez le canal concerne, les destinations prioritaires, les volumes mensuels et les mots cles metier: {$tags}.",
                'body_html' => "<p>Dream Digital peut cadrer le besoin, comparer les routes, preparer un test pilote et fournir les donnees de suivi utiles aux equipes produit, support et commerciales.</p><p>Pour accelerer l echange, partagez le canal concerne, les destinations prioritaires, les volumes mensuels et les mots cles metier: <strong>{$this->escape($tags)}</strong>.</p>",
            ]
            : [
                'heading' => 'How Dream Digital can help',
                'body' => "Dream Digital can frame the requirement, compare routes, prepare a pilot test and provide monitoring data for product, support and sales teams.\n\nTo speed up the discussion, share the channel, priority destinations, monthly volumes and business keywords: {$tags}.",
                'body_html' => "<p>Dream Digital can frame the requirement, compare routes, prepare a pilot test and provide monitoring data for product, support and sales teams.</p><p>To speed up the discussion, share the channel, priority destinations, monthly volumes and business keywords: <strong>{$this->escape($tags)}</strong>.</p>",
            ];

        return $sections;
    }

    private function faqFor(array $content, string $locale): array
    {
        $topic = $content['eyebrow'] ?? 'CPaaS';
        $keywords = implode(', ', $content['tags'] ?? []);

        if ($locale === 'en') {
            return [
                [
                    'question' => "How does {$topic} help a B2B telecom or digital team?",
                    'answer' => "{$topic} helps teams connect operational quality with business outcomes: delivery, routing, customer experience, margin and support visibility. The practical starting point is to define one measurable flow and monitor it before scaling.",
                ],
                [
                    'question' => 'What should be prepared before requesting a Dream Digital recommendation?',
                    'answer' => "Prepare target countries, channels, monthly volumes, current pain points and SLA expectations. Useful keywords for this topic include {$keywords}.",
                ],
            ];
        }

        return [
            [
                'question' => "Comment {$topic} aide une equipe telecom ou digitale B2B ?",
                'answer' => "{$topic} relie la qualite operationnelle aux resultats business : livraison, routage, experience client, marge et visibilite support. Le bon point de depart consiste a cadrer un flux mesurable puis a le suivre avant de scaler.",
            ],
            [
                'question' => 'Que preparer avant de demander une recommandation Dream Digital ?',
                'answer' => "Preparez les pays cibles, les canaux, les volumes mensuels, les irritants actuels et les attentes SLA. Les mots cles utiles pour ce sujet sont {$keywords}.",
            ],
        ];
    }

    private function bodyToHtml(string $body): string
    {
        return collect(preg_split('/\R{2,}/', $body) ?: [])
            ->map(fn (string $paragraph) => trim($paragraph))
            ->filter()
            ->map(fn (string $paragraph) => '<p>' . $this->escape($paragraph) . '</p>')
            ->implode('');
    }

    private function escape(string $value): string
    {
        return e($value);
    }
}
