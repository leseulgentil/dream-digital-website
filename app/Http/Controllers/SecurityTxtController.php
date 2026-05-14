<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SecurityTxtController extends Controller
{
    public function __invoke(): Response
    {
        $contact = (string) config('dream-digital.security.security_txt.contact', 'security@dream-digital.info');
        $expires = now()->addDays((int) config('dream-digital.security.security_txt.expires_days', 30))->toIso8601String();

        $body = implode("\n", [
            'Contact: mailto:' . $contact,
            'Expires: ' . $expires,
            'Preferred-Languages: fr, en',
            'Canonical: ' . rtrim(config('app.url'), '/') . '/.well-known/security.txt',
            '',
        ]);

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
