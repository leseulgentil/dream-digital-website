<?php

namespace App\Http\Controllers;

class PreviewController extends Controller
{
  public function designTokens()
  {
    return view('preview.design-tokens');
  }
}
