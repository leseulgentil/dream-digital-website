<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function show(Request $request, string $locale, string $slug): View
    {
        $locale = in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
        $pageConfig = config("dream-digital.legal.pages.$slug");

        abort_if(empty($pageConfig), 404);

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        return view('content.front-pages.legal-page', [
            'pageConfigs' => ['myLayout' => 'front'],
            'locale'      => $locale,
            'page'        => 'legal-' . $slug,
            'legal'       => $pageConfig,
            'site'        => config('dream-digital.site'),
            'allPages'    => config('dream-digital.legal.pages'),
        ]);
    }
}
