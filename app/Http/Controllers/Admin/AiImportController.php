<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiImportRequest;
use App\Services\Ai\AiKnowledgeImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AiImportController extends Controller
{
    public function create(): View
    {
        return view('admin.ai.import');
    }

    public function store(AiImportRequest $request, AiKnowledgeImporter $importer): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $source = $importer->import($validated['file'], [
                'title' => $validated['title'],
                'locale' => $validated['locale'],
                'country_code' => $validated['country_code'],
                'category' => $validated['category'] ?? null,
                'created_by_id' => auth()->id(),
            ]);
        } catch (Throwable $exception) {
            Log::warning('AI knowledge import failed.', [
                'message' => $exception->getMessage(),
                'filename' => $validated['file']->getClientOriginalName(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['file' => 'Import impossible. Verifiez le fichier puis reessayez.']);
        }

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', 'Import termine : '.$source->chunks->count().' segment(s) crees.');
    }
}
