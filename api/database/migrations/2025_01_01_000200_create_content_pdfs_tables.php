<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_pdfs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('language', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('processing_status')->default('pending'); // pending|processing|complete|failed
            $table->string('file_path');
            $table->unsignedInteger('tokens_used')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('content_pdf_pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('content_pdf_id');
            $table->unsignedInteger('page_number');
            $table->longText('text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('embedding_status')->default('pending'); // pending|processing|complete|failed
            $table->unsignedInteger('tokens_used')->default(0);
            $table->timestamps();

            $table->foreign('content_pdf_id')->references('id')->on('content_pdfs')->cascadeOnDelete();
        });

        // Add vector type column separately (Blueprint lacks native helper)
        DB::statement('ALTER TABLE content_pdf_pages ADD COLUMN embedding vector(1536)');

        Schema::create('content_pdf_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('content_pdf_id');
            $table->unsignedInteger('page_number');
            $table->string('image_path');
            $table->boolean('is_active')->default(true);
            $table->string('embedding_status')->default('pending');
            $table->unsignedInteger('tokens_used')->default(0);
            $table->timestamps();

            $table->foreign('content_pdf_id')->references('id')->on('content_pdfs')->cascadeOnDelete();
        });

        DB::statement('ALTER TABLE content_pdf_images ADD COLUMN embedding vector(1536)');

        Schema::create('content_vectors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('content_pdf_id');
            $table->string('source_type'); // page|image
            $table->uuid('source_id'); // FK to page/image
            $table->unsignedInteger('tokens_used')->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('content_pdf_id')->references('id')->on('content_pdfs')->cascadeOnDelete();
        });
        DB::statement('ALTER TABLE content_vectors ADD COLUMN embedding vector(1536)');
    }

    public function down(): void
    {
        Schema::dropIfExists('content_vectors');
        Schema::dropIfExists('content_pdf_images');
        Schema::dropIfExists('content_pdf_pages');
        Schema::dropIfExists('content_pdfs');
    }
};
