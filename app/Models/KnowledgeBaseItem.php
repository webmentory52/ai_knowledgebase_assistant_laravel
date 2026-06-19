<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseItem extends Model
{
    protected $fillable = [
      'knowledge_base_category_id',
      'title',
      'content'
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseCategory::class, 'knowledge_base_category_id');
    }
}
