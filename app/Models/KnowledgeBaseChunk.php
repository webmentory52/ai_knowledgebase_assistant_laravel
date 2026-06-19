<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseChunk extends Model
{
    protected $connection = 'pgsql';

    protected $fillable = [
        'knowledge_base_item_id',
        'chunk_index',
        'chunk_content',
        'source',
        'embedding',
        'metadata',
    ];

    protected $casts = [
        'embedding' => 'array',
        'metadata' => 'array',
    ];
}
