<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiWebSourceRequest;
use App\Models\AiKnowledgeWebSource;
use App\Services\Ai\AiWebKnowledgeImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiWebSourcesController extends Controller
{
    public function store(AiWebSourceRequest $request, AiWebKnowledgeImporter $importer): RedirectResponse
    {
        $validated = $request->validated();

        $webSource = AiKnowledgeWebSource::create([
            'title' => $validated['title'],
            'type' => $validated['type'],
            'url' => $validated['url'],
            'locale' => $validated['locale'],
            'country_code' => $validated['country_code'],
            'category' => $validated['category'] ?? null,
            'frequency' => $validated['frequency'],
            'import_status' => $validated['import_status'],
            'status' => AiKnowledgeWebSource::STATUS_ACTIVE,
            'next_sync_at' => $validated['frequency'] === AiKnowledgeWebSource::FREQUENCY_MANUAL ? null : now(),
            'metadata' => [
                'auth_token' => filled($validated['auth_token'] ?? null)
                    ? Crypt::encryptString((string) $validated['auth_token'])
                    : null,
            ],
            'created_by_id' => auth()->id(),
        ]);

        if ($request->boolean('sync_now')) {
            return $this->sync($webSource, $importer);
        }

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', 'Source web enregistree.');
    }

    public function sync(AiKnowledgeWebSource $webSource, AiWebKnowledgeImporter $importer): RedirectResponse
    {
        try {
            $chunks = $importer->sync($webSource);
        } catch (Throwable $exception) {
            $webSource->forceFill([
                'last_error' => $exception->getMessage(),
            ])->save();

            Log::warning('AI web knowledge sync failed.', [
                'web_source_id' => $webSource->id,
                'url' => $webSource->url,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('admin.ai.knowledge.index')
                ->withErrors(['url' => 'Synchronisation impossible. Verifiez la source puis reessayez.']);
        }

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', "Source web synchronisee : {$chunks} segment(s) crees ou mis a jour.");
    }

    public function destroy(AiKnowledgeWebSource $webSource): RedirectResponse
    {
        $webSource->sources()->delete();
        $webSource->delete();

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', 'Source web supprimee.');
    }
}
