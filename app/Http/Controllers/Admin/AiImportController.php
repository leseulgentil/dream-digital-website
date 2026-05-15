<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiImportRequest;
use App\Services\Ai\AiKnowledgeImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiImportController extends Controller
{
    public function create(): View
    {
        return view('admin.ai.import');
    }

    public function store(AiImportRequest $request, AiKnowledgeImporter $importer): RedirectResponse
    {
        $validated = $request->validated();
        $source = $importer->import($validated['file'], [
            'title' => $validated['title'],
            'locale' => $validated['locale'],
            'country_code' => $validated['country_code'],
            'category' => $validated['category'] ?? null,
            'created_by_id' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', 'Import termine : '.$source->chunks->count().' segment(s) crees.');
    }
}
