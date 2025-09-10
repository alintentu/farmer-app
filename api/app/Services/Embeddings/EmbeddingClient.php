<?php

declare(strict_types=1);

namespace App\Services\Embeddings;

interface EmbeddingClient
{
    /**
     * Generate an embedding vector for the given text.
     * Returns an array of floats.
     */
    public function embed(string $text): array;
}
