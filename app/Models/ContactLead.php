<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactLead extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_QUALIFIED = 'qualified';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'locale',
        'country_code',
        'status',
        'full_name',
        'company_name',
        'email',
        'phone',
        'service_interest',
        'monthly_volume',
        'message',
        'source_page',
        'ip_hash',
        'user_agent',
        'qualified_at',
    ];

    protected $casts = [
        'qualified_at' => 'datetime',
    ];
}
