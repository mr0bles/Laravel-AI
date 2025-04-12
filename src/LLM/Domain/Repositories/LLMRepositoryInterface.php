<?php

namespace Src\LLM\Domain\Repositories;

interface LLMRepositoryInterface
{
    /**
     * Genera una respuesta usando el modelo de LLM
     *
     * @param string $prompt
     * @param string $model
     * @param array $options
     * @return array
     */
    public function generate(string $prompt, string $model, array $options = []): array;

    /**
     * Obtiene todos los modelos disponibles
     *
     * @return array
     */
    public function getModels(): array;

    /**
     * Obtiene información sobre un modelo específico
     *
     * @param string $modelName
     * @return array
     */
    public function getModel(string $modelName): array;
} 