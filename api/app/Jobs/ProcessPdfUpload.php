<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Content\Models\ContentPdf;
use App\Domain\Content\Models\ContentPdfImage;
use App\Domain\Content\Models\ContentPdfPage;
use App\Services\Embeddings\EmbeddingClient;
use App\Services\Embeddings\MockEmbeddingClient;
use App\Services\Embeddings\OpenAIEmbeddingClient;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser; // requires package in composer

class ProcessPdfUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $pdfId) {}

    public function handle(): void
    {
        $pdf = ContentPdf::findOrFail($this->pdfId);
        $pdf->update(['processing_status' => 'processing']);

        try {
            $embeddings = $this->makeEmbeddingClient();
            $this->extractText($pdf, $embeddings);
            $this->extractImages($pdf, $embeddings);
            $pdf->update(['processing_status' => 'complete', 'processed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('PDF processing failed', ['id' => $pdf->id, 'error' => $e->getMessage()]);
            $pdf->update(['processing_status' => 'failed']);
            throw $e;
        }
    }

    private function makeEmbeddingClient(): EmbeddingClient
    {
        $driver = config('services.embeddings.driver', env('EMBEDDINGS_DRIVER', 'mock'));
        if ($driver === 'openai' && ($apiKey = env('OPENAI_API_KEY'))) {
            return new OpenAIEmbeddingClient(new HttpClient(), $apiKey, env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'));
        }
        return new MockEmbeddingClient();
    }

    private function extractText(ContentPdf $pdf, EmbeddingClient $embeddings): void
    {
        // Parse PDF text per page (placeholder; requires smalot/pdfparser or spatie/pdf-to-text)
        $parser = class_exists(PdfParser::class) ? new PdfParser() : null;
        $pages = [];
        if ($parser) {
            $document = $parser->parseFile(storage_path('app/' . $pdf->file_path));
            foreach ($document->getPages() as $i => $page) {
                $pages[] = [
                    'page_number' => $i + 1,
                    'text' => $page->getText(),
                ];
            }
        } else {
            // Fallback: store a single page with empty text in dev without deps
            $pages[] = ['page_number' => 1, 'text' => ''];
        }

        foreach ($pages as $p) {
            $page = ContentPdfPage::create([
                'content_pdf_id' => $pdf->id,
                'page_number' => $p['page_number'],
                'text' => $p['text'] ?? '',
                'is_active' => true,
                'embedding_status' => 'processing',
            ]);

            // Embed text (mockable)
            $vec = $embeddings->embed(mb_substr($page->text ?? '', 0, 8000));
            // Store vector via raw statement (Laravel lacks vector casting by default)
            $placeholders = implode(',', $vec);
            \DB::statement("UPDATE content_pdf_pages SET embedding = '[".$placeholders."]'::vector, embedding_status = 'complete' WHERE id = ?", [$page->id]);
        }
    }

    private function extractImages(ContentPdf $pdf, EmbeddingClient $embeddings): void
    {
        // Use Imagick to rasterize pages to images (if available)
        if (class_exists(\Imagick::class)) {
            $source = storage_path('app/' . $pdf->file_path);
            try {
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($source);
                $imagick->setImageFormat('png');

                foreach ($imagick as $i => $frame) {
                    $pageNum = $i + 1;
                    $path = 'content_library/pdf/extracted_images/page_' . $pdf->id . '_' . $pageNum . '.png';
                    $fullPath = storage_path('app/' . $path);
                    @mkdir(dirname($fullPath), 0775, true);
                    $frame->writeImage($fullPath);

                    $image = ContentPdfImage::create([
                        'content_pdf_id' => $pdf->id,
                        'page_number' => $pageNum,
                        'image_path' => $path,
                        'is_active' => true,
                        'embedding_status' => 'processing',
                    ]);

                    $vec = $embeddings->embed('image page ' . $pageNum . ' of ' . $pdf->name);
                    $placeholders = implode(',', $vec);
                    \DB::statement("UPDATE content_pdf_images SET embedding = '[".$placeholders."]'::vector, embedding_status = 'complete' WHERE id = ?", [$image->id]);
                }
            } catch (\Throwable $e) {
                // Non-fatal in dev
                \Log::warning('Imagick not available or failed: ' . $e->getMessage());
            }
        }
    }
}
