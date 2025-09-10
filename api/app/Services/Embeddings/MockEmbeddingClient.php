<?php

declare(strict_types=1);

namespace App\Services\Embeddings;

class MockEmbeddingClient implements EmbeddingClient
{
    public function embed(string $text): array
    {
        // Deterministic mock: hash to pseudo-random small vector for dev
        $dim = 16; // keep small for mock; DB uses 1536 in prod
        $hash = md5($text);
        $vector = [];
        for ($i = 0; $i < $dim; $i++) {
            $pair = substr($hash, ($i * 2) % 32, 2);
            $vector[] = (hexdec($pair) % 100) / 100.0;
        }
        return $vector;
    }
}

