<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\ContactLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactLeadController extends Controller
{
    public function store(Request $request, string $locale): RedirectResponse
    {
        $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';

        $payload = $request->validate([
            'full_name' => ['required', 'string', 'max:160'],
            'company_name' => ['nullable', 'string', 'max:190'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'service_interest' => ['nullable', Rule::in(['sms', 'voice', 'esim', 'did', 'sip', 'dialo', 'other'])],
            'monthly_volume' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:3000'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);

        if (filled($payload['website'] ?? null)) {
            return $this->redirectToContact($locale);
        }

        ContactLead::create([
            'locale' => $locale,
            'country_code' => app()->bound('current_country') ? app('current_country')?->code : null,
            'full_name' => $payload['full_name'],
            'company_name' => $payload['company_name'] ?? null,
            'email' => $payload['email'],
            'phone' => $payload['phone'] ?? null,
            'service_interest' => $payload['service_interest'] ?? null,
            'monthly_volume' => $payload['monthly_volume'] ?? null,
            'message' => $payload['message'],
            'source_page' => $request->headers->get('referer') ?: $request->fullUrl(),
            'ip_hash' => $request->ip() ? hash('sha256', $request->ip()) : null,
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        return $this->redirectToContact($locale);
    }

    private function redirectToContact(string $locale): RedirectResponse
    {
        return redirect("/{$locale}/contact")->with('status', $this->successMessage($locale));
    }

    private function successMessage(string $locale): string
    {
        return $locale === 'en'
            ? 'Thanks, your request has been sent.'
            : 'Merci, votre demande a ete envoyee.';
    }
}
