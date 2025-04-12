<?php

namespace Src\LLM\Application\Services;

use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Src\LLM\Domain\DTOs\LLMResponseDTO;

class LLMService
{
    public function __construct(
        private readonly LLMRepositoryInterface $repository
    ) {}

    public function generate(string $prompt, string $model, array $options = []): LLMResponseDTO
    {
        $response = $this->repository->generate($prompt, $model, $options);
        return LLMResponseDTO::fromArray($response);
    }

    public function getModels(): array
    {
        return $this->repository->getModels();
    }

    public function getModel(string $modelName): array
    {
        return $this->repository->getModel($modelName);
    }
}
