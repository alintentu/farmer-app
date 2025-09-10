<?php

declare(strict_types=1);

namespace App\Services\Embeddings;

use GuzzleHttp\Client;

class OpenAIEmbeddingClient implements EmbeddingClient
{
    public function __construct(
        private readonly Client $http,
        private readonly string $apiKey,
        private readonly string $model = 'text-embedding-3-small',
    ) {}

    public function embed(string $text): array
    {
        // Simple call; in dev this can be mocked
        $resp = $this->http->post('https://api.openai.com/v1/embeddings', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'input' => $text,
                'model' => $this->model,
            ],
            'timeout' => 20,
        ]);

        $data = json_decode((string) $resp->getBody(), true);

        return $data['data'][0]['embedding'] ?? [];
    }
}
