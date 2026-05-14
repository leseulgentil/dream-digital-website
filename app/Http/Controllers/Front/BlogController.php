<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        return $this->renderIndex($request, $this->contentLocale());
    }

    public function localizedIndex(Request $request, string $locale): View
    {
        return $this->renderIndex($request, $this->normalizeLocale($locale));
    }

    public function show(Request $request, string $slug): View
    {
        return $this->renderShow($request, $this->contentLocale(), $slug);
    }

    public function localizedShow(Request $request, string $locale, string $slug): View
    {
        return $this->renderShow($request, $this->normalizeLocale($locale), $slug);
    }

    private function renderIndex(Request $request, string $locale): View
    {
        abort_unless(Schema::hasTable('pages'), 404);

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        $articles = Page::published()
            ->where('section', 'blog')
            ->where('locale', $locale)
            ->whereNull('country_id')
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at')
            ->paginate(12)
            ->through(fn (Page $page) => $this->pageToArticle($page));
        $featured = $articles->getCollection()->first();

        return view('content.front-pages.blog-index', [
            'pageConfigs' => ['myLayout' => 'front'],
            'locale' => $locale,
            'articles' => $articles,
            'featured' => $featured,
            'site' => config('dream-digital.site'),
        ]);
    }

    private function renderShow(Request $request, string $locale, string $slug): View
    {
        abort_unless(Schema::hasTable('pages'), 404);

        $page = Page::published()
            ->where('section', 'blog')
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->whereNull('country_id')
            ->first();

        abort_if($page === null, 404);

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        $article = $this->pageToArticle($page);
        $related = Page::published()
            ->where('section', 'blog')
            ->where('locale', $locale)
            ->whereNull('country_id')
            ->where('id', '!=', $page->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Page $relatedPage) => $this->pageToArticle($relatedPage));

        return view('content.front-pages.blog-show', [
            'pageConfigs' => ['myLayout' => 'front'],
            'locale' => $locale,
            'article' => $article,
            'related' => $related,
            'site' => config('dream-digital.site'),
        ]);
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

    private function contentLocale(): string
    {
        return $this->normalizeLocale(session()->get('locale', 'fr'));
    }

    private function normalizeLocale(?string $locale): string
    {
        return in_array($locale, ['fr', 'en'], true) ? $locale : 'fr';
    }
}
