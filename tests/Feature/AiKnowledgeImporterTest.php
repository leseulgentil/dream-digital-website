<?php

namespace Tests\Feature;

use App\Models\AiKnowledgeChunk;
use App\Models\AiKnowledgeSource;
use App\Services\Ai\AiKnowledgeChunker;
use App\Services\Ai\AiKnowledgeImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class AiKnowledgeImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_markdown_import_creates_draft_chunks(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent(
            'faq.md',
            "# SMS A2P\nDream Digital gere les flux SMS A2P avec supervision.",
        );

        $source = app(AiKnowledgeImporter::class)->import($file, [
            'title' => 'FAQ SMS',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'created_by_id' => null,
        ]);

        $this->assertSame(AiKnowledgeSource::TYPE_MARKDOWN, $source->type);
        $this->assertSame('FAQ SMS', $source->title);
        Storage::disk('local')->assertExists($source->stored_path);

        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'ai_knowledge_source_id' => $source->id,
            'status' => 'draft',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
        ]);
    }

    public function test_csv_import_maps_question_answer_rows(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent(
            'coverage.csv',
            "question,answer,category,country,locale\n\"Quels pays?\",\"RDC CI Congo\",coverage,global,fr\n",
        );

        $source = app(AiKnowledgeImporter::class)->import($file, [
            'title' => 'Coverage',
            'locale' => 'en',
            'country_code' => 'ci',
            'category' => 'general',
            'created_by_id' => null,
        ]);

        $chunk = AiKnowledgeChunk::query()->where('ai_knowledge_source_id', $source->id)->firstOrFail();

        $this->assertSame(AiKnowledgeSource::TYPE_CSV, $source->type);
        $this->assertStringContainsString('Quels pays?', $chunk->content);
        $this->assertStringContainsString('RDC CI Congo', $chunk->content);
        $this->assertSame('coverage', $chunk->category);
        $this->assertSame('fr', $chunk->locale);
        $this->assertSame('global', $chunk->country_code);
    }

    public function test_pdf_import_creates_draft_chunks(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent('guide.pdf', $this->minimalPdf('Dream Digital PDF Knowledge'));

        $source = app(AiKnowledgeImporter::class)->import($file, [
            'title' => 'PDF Guide',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'guide',
            'created_by_id' => null,
        ]);

        $this->assertSame(AiKnowledgeSource::TYPE_PDF, $source->type);
        $this->assertGreaterThan(0, $source->chunks->count());
        $this->assertDatabaseHas('ai_knowledge_chunks', [
            'ai_knowledge_source_id' => $source->id,
            'status' => 'draft',
            'category' => 'guide',
        ]);
    }

    public function test_chunker_splits_unbroken_tokens_that_exceed_max_chars(): void
    {
        $chunks = app(AiKnowledgeChunker::class)->chunks(str_repeat('A', 1300), 1200);

        $this->assertGreaterThan(1, count($chunks));

        foreach ($chunks as $chunk) {
            $this->assertLessThanOrEqual(1200, mb_strlen($chunk));
        }
    }

    public function test_failed_pdf_import_removes_stored_file_and_rolls_back_database_rows(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent('broken.pdf', 'this is not a valid pdf');

        $exception = null;

        try {
            app(AiKnowledgeImporter::class)->import($file, [
                'title' => 'Broken PDF',
                'locale' => 'fr',
                'country_code' => 'global',
                'category' => 'guide',
                'created_by_id' => null,
            ]);
        } catch (\Throwable $caught) {
            $exception = $caught;
        }

        $this->assertNotNull($exception, 'Expected invalid PDF import to fail.');
        $this->assertDatabaseCount('ai_knowledge_sources', 0);
        $this->assertDatabaseCount('ai_knowledge_chunks', 0);
        $this->assertSame([], Storage::disk('local')->allFiles('ai-knowledge-sources'));
    }

    public function test_unsupported_file_type_is_rejected(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent('faq.docx', 'not supported');

        $this->expectException(InvalidArgumentException::class);

        app(AiKnowledgeImporter::class)->import($file, [
            'title' => 'FAQ',
            'locale' => 'fr',
            'country_code' => 'global',
            'category' => 'faq',
            'created_by_id' => null,
        ]);
    }

    private function minimalPdf(string $text): string
    {
        $stream = "BT /F1 24 Tf 100 700 Td ({$text}) Tj ET";
        $objects = [
            "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n",
            "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n",
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>\nendobj\n",
            "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n",
            "5 0 obj\n<< /Length ".strlen($stream)." >>\nstream\n{$stream}\nendstream\nendobj\n",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }
}
