<?php

namespace Src\RAG\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RAGDocument extends Model
{
    protected $table = 'rag_documents';

    protected $fillable = [
        'content',
        'metadata',
        'embedding'
    ];

    protected $casts = [
        'metadata' => 'array',
        'embedding' => 'array'
    ];

    /**
     * Obtiene el contenido del documento
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Obtiene los metadatos del documento
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata ?? [];
    }

    /**
     * Obtiene el embedding del documento
     *
     * @return array
     */
    public function getEmbedding(): array
    {
        return $this->embedding ?? [];
    }

    /**
     * Establece el embedding del documento
     *
     * @param array $embedding
     * @return void
     */
    public function setEmbedding(array $embedding): void
    {
        $this->embedding = $embedding;
    }
} 