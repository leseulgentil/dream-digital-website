<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    public const LOCALES = ['fr', 'en'];

    protected $fillable = [
        'locale',
        'company_name',
        'legal_name',
        'public_phone',
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
    ];

    protected $casts = [
        'legal_validated' => 'boolean',
        'admin_password_rotated' => 'boolean',
    ];
}
