<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(): View
    {
        $directory = public_path('img/cms/pages');
        File::ensureDirectoryExists($directory);

        $media = collect(File::files($directory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'path' => '/img/cms/pages/' . $file->getFilename(),
                'size_kb' => round($file->getSize() / 1024, 1),
                'modified_at' => date('Y-m-d H:i', $file->getMTime()),
            ])
            ->values();

        return view('admin.media.index', ['media' => $media]);
    }
}
