<?php

declare(strict_types=1);

namespace Src\RAG\Application\Services;

use Src\RAG\Domain\Repositories\RAGRepositoryInterface;
use Src\RAG\Domain\ValueObjects\Document;
use Illuminate\Support\Facades\Log;

class RAGService
{
    public function __construct(
        private readonly RAGRepositoryInterface $ragRepository
    ) {}

    /**
     * Busca documentos similares a la consulta dada
     *
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        try {
            Log::info('Iniciando búsqueda RAG', ['query' => $query]);
            $results = $this->ragRepository->search($query);
            Log::info('Búsqueda RAG completada', ['results' => $results]);
            return $results;
        } catch (\Exception $e) {
            Log::error('Error en servicio RAG durante búsqueda', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);
            throw $e;
        }
    }

    /**
     * Almacena un nuevo documento
     *
     * @param array $documentData
     * @return array
     */
    public function store(array $documentData): array
    {
        try {
            Log::info('Iniciando almacenamiento de documento', ['document' => $documentData]);
            $document = Document::fromArray($documentData);
            $result = $this->ragRepository->store($document->toArray());
            Log::info('Documento almacenado exitosamente', ['id' => $result['id'] ?? null]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Error en servicio RAG durante almacenamiento', [
                'error' => $e->getMessage(),
                'document' => $documentData
            ]);
            throw $e;
        }
    }

    /**
     * Elimina un documento por su ID
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        try {
            Log::info('Iniciando eliminación de documento', ['id' => $id]);
            $this->ragRepository->delete($id);
            Log::info('Documento eliminado exitosamente', ['id' => $id]);
        } catch (\Exception $e) {
            Log::error('Error en servicio RAG durante eliminación', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw $e;
        }
    }
} 