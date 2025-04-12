<?php

namespace Src\LLM\Infrastructure\Repositories;

use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LLMRepository implements LLMRepositoryInterface
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
    }

    /**
     * @param string $prompt
     * @param string $model
     * @param array $options
     * @return array
     * @throws \RuntimeException
     */
    public function generate(string $prompt, string $model, array $options = []): array
    {
        try {
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

            return $response->json();

        } catch (\Exception $e) {
            Log::error('Error en el servicio LLM', [
                'error' => $e->getMessage(),
                'prompt' => $prompt,
                'model' => $model
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