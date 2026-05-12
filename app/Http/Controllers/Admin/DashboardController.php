<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  public function index()
  {
    return view('admin.dashboard', [
      'services' => collect(config('dream-digital.services.items', []))->where('active', true)->sortBy('order')->values(),
      'stats' => config('dream-digital.pages.stats', []),
      'corridors' => config('dream-digital.pages.corridors', []),
      'features' => config('dream-digital.pages.features.admin', []),
    ]);
  }
}
