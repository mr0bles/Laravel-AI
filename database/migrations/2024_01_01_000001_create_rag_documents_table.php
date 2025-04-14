<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

return new class extends Migration
{
    public function up(): void
    {
        // Crear la extensión pgvector si no existe
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create('rag_documents', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->jsonb('metadata')->default('{}');
            $table->vector('embedding', Config::get('services.ollama.embedding_dimensions', 768));
            $table->timestamps();
        });

        // Crear el índice para búsqueda por similitud
        DB::statement('CREATE INDEX ON rag_documents USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)');
    }

    public function down(): void
    {
        Schema::dropIfExists('rag_documents');
    }
}; 