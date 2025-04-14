<?php

namespace Src\LLM\Application\Services;

use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Src\LLM\Domain\DTOs\LLMResponseDTO;
use Illuminate\Support\Facades\Log;

class LLMService
{
    public function __construct(
        private readonly LLMRepositoryInterface $repository
    ) {}

    public function generate(string $prompt, array $options = []): LLMResponseDTO
    {
        try {
            $response = $this->repository->generate($prompt, $options);
            return LLMResponseDTO::fromArray($response);
        } catch (\Exception $e) {
            Log::error('Error en servicio LLM durante generación', [
                'error' => $e->getMessage(),
                'prompt' => $prompt
            ]);
            throw $e;
        }
    }

    public function getEmbedding(string $text): LLMResponseDTO
    {
        try {
            $response = $this->repository->getEmbedding($text);
            return LLMResponseDTO::fromArray($response);
        } catch (\Exception $e) {
            Log::error('Error en servicio LLM durante obtención de embedding', [
                'error' => $e->getMessage(),
                'text' => $text
            ]);
            throw $e;
        }
    }

    public function getModels(): array
    {
        try {
            return $this->repository->getModels();
        } catch (\Exception $e) {
            Log::error('Error en servicio LLM durante obtención de modelos', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getModel(string $modelName): array
    {
        try {
            return $this->repository->getModel($modelName);
        } catch (\Exception $e) {
            Log::error('Error en servicio LLM durante obtención de modelo', [
                'error' => $e->getMessage(),
                'model' => $modelName
            ]);
            throw $e;
        }
    }

    public function analyzeImage(string $imagePath, string $prompt, array $options = []): LLMResponseDTO
    {
        try {
            $response = $this->repository->analyzeImage($imagePath, $prompt, $options);
            return LLMResponseDTO::fromArray($response);
        } catch (\Exception $e) {
            Log::error('Error en servicio LLM durante análisis de imagen', [
                'error' => $e->getMessage(),
                'image_path' => $imagePath,
                'prompt' => $prompt
            ]);
            throw $e;
        }
    }

    public function chat(array $messages, array $options = []): LLMResponseDTO
    {
        try {
            $response = $this->repository->chat($messages, $options);
            return LLMResponseDTO::fromArray($response);
        } catch (\Exception $e) {
            Log::error('Error en servicio LLM durante chat', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages)
            ]);
            throw $e;
        }
    }
}
