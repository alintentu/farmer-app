<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentPdfPage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'content_pdf_pages';

    protected $fillable = [
        'content_pdf_id', 'page_number', 'text', 'is_active', 'embedding_status', 'tokens_used',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pdf(): BelongsTo
    {
        return $this->belongsTo(ContentPdf::class, 'content_pdf_id');
    }
}

