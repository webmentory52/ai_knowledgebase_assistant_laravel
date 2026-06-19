<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'pgsql';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('knowledge_base_chunks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('knowledge_base_item_id');
            $table->integer('chunk_index');
            $table->text('chunk_content');
            $table->string('source');
            $table->vector('embedding', dimensions: 1536);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique([
                'knowledge_base_item_id',
                'chunk_index'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_chunks');
    }
};
