<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeBaseCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function knowledgeBaseItems() : HasMany
    {
        return $this->hasMany(KnowledgeBaseItem::class, 'knowledge_base_category_id');
    }
}
