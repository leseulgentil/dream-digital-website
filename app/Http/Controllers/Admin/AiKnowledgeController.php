<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiKnowledgeRequest;
use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Models\RoleProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiKnowledgeController extends Controller
{
    public function index(Request $request): View
    {
        $query = AiKnowledgeChunk::query()
            ->with('source')
            ->latest('updated_at');

        if ($locale = $request->input('locale')) {
            $query->where('locale', $locale);
        }
        if ($countryCode = $request->input('country_code')) {
            $query->where('country_code', $countryCode);
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('admin.ai.knowledge-index', [
            'chunks' => $query->paginate(25)->withQueryString(),
            'canManageAiKnowledge' => $request->user()?->hasPermission(RoleProfile::PERMISSION_AI_KNOWLEDGE_MANAGE) ?? false,
            'filters' => [
                'locale' => $locale,
                'country_code' => $countryCode,
                'status' => $status,
            ],
        ]);
    }

    public function store(AiKnowledgeRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $source = AiKnowledgeSource::create([
            'type' => AiKnowledgeSource::TYPE_MANUAL,
            'title' => $validated['title'],
            'locale' => $validated['locale'],
            'country_code' => $validated['country_code'],
            'status' => $validated['status'],
            'metadata' => [
                'category' => $validated['category'] ?? null,
            ],
            'created_by_id' => auth()->id(),
        ]);

        $source->chunks()->create($this->chunkPayload($validated));

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', 'Entree ajoutee a la base IA.');
    }

    public function edit(AiKnowledgeChunk $chunk): View
    {
        return view('admin.ai.knowledge-edit', [
            'chunk' => $chunk->load('source'),
        ]);
    }

    public function update(AiKnowledgeRequest $request, AiKnowledgeChunk $chunk): RedirectResponse
    {
        $validated = $request->validated();

        $chunk->update($this->chunkPayload($validated));

        if ($chunk->source) {
            $chunk->source->update([
                'title' => $validated['title'],
                'status' => $validated['status'],
                'locale' => $validated['locale'],
                'country_code' => $validated['country_code'],
                'metadata' => [
                    ...($chunk->source->metadata ?? []),
                    'category' => $validated['category'] ?? null,
                ],
            ]);
        }

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', 'Entree mise a jour.');
    }

    public function destroy(AiKnowledgeChunk $chunk): RedirectResponse
    {
        $source = $chunk->source;
        $chunk->delete();

        if ($source && ! $source->chunks()->exists()) {
            $source->delete();
        }

        return redirect()
            ->route('admin.ai.knowledge.index')
            ->with('status', 'Entree supprimee.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function chunkPayload(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'locale' => $validated['locale'],
            'country_code' => $validated['country_code'],
            'category' => $validated['category'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'] ?? 0,
            'expires_at' => $validated['expires_at'] ?? null,
        ];
    }
}
