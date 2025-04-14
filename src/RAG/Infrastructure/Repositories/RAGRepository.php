<?php

declare(strict_types=1);

namespace Src\RAG\Infrastructure\Repositories;

use Src\RAG\Domain\Repositories\RAGRepositoryInterface;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Src\RAG\Infrastructure\Models\RAGDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RAGRepository implements RAGRepositoryInterface
{
    public function __construct(
        private readonly LLMRepositoryInterface $llmRepository
    ) {}

    public function search(string $query, array $options = []): array
    {
        try {
            // Obtener el embedding de la consulta
            $queryEmbedding = $this->getEmbedding($query);

            // Obtener documentos similares
            $similarDocuments = $this->findSimilarDocuments($queryEmbedding, $options);
            Log::info('Búsqueda RAG documentos', ['similarDocuments' => $similarDocuments]);

            // Generar respuesta usando el contexto
            $context = $this->buildContext($similarDocuments);
            $prompt = $this->buildPrompt($query, $context);

            // Usar las opciones de generación del RAG, asegurando que sean float
            $generationOptions = [
                'temperature' => (float)Config::get('rag.temperature', 0.7),
                'top_p' => (float)Config::get('rag.top_p', 0.9)
            ];

            $response = $this->llmRepository->generate($prompt, $generationOptions);

            return [
                'response' => $response['response'] ?? '',
                'context' => $context,
                'documents' => $similarDocuments->toArray()
            ];

        } catch (\Exception $e) {
            Log::error('Error en búsqueda RAG', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);
            throw $e;
        }
    }

    public function store(array $document): array
    {
        try {
            // 1. Obtener el embedding del contenido
            $embedding = $this->getEmbedding($document['content']);

            // 2. Crear el documento en la base de datos
            $ragDocument = RAGDocument::create([
                'content' => $document['content'],
                'metadata' => $document['metadata'] ?? [],
                'embedding' => $embedding
            ]);

            return [
                'id' => $ragDocument->id,
                'status' => 'stored',
                'timestamp' => Carbon::now()->toIso8601String()
            ];
        } catch (\Exception $e) {
            Log::error('Error al almacenar documento: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): void
    {
        try {
            RAGDocument::findOrFail($id)->delete();
            Log::info("Documento eliminado: {$id}");
        } catch (\Exception $e) {
            Log::error('Error al eliminar documento: ' . $e->getMessage());
            throw $e;
        }
    }

    private function getEmbedding(string $text): array
    {
        try {
            return $this->llmRepository->getEmbedding($text);
        } catch (\Exception $e) {
            Log::error('Error al obtener embedding: ' . $e->getMessage());
            throw $e;
        }
    }

    private function findSimilarDocuments(array $queryEmbedding, array $options = []): Collection
    {
        $similarityThreshold = $options['similarity_threshold'] ?? Config::get('rag.similarity_threshold');
        $maxResults = $options['max_results'] ?? Config::get('rag.max_results');

        // Convertir el array de embedding a una cadena SQL segura
        $embeddingString = implode(',', array_map(function($value) {
            return (float)$value;
        }, $queryEmbedding));

        return RAGDocument::select('*')
            ->selectRaw('1 - (embedding <=> ?::vector) as similarity', ["[{$embeddingString}]"])
            ->whereRaw('1 - (embedding <=> ?::vector) >= ?', ["[{$embeddingString}]", $similarityThreshold])
            ->orderBy('similarity', 'desc')
            ->limit($maxResults)
            ->get();
    }

    private function buildContext(Collection $documents): string
    {
        return $documents->map(function ($document) {
            return $document->getContent();
        })->join("\n\n");
    }

    private function buildPrompt(string $query, string $context): string
    {
        return "Contexto:\n{$context}\n\nPregunta: {$query}\n\nRespuesta:";
    }
}
