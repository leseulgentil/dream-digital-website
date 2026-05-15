<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    public const LOCALES = ['fr', 'en'];
    public const ENTITY_COUNTRIES = [
        'cd' => ['label' => 'RDC', 'city' => 'Kinshasa'],
        'ci' => ['label' => 'Cote d Ivoire', 'city' => 'Abidjan'],
        'cg' => ['label' => 'Congo', 'city' => 'Brazzaville'],
    ];

    protected $fillable = [
        'country_code',
        'locale',
        'company_name',
        'legal_name',
        'public_phone',
        'whatsapp_number',
        'address_line',
        'city',
        'country_label',
        'registration_number',
        'tax_id',
        'support_hours',
        'latitude',
        'longitude',
        'email_sales',
        'email_support',
        'email_security',
        'email_privacy',
        'social_linkedin',
        'social_twitter',
        'social_github',
        'og_image_path',
        'legal_validated',
        'admin_password_rotated',
        'public_basic_auth_disabled',
        'backups_configured',
        'env_backed_up',
        'deployment_runbook_reviewed',
    ];

    protected $casts = [
        'legal_validated' => 'boolean',
        'admin_password_rotated' => 'boolean',
        'public_basic_auth_disabled' => 'boolean',
        'backups_configured' => 'boolean',
        'env_backed_up' => 'boolean',
        'deployment_runbook_reviewed' => 'boolean',
    ];
}
