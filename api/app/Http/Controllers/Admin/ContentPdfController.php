<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Content\Models\ContentPdf;
use App\Domain\Content\Models\ContentPdfImage;
use App\Domain\Content\Models\ContentPdfPage;
use App\Jobs\ProcessPdfUpload;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ContentPdfController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $language = $request->string('language')->toString();
        $status = $request->string('status')->toString();
        $perPage = (int) $request->integer('per_page', 15);

        $items = ContentPdf::query()
            ->when($q !== '', fn ($qBuilder) => $qBuilder->where('name', 'ILIKE', "%$q%"))
            ->when($language !== '', fn ($qb) => $qb->where('language', $language))
            ->when($status !== '', fn ($qb) => $qb->where('processing_status', $status))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'language' => ['nullable', 'string', 'max:10'],
            'is_active' => ['sometimes', 'boolean'],
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:51200'],
        ]);

        $path = $request->file('pdf')->store('content_library/pdf');

        $pdf = ContentPdf::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'language' => $data['language'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'processing_status' => 'pending',
            'file_path' => $path,
        ]);

        ProcessPdfUpload::dispatch((string) $pdf->id);

        return response()->json($pdf, 201);
    }

    public function show(string $id)
    {
        $pdf = ContentPdf::with(['pages' => fn ($q) => $q->orderBy('page_number'), 'images'])->findOrFail($id);

        return response()->json($pdf);
    }

    public function download(string $id)
    {
        $pdf = ContentPdf::findOrFail($id);
        $fullPath = storage_path('app/'.$pdf->file_path);
        abort_unless(file_exists($fullPath), 404);

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($fullPath).'"',
        ]);
    }

    public function togglePage(string $pdfId, string $pageId)
    {
        $page = ContentPdfPage::where('content_pdf_id', $pdfId)->findOrFail($pageId);
        $page->is_active = ! $page->is_active;
        $page->save();

        return response()->json($page);
    }

    public function toggleImage(string $pdfId, string $imageId)
    {
        $image = ContentPdfImage::where('content_pdf_id', $pdfId)->findOrFail($imageId);
        $image->is_active = ! $image->is_active;
        $image->save();

        return response()->json($image);
    }
}
