<?php

namespace Database\Seeders;

use App\Models\KnowledgeBaseCategory;
use App\Models\KnowledgeBaseItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;


class KnowledgeBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = public_path('/seed-data/knowledge-base.json');

        if (! File::exists($path)) {
            $this->command->error(
                "knowledge-base.json not found."
            );

            return;
        }

        $data = json_decode(
            File::get($path),
            true
        );

        foreach ($data['categories'] as $categoryData) {
            $category = KnowledgeBaseCategory::create([
                'name' => $categoryData['name'],
                'description' => $categoryData['description'] ?? null,
                'is_active' => true,
            ]);

            foreach ($categoryData['items'] as $itemData) {
                KnowledgeBaseItem::create([
                    'knowledge_base_category_id' => $category->id,
                    'title' => $itemData['title'],
                    'content' => $itemData['content'],
                ]);
            }
        }
    }
}
