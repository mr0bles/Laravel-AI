<?php

namespace Src\LLM\Infrastructure\Repositories;

use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

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
     * @param array $options
     * @return array
     * @throws \RuntimeException
     */
    public function generate(string $prompt, array $options = []): array
    {
        try {
            // Asegurarnos de que no se use el modelo de embeddings para generación
            $model = $options['model'] ?? $this->defaultModel;
            if ($model === $this->embeddingModel) {
                $model = $this->defaultModel;
            }
            
            $response = Http::post("{$this->baseUrl}/api/generate", [
                'model' => $model,
                'prompt' => $prompt,
                'options' => $options,
                'stream' => false
            ]);

            if (!$response->successful()) {
                Log::error('LLM API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \RuntimeException('Error al comunicarse con el servicio LLM');
            }

            $result = $response->json();
            $result['model'] = $model; // Añadir el modelo usado a la respuesta
            
            return $result;

        } catch (\Exception $e) {
            Log::error('Error en el servicio LLM', [
                'error' => $e->getMessage(),
                'prompt' => $prompt,
                'model' => $model ?? $this->defaultModel
            ]);
            throw $e;
        }
    }

    /**
     * @param string $text
     * @return array
     * @throws \RuntimeException
     */
    public function getEmbedding(string $text): array
    {
        try {
            $response = Http::post("{$this->baseUrl}/api/embeddings", [
                'model' => $this->embeddingModel,
                'prompt' => $text
            ]);

            if (!$response->successful()) {
                Log::error('LLM API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \RuntimeException('Error al obtener el embedding');
            }

            return $response->json('embedding', []);

        } catch (\Exception $e) {
            Log::error('Error al obtener embedding', [
                'error' => $e->getMessage(),
                'text' => $text,
                'model' => $this->embeddingModel
            ]);
            throw $e;
        }
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getModels(): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/api/tags");

            if (!$response->successful()) {
                Log::error('LLM API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \RuntimeException('Error al obtener los modelos disponibles');
            }

            return $response->json('models', []);

        } catch (\Exception $e) {
            Log::error('Error al obtener modelos del servicio LLM', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * @param string $modelName
     * @return array
     * @throws \RuntimeException
     */
    public function getModel(string $modelName): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/api/show", [
                'name' => $modelName
            ]);

            if (!$response->successful()) {
                Log::error('LLM API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'model' => $modelName
                ]);
                throw new \RuntimeException('Error al obtener información del modelo');
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Error al obtener información del modelo', [
                'error' => $e->getMessage(),
                'model' => $modelName
            ]);
            throw $e;
        }
    }
} 