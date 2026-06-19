<?php

namespace App\Services;

use App\Models\KnowledgeBaseChunk;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

class KnowledgeBaseRagService
{
    public function search(string $question) : Collection
    {
        $queryEmbedding = Embeddings::for([$question])
            ->dimensions(1536)
            ->generate(Lab::OpenAI, 'text-embedding-3-small')
            ->embeddings[0];

        return KnowledgeBaseChunk::query()
            ->whereVectorSimilarTo('embedding', $queryEmbedding, minSimilarity: 0.4)
            ->limit(10)
            ->get();
    }

    public function buildContext(Collection $chunks): string
    {
        return $chunks->map(function($chunk) {
            $category =
                $chunk->metadata['category_name']
                ?? 'General';

            $title =
                $chunk->metadata['title']
                ?? 'Untitled';

            return <<<TEXT
                [Category: {$category}]
                [Title: {$title}]

                {$chunk->chunk_content}
            TEXT;
        })->implode("\n\n---\n\n");
    }
}
