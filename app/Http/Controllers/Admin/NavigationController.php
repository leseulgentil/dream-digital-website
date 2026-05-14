<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NavigationItemRequest;
use App\Models\NavigationItem;
use App\Services\Navigation\MainMenuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NavigationController extends Controller
{
    public function __construct(private readonly MainMenuService $mainMenuService)
    {
    }

    public function index(): View
    {
        return view('admin.navigation.index', [
            'items' => NavigationItem::main()
                ->whereNull('parent_id')
                ->with('children')
                ->orderBy('sort_order')
                ->orderBy('label_fr')
                ->get(),
            'suggestions' => $this->mainMenuService->suggestions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.navigation.create', [
            'item' => new NavigationItem([
                'menu_area' => 'main',
                'type' => NavigationItem::TYPE_LINK,
                'is_active' => true,
                'sort_order' => 80,
            ]),
            'parents' => $this->parentOptions(),
            'types' => NavigationItem::TYPES,
            'suggestions' => $this->mainMenuService->suggestions(),
        ]);
    }

    public function store(NavigationItemRequest $request): RedirectResponse
    {
        $item = NavigationItem::create($request->payload());

        return redirect()
            ->route('admin.navigation.index')
            ->with('status', "Lien de menu cree : {$item->label_fr}");
    }

    public function edit(NavigationItem $navigation): View
    {
        return view('admin.navigation.edit', [
            'item' => $navigation,
            'parents' => $this->parentOptions($navigation),
            'types' => NavigationItem::TYPES,
            'suggestions' => $this->mainMenuService->suggestions(),
        ]);
    }

    public function update(NavigationItemRequest $request, NavigationItem $navigation): RedirectResponse
    {
        $navigation->update($request->payload());

        return redirect()
            ->route('admin.navigation.index')
            ->with('status', "Lien de menu mis a jour : {$navigation->label_fr}");
    }

    public function destroy(NavigationItem $navigation): RedirectResponse
    {
        $label = $navigation->label_fr;
        $navigation->delete();

        return redirect()
            ->route('admin.navigation.index')
            ->with('status', "Lien de menu supprime : {$label}");
    }

    private function parentOptions(?NavigationItem $current = null)
    {
        return NavigationItem::main()
            ->whereNull('parent_id')
            ->when($current?->exists, fn ($query) => $query->whereKeyNot($current->id))
            ->orderBy('sort_order')
            ->orderBy('label_fr')
            ->get();
    }
}
