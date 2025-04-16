<?php

declare(strict_types=1);

namespace Src\RAG\Application\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Src\RAG\Domain\Repositories\RAGRepositoryInterface;
use Src\RAG\Domain\ValueObjects\Document;

readonly class RAGService
{
    public function __construct(
        private RAGRepositoryInterface $ragRepository
    )
    {
    }

    public function search(string $query, array $options = []): array
    {
        try {
            return $this->ragRepository->search($query, $options);
        } catch (Exception $e) {
            Log::error('Error en servicio RAG durante búsqueda', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);
            throw $e;
        }
    }

    public function store(array $documentData): array
    {
        try {
            $document = Document::fromArray($documentData);
            return $this->ragRepository->store($document->toArray());
        } catch (Exception $e) {
            Log::error('Error en servicio RAG durante almacenamiento', [
                'error' => $e->getMessage(),
                'document' => $documentData
            ]);
            throw $e;
        }
    }

    public function delete(string $id): void
    {
        try {
            $this->ragRepository->delete($id);
        } catch (Exception $e) {
            Log::error('Error en servicio RAG durante eliminación', [
                'error' => $e->getMessage(),
                'id' => $id
            ]);
            throw $e;
        }
    }
}
