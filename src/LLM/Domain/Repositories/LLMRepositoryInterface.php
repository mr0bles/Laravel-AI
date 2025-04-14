<?php

namespace Src\LLM\Domain\Repositories;

interface LLMRepositoryInterface
{
    /**
     * Genera texto usando el modelo LLM
     *
     * @param string $prompt El prompt para generar el texto
     * @param array $options Opciones adicionales para la generación
     * @return array La respuesta del modelo
     * @throws \RuntimeException
     */
    public function generate(string $prompt, array $options = []): array;

    /**
     * Obtiene el embedding de un texto
     *
     * @param string $text El texto para obtener el embedding
     * @return array El vector de embedding
     * @throws \RuntimeException
     */
    public function getEmbedding(string $text): array;

    /**
     * Obtiene la lista de modelos disponibles
     *
     * @return array Lista de modelos
     * @throws \RuntimeException
     */
    public function getModels(): array;

    /**
     * Obtiene información de un modelo específico
     *
     * @param string $modelName Nombre del modelo
     * @return array Información del modelo
     * @throws \RuntimeException
     */
    public function getModel(string $modelName): array;
} 