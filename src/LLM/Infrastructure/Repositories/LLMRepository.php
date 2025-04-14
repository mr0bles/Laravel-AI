<?php

namespace Src\LLM\Infrastructure\Repositories;

use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class LLMRepository implements LLMRepositoryInterface
{
    private string $baseUrl;
    private string $defaultModel;
    private string $embeddingModel;

    public function __construct()
    {
        $this->baseUrl = Config::get('services.ollama.url', 'http://localhost:11434');
        $this->defaultModel = Config::get('services.ollama.default_model', 'deepseek-coder-v2:lite');
        $this->embeddingModel = Config::get('services.ollama.embedding_model', 'nomic-embed-text:latest');
    }

    /**
     * @param string $prompt
     * @param array{model?: string, temperature?: float, top_p?: float} $options
     * @return array{response: string, model: string, created_at: string}
     * @throws RuntimeException
     */
    public function generate(string $prompt, array $options = []): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/generate", [
                'model' => $options['model'] ?? $this->defaultModel,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => $options['temperature'] ?? 0.7,
                    'top_p' => $options['top_p'] ?? 0.9
                ]
            ]);

            if (!$response->successful()) {
                throw new RuntimeException('Error al generar texto: ' . $response->body());
            }

            $data = $response->json();
            
            if (!is_array($data)) {
                throw new RuntimeException('La respuesta del modelo no es un array válido');
            }

            if (!isset($data['response'])) {
                throw new RuntimeException('La respuesta del modelo no contiene el campo "response"');
            }

            return [
                'response' => $data['response'],
                'model' => $data['model'] ?? $this->defaultModel,
                'created_at' => $data['created_at'] ?? now()->toIso8601String()
            ];
        } catch (\Exception $e) {
            Log::error('Error en repositorio LLM durante generación', [
                'error' => $e->getMessage(),
                'prompt' => $prompt,
                'options' => $options
            ]);
            throw new RuntimeException('Error al generar texto: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $text
     * @return array<float>
     * @throws RuntimeException
     */
    public function getEmbedding(string $text): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/embeddings", [
                'model' => $this->embeddingModel,
                'prompt' => $text
            ]);

            if (!$response->successful()) {
                throw new RuntimeException('Error al obtener embedding: ' . $response->body());
            }

            $data = $response->json();
            return $data['embedding'];
        } catch (\Exception $e) {
            Log::error('Error en repositorio LLM durante obtención de embedding', [
                'error' => $e->getMessage(),
                'text' => $text
            ]);
            throw new RuntimeException('Error al obtener embedding: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return array<array{name: string, size: string}>
     * @throws RuntimeException
     */
    public function getModels(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/api/tags");

            if (!$response->successful()) {
                throw new RuntimeException('Error al obtener modelos: ' . $response->body());
            }

            $data = $response->json();
            return $data['models'];
        } catch (\Exception $e) {
            Log::error('Error en repositorio LLM durante obtención de modelos', [
                'error' => $e->getMessage()
            ]);
            throw new RuntimeException('Error al obtener modelos: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param string $modelName
     * @return array{name: string, size: string, parameters: array{temperature: float, top_p: float}}
     * @throws RuntimeException
     */
    public function getModel(string $modelName): array
    {
        try {
            $url = "{$this->baseUrl}/api/show?name={$modelName}";
            $response = Http::get($url);

            if (!$response->successful()) {
                throw new RuntimeException('Error al obtener información del modelo: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error en repositorio LLM durante obtención de modelo', [
                'error' => $e->getMessage(),
                'model' => $modelName
            ]);
            throw new RuntimeException('Error al obtener información del modelo: ' . $e->getMessage(), 0, $e);
        }
    }
}
