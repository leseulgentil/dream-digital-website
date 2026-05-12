<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PricingController extends Controller
{
  public function index()
  {
    return view('admin.pricing.index', [
      'services' => collect(config('dream-digital.services.items', []))->where('active', true)->sortBy('order')->values(),
      'corridors' => config('dream-digital.pages.corridors', []),
    ]);
  }
}
