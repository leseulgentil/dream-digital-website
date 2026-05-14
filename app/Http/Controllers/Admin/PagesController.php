<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Country;
use App\Models\Page;
use App\Models\PageRevision;
use App\Services\Admin\ArticleGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PagesController extends Controller
{
    private const SECTIONS = ['legal', 'marketing', 'blog', 'help'];

    public function __construct(private readonly ArticleGeneratorService $articleGenerator)
    {
    }

    public function index(Request $request): View
    {
        $query = Page::with('country')
            ->withCount('revisions')
            ->orderBy('section')
            ->orderBy('slug')
            ->orderBy('locale');

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
            'cmsSchemas' => config('dream-digital.cms.schemas', []),
        ]);
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $page = Page::create($this->payload($request));
        $this->recordRevision($page, 'created');

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
            'cmsSchemas' => config('dream-digital.cms.schemas', []),
            'revisions' => $page->revisions()->with('user')->limit(8)->get(),
            'sectionsJson' => json_encode(
                $blocks['sections'] ?? [],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
        ]);
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $page->update($this->payload($request, $page));
        $this->recordRevision($page, 'updated');

        return redirect()->route('admin.pages.index')
            ->with('status', "Page mise a jour : {$page->title}");
    }

    public function preview(Request $request, Page $page): View
    {
        app()->setLocale($page->locale);
        $request->session()->put('locale', $page->locale);

        return match ($page->section) {
            'blog' => view('content.front-pages.blog-show', [
                'pageConfigs' => ['myLayout' => 'front'],
                'locale' => $page->locale,
                'article' => $this->pageToArticle($page),
                'related' => Page::published()
                    ->where('section', 'blog')
                    ->where('locale', $page->locale)
                    ->whereNull('country_id')
                    ->where('id', '!=', $page->id)
                    ->orderByDesc('published_at')
                    ->limit(3)
                    ->get()
                    ->map(fn (Page $relatedPage) => $this->pageToArticle($relatedPage)),
                'site' => config('dream-digital.site'),
            ]),
            'legal' => view('content.front-pages.legal-page', [
                'pageConfigs' => ['myLayout' => 'front'],
                'locale' => $page->locale,
                'page' => 'legal-' . $page->slug,
                'legal' => $this->pageToLegal($page),
                'site' => config('dream-digital.site'),
                'allPages' => $this->legalPagesFor($page),
            ]),
            'marketing' => view('content.front-pages.marketing-page', $this->marketingPreviewData($page)),
            default => view('content.front-pages.cms-preview', [
                'pageConfigs' => ['myLayout' => 'front'],
                'locale' => $page->locale,
                'page' => $this->pageToLegal($page),
                'site' => config('dream-digital.site'),
            ]),
        };
    }

    public function duplicateLocale(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'target_locale' => ['required', Rule::in(['fr', 'en'])],
        ]);
        $targetLocale = $validated['target_locale'];

        abort_if($targetLocale === $page->locale, 422);

        $existing = Page::query()
            ->where('slug', $page->slug)
            ->where('section', $page->section)
            ->where('locale', $targetLocale)
            ->when($page->country_id, fn ($query) => $query->where('country_id', $page->country_id), fn ($query) => $query->whereNull('country_id'))
            ->first();

        if ($existing) {
            return redirect()
                ->route('admin.pages.edit', $existing)
                ->with('status', "Version {$targetLocale} deja existante : {$existing->title}");
        }

        $duplicate = $page->replicate();
        $duplicate->locale = $targetLocale;
        $duplicate->title = '[' . strtoupper($targetLocale) . '] ' . $page->title;
        $duplicate->is_published = false;
        $duplicate->published_at = null;
        $duplicate->save();
        $this->recordRevision($duplicate, 'duplicated');

        return redirect()
            ->route('admin.pages.edit', $duplicate)
            ->with('status', "Brouillon {$targetLocale} cree depuis {$page->locale}.");
    }

    public function destroy(Page $page): RedirectResponse
    {
        $label = $page->title;
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('status', "Page supprimee : {$label}");
    }

    public function generateArticle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idea' => ['required', 'string', 'max:200'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'guidelines' => ['nullable', 'string', 'max:2000'],
            'locale' => ['required', Rule::in(['fr', 'en'])],
            'variants' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        try {
            return response()->json($this->articleGenerator->generateWithMetadata($validated));
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'La generation OpenAI est indisponible pour le moment.',
                'error' => $exception->getMessage(),
                'provider' => 'openai',
                'fallback_used' => false,
            ], 502);
        }
    }

    private function payload(PageRequest $request, ?Page $page = null): array
    {
        $validated = $request->validated();
        $existingBlocks = $page?->content_blocks ?? [];
        $sectionsArray = $request->decodedSections() ?? [];
        $faqArray = $request->decodedFaq();
        $uploadedImagePath = $this->uploadedImagePath($request, $validated);

        return [
            'slug' => $validated['slug'],
            'section' => $validated['section'],
            'country_id' => $validated['country_id'] ?? null,
            'locale' => $validated['locale'],
            'title' => $validated['title'],
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_image_path' => $uploadedImagePath ?? ($validated['meta_image_path'] ?? null),
            'content_blocks' => array_merge($existingBlocks, [
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
                'faq' => $faqArray ?? ($existingBlocks['faq'] ?? []),
                'sections' => $sectionsArray,
            ]),
            'is_published' => $validated['is_published'] ?? false,
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ];
    }

    private function recordRevision(Page $page, string $action): void
    {
        PageRevision::create([
            'page_id' => $page->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'slug' => $page->slug,
            'section' => $page->section,
            'locale' => $page->locale,
            'title' => $page->title,
            'meta_description' => $page->meta_description,
            'meta_image_path' => $page->meta_image_path,
            'content_blocks' => $page->content_blocks,
            'is_published' => $page->is_published,
            'published_at' => $page->published_at,
        ]);
    }

    private function uploadedImagePath(PageRequest $request, array $validated): ?string
    {
        if (!$request->hasFile('image_file')) {
            return null;
        }

        $file = $request->file('image_file');
        $directory = public_path('img/cms/pages');
        File::ensureDirectoryExists($directory);

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = now()->format('YmdHis') . '-' . $validated['section'] . '-' . $validated['slug'] . '-' . $validated['locale'] . '.' . $extension;

        $file->move($directory, $filename);

        return '/img/cms/pages/' . $filename;
    }

    private function pageToArticle(Page $page): array
    {
        $blocks = $page->content_blocks ?? [];

        return [
            'slug' => $page->slug,
            'title' => $page->title,
            'seo_title' => $blocks['seo_title'] ?? $page->title,
            'meta_description' => $page->meta_description,
            'meta_image_path' => $page->meta_image_path,
            'eyebrow' => $blocks['eyebrow'] ?? 'Blog',
            'lead' => $blocks['lead'] ?? '',
            'author' => $blocks['author'] ?? 'Dream Digital',
            'reading_time' => $blocks['reading_time'] ?? null,
            'image_alt' => $blocks['image_alt'] ?? $page->title,
            'image_credit' => $blocks['image_credit'] ?? null,
            'image_source_url' => $blocks['image_source_url'] ?? null,
            'tags' => $blocks['tags'] ?? [],
            'seo_focus_keywords' => $blocks['seo_focus_keywords'] ?? ($blocks['tags'] ?? []),
            'faq' => $blocks['faq'] ?? [],
            'sections' => $blocks['sections'] ?? [],
            'published_at' => $page->published_at ?? $page->created_at,
            'updated_at' => $page->updated_at,
            'url' => url('/' . $page->locale . '/blog/' . $page->slug),
        ];
    }

    private function pageToLegal(Page $page): array
    {
        $blocks = $page->content_blocks ?? [];

        return [
            'slug' => $page->slug,
            'title' => $page->title,
            'eyebrow' => $blocks['eyebrow'] ?? '',
            'lead' => $blocks['lead'] ?? '',
            'last_updated' => $blocks['last_updated'] ?? optional($page->updated_at)->format('Y-m-d'),
            'sections' => $blocks['sections'] ?? [],
            'source' => 'preview',
        ];
    }

    private function legalPagesFor(Page $page): array
    {
        $pages = Page::query()
            ->where('section', 'legal')
            ->where('locale', $page->locale)
            ->whereNull('country_id')
            ->orderBy('slug')
            ->get();

        if ($pages->doesntContain('id', $page->id)) {
            $pages->push($page);
        }

        return $pages
            ->mapWithKeys(fn (Page $legalPage) => [$legalPage->slug => ['slug' => $legalPage->slug, 'title' => $legalPage->title]])
            ->all();
    }

    private function marketingPreviewData(Page $page): array
    {
        $blocks = $page->content_blocks ?? [];
        $locale = $page->locale;

        return [
            'pageConfigs' => ['myLayout' => 'front'],
            'locale' => $locale,
            'page' => $page->slug,
            'pageData' => [
                'eyebrow' => $blocks['eyebrow'] ?? '',
                'title' => $page->title,
                'lead' => $blocks['lead'] ?? '',
                'source' => 'preview',
            ],
            'site' => config('dream-digital.site'),
            'home' => config('dream-digital.home'),
            'services' => $this->activeItems(config('dream-digital.services.items', [])),
            'industries' => $this->activeItems(config('dream-digital.industries.items', [])),
            'coverage' => config('dream-digital.coverage'),
            'stats' => config('dream-digital.pages.stats', []),
            'features' => config('dream-digital.pages.features', []),
            'corridors' => config('dream-digital.pages.corridors', []),
            'liveFeed' => config('dream-digital.pages.live_feed', []),
        ];
    }

    private function activeItems(array $items): array
    {
        return Collection::make($items)
            ->filter(fn ($item) => (bool) ($item['active'] ?? true))
            ->sortBy('order')
            ->values()
            ->all();
    }
}
