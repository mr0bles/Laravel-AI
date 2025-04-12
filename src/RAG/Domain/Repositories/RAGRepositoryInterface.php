<?php

namespace Src\RAG\Domain\Repositories;

use Src\RAG\Domain\ValueObjects\Document;

interface RAGRepositoryInterface
{
    /**
     * Busca documentos relacionados con la consulta
     *
     * @param string $query
     * @return array
     */
    public function search(string $query): array;

    /**
     * Almacena un nuevo documento
     *
     * @param array $document
     * @return array
     */
    public function store(array $document): array;

    /**
     * Elimina un documento por su ID
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): void;
} 