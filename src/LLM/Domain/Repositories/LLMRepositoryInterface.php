<?php

declare(strict_types=1);

namespace Src\LLM\Domain\Repositories;

interface LLMRepositoryInterface
{
    /**
     * Genera una respuesta basada en un prompt
     *
     * @param string $prompt El texto de entrada para generar la respuesta
     * @param array{model?: string, temperature?: float, top_p?: float} $options Opciones adicionales para la generación
     * @return array{response: string, metadata: array{model: string, created_at: string, temperature: float, top_p: float}}
     */
    public function generate(string $prompt, array $options = []): array;

    /**
     * Obtiene el embedding de un texto
     *
     * @param string $prompt El texto para obtener su embedding
     * @return array{response: array<float>, metadata: array{model: string, created_at: string}}
     */
    public function getEmbedding(string $prompt): array;

    /**
     * Inicia una conversación con el modelo
     *
     * @param array<array{role: string, content: string}> $messages Historial de mensajes
     * @param array{model?: string, temperature?: float, top_p?: float} $options Opciones adicionales
     * @return array{response: string, metadata: array{model: string, created_at: string, temperature: float, top_p: float}}
     */
    public function chat(array $messages, array $options = []): array;

    /**
     * Analiza una imagen y genera una descripción
     *
     * @param string $imagePath Ruta de la imagen a analizar
     * @param string $prompt Prompt para el análisis de la imagen
     * @param array{model?: string, temperature?: float, top_p?: float} $options Opciones adicionales
     * @return array{response: string, metadata: array{model: string, created_at: string, temperature: float, top_p: float}}
     */
    public function analyzeImage(string $imagePath, string $prompt, array $options = []): array;

    /**
     * Obtiene la lista de modelos disponibles
     *
     * @return array<array{name: string, modified_at: string, size: int}>
     */
    public function getModels(): array;

    /**
     * Obtiene información de un modelo específico
     *
     * @param string $modelName El nombre del modelo a consultar
     * @return array
     */
    public function getModel(string $modelName): array;
}
