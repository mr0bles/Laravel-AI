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
}
