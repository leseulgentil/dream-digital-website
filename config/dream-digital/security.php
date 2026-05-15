<?php

return [
    'csp' => [
        'enabled' => env('DD_CSP_ENABLED', true),
        'report_only' => env('DD_CSP_REPORT_ONLY', true),
        'report_uri' => env('DD_CSP_REPORT_URI'),
    ],

    'security_txt' => [
        'contact' => 'security@dream-digital.info',
        'expires_days' => (int) env('DD_SECURITY_TXT_EXPIRES_DAYS', 30),
    ],
];
