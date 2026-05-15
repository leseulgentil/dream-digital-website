<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(): View
    {
        $this->syncLocalFiles();

        $media = MediaAsset::query()
            ->with('uploader')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (MediaAsset $asset): array {
                $usage = $this->usageFor($asset->path);

                return [
                    'asset' => $asset,
                    'size_kb' => $asset->size_bytes ? round($asset->size_bytes / 1024, 1) : null,
                    'usage' => $usage,
                ];
            });

        return view('admin.media.index', ['media' => $media]);
    }

    public function update(Request $request, MediaAsset $media): RedirectResponse
    {
        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:220'],
            'credit' => ['nullable', 'string', 'max:220'],
            'source_url' => ['nullable', 'url', 'max:500'],
        ]);

        $media->update($validated);

        return redirect()
            ->route('admin.media.index')
            ->with('status', "Media mis a jour : {$media->filename}");
    }

    public function destroy(MediaAsset $media): RedirectResponse
    {
        $usage = $this->usageFor($media->path);
        if ($usage->isNotEmpty()) {
            return redirect()
                ->route('admin.media.index')
                ->with('error', "Suppression bloquee : {$media->filename} est utilise par {$usage->count()} page(s).");
        }

        $fullPath = public_path(ltrim($media->path, '/'));
        if (is_file($fullPath)) {
            File::delete($fullPath);
        }
        $media->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('status', "Media supprime : {$media->filename}");
    }

    private function syncLocalFiles(): void
    {
        $directory = public_path('img/cms/pages');
        File::ensureDirectoryExists($directory);

        collect(File::files($directory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))
            ->each(function ($file): void {
                if (! is_file($file->getPathname())) {
                    return;
                }

                $path = '/img/cms/pages/' . $file->getFilename();
                $dimensions = @getimagesize($file->getPathname());

                MediaAsset::updateOrCreate(
                    ['path' => $path],
                    [
                        'filename' => $file->getFilename(),
                        'mime_type' => $dimensions['mime'] ?? null,
                        'size_bytes' => $file->getSize(),
                        'width' => $dimensions[0] ?? null,
                        'height' => $dimensions[1] ?? null,
                    ],
                );
            });
    }

    private function usageFor(string $path)
    {
        return Page::query()
            ->where('meta_image_path', $path)
            ->orderBy('section')
            ->orderBy('slug')
            ->get(['id', 'section', 'slug', 'locale', 'title']);
    }
}
