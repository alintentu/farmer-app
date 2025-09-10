<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentPdf extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'content_pdfs';

    protected $fillable = [
        'name', 'description', 'language', 'is_active', 'processing_status', 'file_path', 'tokens_used', 'processed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(ContentPdfPage::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ContentPdfImage::class);
    }
}

