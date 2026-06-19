<?php

namespace App\Console\Commands;

use App\Models\KnowledgeBaseCategory;
use App\Models\KnowledgeBaseChunk;
use App\Services\TextChunker;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Enums\Lab;

#[Signature('kb:embed {--refresh : Delete existing embeddings first}')]
#[Description('Generate embeddings for knowledge base articles')]
class GenerateKnowledgeBaseEmbeddings extends Command
{
    /**
     * Execute the command.
     */
    public function handle(TextChunker $chunker)
    {
        if ($this->option('refresh')) {

            $this->warn('Deleting existing embeddings...');

            KnowledgeBaseChunk::truncate();
        }

        $categories = KnowledgeBaseCategory::with('knowledgeBaseItems')->get();

        $this->info(
            "Found {$categories->count()} categories"
        );

        foreach ($categories as $category) {
            foreach ($category->knowledgeBaseItems as $item) {
                $this->line("Processing: {$item->title}");

                $textForEmbedding = implode("\n\n", [
                    "Category: {$category->name}",
                    "Title: {$item->title}",
                    $item->content,
                ]);

                $chunks = $chunker->chunk($textForEmbedding, 100, 20);

                foreach ($chunks as $index => $chunk) {
                    $exists = KnowledgeBaseChunk::query()
                        ->where('knowledge_base_item_id', $item->id)
                        ->where('chunk_index', $index)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    try {
                        $embedding = Embeddings::for([$chunk])
                            ->dimensions(1536)
                            ->cache()
                            ->generate(Lab::OpenAI, 'text-embedding-3-small')
                            ->embeddings[0];

                        // Store embeddings
                        KnowledgeBaseChunk::create([
                            'knowledge_base_item_id' => $item->id,
                            'chunk_index' => $index,
                            'chunk_content' => $chunk,
                            'embedding' => $embedding,
                            'source' => $category->name,
                            'metadata' => [
                                'category_id' => $category->id,
                                'category_name' => $category->name,
                                'category_description' => $category->description,
                            ]
                        ]);
                    } catch (\Throwable $ex) {
                        $this->error("Failed: {$ex->getMessage()}");
                    }
                }
            }
        }

        $this->info('Knowledge base embeddings generated.');

        return self::SUCCESS;
    }
}
