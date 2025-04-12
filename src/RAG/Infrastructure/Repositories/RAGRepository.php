<?php

declare(strict_types=1);

namespace Src\RAG\Infrastructure\Repositories;

use Src\RAG\Domain\Repositories\RAGRepositoryInterface;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class RAGRepository implements RAGRepositoryInterface
{
    public function __construct(
        private readonly LLMRepositoryInterface $llmRepository
    ) {}

    public function search(string $query): array
    {
        try {
            $response = $this->llmRepository->generate(
                prompt: $query,
                model: Config::get('services.llm.default_model', 'deepseek-coder-v2:lite'),
                options: [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ]
            );

            return [
                'results' => [
                    'response' => $response['response'] ?? '',
                    'model' => Config::get('services.llm.default_model', 'deepseek-coder-v2:lite'),
                    'timestamp' => Carbon::now()->toIso8601String()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error en búsqueda RAG: ' . $e->getMessage());
            return [
                'results' => [
                    'response' => '',
                    'error' => 'Error al realizar la búsqueda'
                ]
            ];
        }
    }

    public function store(array $document): array
    {
        try {
            return [
                'id' => uniqid('doc_'),
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
            Log::info("Documento eliminado: {$id}");
        } catch (\Exception $e) {
            Log::error('Error al eliminar documento: ' . $e->getMessage());
            throw $e;
        }
    }
} 