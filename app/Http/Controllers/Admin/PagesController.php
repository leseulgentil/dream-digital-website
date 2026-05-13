<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Country;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagesController extends Controller
{
    private const SECTIONS = ['legal', 'marketing', 'blog', 'help'];

    public function index(Request $request): View
    {
        $query = Page::with('country')->orderBy('section')->orderBy('slug')->orderBy('locale');

        if ($section = $request->input('section')) {
            $query->where('section', $section);
        }
        if ($locale = $request->input('locale')) {
            $query->where('locale', $locale);
        }
        if ($request->has('published') && $request->input('published') !== '') {
            $query->where('is_published', $request->boolean('published'));
        }

        return view('admin.pages.index', [
            'pages' => $query->paginate(25)->withQueryString(),
            'sections' => self::SECTIONS,
            'filters' => [
                'section' => $section,
                'locale' => $locale,
                'published' => $request->input('published', ''),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.pages.create', [
            'page' => new Page(['is_published' => true, 'locale' => 'fr', 'section' => 'legal']),
            'countries' => Country::active()->orderBy('sort_order')->get(),
            'sections' => self::SECTIONS,
            'sectionsJson' => '[]',
        ]);
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $page = Page::create($this->payload($request));

        return redirect()->route('admin.pages.index')
            ->with('status', "Page creee : {$page->title} ({$page->section}/{$page->locale})");
    }

    public function edit(Page $page): View
    {
        $blocks = $page->content_blocks ?? [];

        return view('admin.pages.edit', [
            'page' => $page,
            'countries' => Country::active()->orderBy('sort_order')->get(),
            'sections' => self::SECTIONS,
            'sectionsJson' => json_encode(
                $blocks['sections'] ?? [],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
        ]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $page->update($this->payload($request));

        return redirect()->route('admin.pages.index')
            ->with('status', "Page mise a jour : {$page->title}");
    }

    public function destroy(Page $page): RedirectResponse
    {
        $label = $page->title;
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('status', "Page supprimee : {$label}");
    }

    private function payload(PageRequest $request): array
    {
        $validated = $request->validated();
        $sectionsArray = $request->decodedSections() ?? [];

        return [
            'slug' => $validated['slug'],
            'section' => $validated['section'],
            'country_id' => $validated['country_id'] ?? null,
            'locale' => $validated['locale'],
            'title' => $validated['title'],
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_image_path' => $validated['meta_image_path'] ?? null,
            'content_blocks' => [
                'seo_title' => $validated['seo_title'] ?? null,
                'eyebrow' => $validated['eyebrow'] ?? null,
                'lead' => $validated['lead'] ?? null,
                'author' => $validated['author'] ?? null,
                'reading_time' => $validated['reading_time'] ?? null,
                'image_alt' => $validated['image_alt'] ?? null,
                'image_credit' => $validated['image_credit'] ?? null,
                'image_source_url' => $validated['image_source_url'] ?? null,
                'tags' => $request->decodedTags(),
                'last_updated' => $validated['last_updated'] ?? null,
                'sections' => $sectionsArray,
            ],
            'is_published' => $validated['is_published'] ?? false,
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ];
    }
}
