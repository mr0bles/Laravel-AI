<?php

declare(strict_types=1);

namespace Src\LLM\Application\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Src\LLM\Application\DTOs\LLMResponseDTO;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;

readonly class LLMService
{
    public function __construct(
        private LLMRepositoryInterface $repository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function generate(string $prompt, array $options = []): LLMResponseDTO
    {
        try {
            $response = $this->repository->generate($prompt, $options);
            return LLMResponseDTO::fromArray($response);
        } catch (Exception $e) {
            Log::error('Error en servicio LLM durante generación', [
                'error' => $e->getMessage(),
                'prompt' => $prompt
            ]);
            throw $e;
        }
    }

    public function getEmbedding(string $prompt): LLMResponseDTO
    {
        try {
            $response = $this->repository->getEmbedding($prompt);
            return LLMResponseDTO::fromArray($response);
        } catch (Exception $e) {
            Log::error('Error en servicio LLM durante obtención de embedding', [
                'error' => $e->getMessage(),
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
        } catch (Exception $e) {
            Log::error('Error en servicio LLM durante chat', [
                'error' => $e->getMessage(),
                'messages_count' => count($messages)
            ]);
            throw $e;
        }
    }

    public function analyzeImage(string $imagePath, string $prompt, array $options = []): LLMResponseDTO
    {
        try {
            $response = $this->repository->analyzeImage($imagePath, $prompt, $options);
            return LLMResponseDTO::fromArray($response);
        } catch (Exception $e) {
            Log::error('Error en servicio LLM durante análisis de imagen', [
                'error' => $e->getMessage(),
                'image_path' => $imagePath,
                'prompt' => $prompt
            ]);
            throw $e;
        }
    }


    public function getModels(): array
    {
        try {
            return $this->repository->getModels();
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            Log::error('Error en servicio LLM durante obtención de modelo', [
                'error' => $e->getMessage(),
                'model' => $modelName
            ]);
            throw $e;
        }
    }
}
