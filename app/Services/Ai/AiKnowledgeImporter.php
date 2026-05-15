<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeSource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Smalot\PdfParser\Parser;

class AiKnowledgeImporter
{
    public function __construct(
        private readonly AiKnowledgeChunker $chunker,
    ) {}

    public function import(UploadedFile $file, array $metadata): AiKnowledgeSource
    {
        $type = $this->typeForExtension($file->getClientOriginalExtension());

        return DB::transaction(function () use ($file, $metadata, $type): AiKnowledgeSource {
            $storedPath = $file->store('ai-knowledge-sources', 'local');
            $title = (string) ($metadata['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $locale = (string) ($metadata['locale'] ?? 'fr');
            $countryCode = (string) ($metadata['country_code'] ?? 'global');
            $category = $metadata['category'] ?? null;

            $source = AiKnowledgeSource::create([
                'type' => $type,
                'title' => $title,
                'original_filename' => $file->getClientOriginalName(),
                'stored_path' => $storedPath,
                'mime_type' => $file->getMimeType(),
                'locale' => $locale,
                'country_code' => $countryCode,
                'status' => 'draft',
                'metadata' => [
                    'category' => $category,
                ],
                'created_by_id' => $metadata['created_by_id'] ?? null,
            ]);

            foreach ($this->chunkRows($type, $storedPath, $title, $locale, $countryCode, $category) as $chunk) {
                $source->chunks()->create([
                    'title' => $chunk['title'],
                    'content' => $chunk['content'],
                    'locale' => $chunk['locale'],
                    'country_code' => $chunk['country_code'],
                    'category' => $chunk['category'],
                    'status' => 'draft',
                    'priority' => 0,
                ]);
            }

            return $source->load('chunks');
        });
    }

    private function typeForExtension(string $extension): string
    {
        return match (strtolower($extension)) {
            'md', 'markdown' => AiKnowledgeSource::TYPE_MARKDOWN,
            'csv' => AiKnowledgeSource::TYPE_CSV,
            'pdf' => AiKnowledgeSource::TYPE_PDF,
            default => throw new InvalidArgumentException("Unsupported knowledge file type [{$extension}]."),
        };
    }

    /**
     * @return array<int, array{title: string, content: string, locale: string, country_code: string, category: mixed}>
     */
    private function chunkRows(
        string $type,
        string $storedPath,
        string $title,
        string $locale,
        string $countryCode,
        mixed $category,
    ): array {
        return match ($type) {
            AiKnowledgeSource::TYPE_MARKDOWN => $this->textChunkRows(
                Storage::disk('local')->get($storedPath),
                $title,
                $locale,
                $countryCode,
                $category,
            ),
            AiKnowledgeSource::TYPE_CSV => $this->csvChunkRows($storedPath, $title, $locale, $countryCode, $category),
            AiKnowledgeSource::TYPE_PDF => $this->textChunkRows(
                (new Parser)->parseFile(Storage::disk('local')->path($storedPath))->getText(),
                $title,
                $locale,
                $countryCode,
                $category,
            ),
            default => [],
        };
    }

    /**
     * @return array<int, array{title: string, content: string, locale: string, country_code: string, category: mixed}>
     */
    private function textChunkRows(
        string $text,
        string $title,
        string $locale,
        string $countryCode,
        mixed $category,
    ): array {
        $rows = [];

        foreach ($this->chunker->chunks($text) as $index => $content) {
            $rows[] = [
                'title' => $title.' #'.($index + 1),
                'content' => $content,
                'locale' => $locale,
                'country_code' => $countryCode,
                'category' => $category,
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{title: string, content: string, locale: string, country_code: string, category: mixed}>
     */
    private function csvChunkRows(
        string $storedPath,
        string $title,
        string $locale,
        string $countryCode,
        mixed $category,
    ): array {
        $handle = fopen(Storage::disk('local')->path($storedPath), 'rb');

        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle);
        $rows = [];

        if ($headers === false) {
            fclose($handle);

            return [];
        }

        $headers = array_map(
            fn (string $header): string => strtolower(trim($header)),
            $headers,
        );

        while (($values = fgetcsv($handle)) !== false) {
            $values = array_slice(array_pad($values, count($headers), ''), 0, count($headers));
            $row = array_combine($headers, $values);

            if ($row === false || $this->isBlankCsvRow($row)) {
                continue;
            }

            $question = trim((string) ($row['question'] ?? ''));
            $answer = trim((string) ($row['answer'] ?? ''));
            $content = trim($question."\n\n".$answer);

            if ($content === '') {
                continue;
            }

            $rows[] = [
                'title' => $question !== '' ? $question : $title.' #'.(count($rows) + 1),
                'content' => $content,
                'locale' => trim((string) ($row['locale'] ?? '')) ?: $locale,
                'country_code' => trim((string) ($row['country'] ?? '')) ?: $countryCode,
                'category' => trim((string) ($row['category'] ?? '')) ?: $category,
            ];
        }

        fclose($handle);

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function isBlankCsvRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }
}
