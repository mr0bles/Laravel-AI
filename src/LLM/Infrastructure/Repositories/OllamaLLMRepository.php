<?php

declare(strict_types=1);

namespace Src\LLM\Infrastructure\Repositories;

use Cloudstudio\Ollama\Ollama;
use Illuminate\Support\Facades\Log;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class OllamaLLMRepository implements LLMRepositoryInterface
{
    private Ollama $ollama;

    public function __construct(Ollama $ollama)
    {
        $this->ollama = $ollama;
    }

    public function generate(string $prompt, array $options = []): array
    {
        try {
            $model = $options['model'] ?? Config::get('ollama-laravel.model');
            $temperature = $options['temperature'] ?? Config::get('ollama-laravel.temperature');
            $topP = $options['top_p'] ?? Config::get('ollama-laravel.top_p');

            $response = $this->ollama->agent('You are a helpful AI assistant.')
                ->prompt($prompt)
                ->model($model)
                ->options([
                    'temperature' => $temperature,
                    'top_p' => $topP
                ])
                ->stream(false)
                ->ask();

            if (!isset($response['response'])) {
                throw new RuntimeException('Respuesta inválida de Ollama: no contiene el campo response');
            }

            return [
                'response' => $response['response'],
                'metadata' => [
                    'model' => $model,
                    'created_at' => now()->toIso8601String(),
                    'temperature' => $temperature,
                    'top_p' => $topP
                ]
            ];
        } catch (\Exception $e) {
            throw new RuntimeException('Error al generar texto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getEmbedding(string $text): array
    {
        try {
            $model = Config::get('ollama-laravel.embedding_model');
            $response = $this->ollama->model($model)->embeddings($text);

            if (!isset($response['embedding'])) {
                throw new RuntimeException('Respuesta inválida de Ollama: no contiene el campo embedding');
            }

            return [
                'embedding' => $response['embedding'],
                'metadata' => [
                    'model' => $model,
                    'created_at' => now()->toIso8601String()
                ]
            ];
        } catch (\Exception $e) {
            throw new RuntimeException('Error al obtener embedding: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getModels(): array
    {
        try {
            $response = $this->ollama->models();
            if (!isset($response['models'])) {
                throw new RuntimeException('Respuesta inválida de Ollama: no contiene la lista de modelos');
            }
            return $response['models'];
        } catch (\Exception $e) {
            throw new RuntimeException('Error al obtener modelos: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getModel(string $modelName): array
    {
        try {
            $response = $this->ollama->model($modelName);
            if (!isset($response['name'])) {
                throw new RuntimeException('Respuesta inválida de Ollama: no contiene información del modelo');
            }
            return [
                'name' => $response['name'],
                'modified_at' => $response['modified_at'] ?? now()->toIso8601String(),
                'size' => $response['size'] ?? 0
            ];
        } catch (\Exception $e) {
            throw new RuntimeException('Error al obtener información del modelo: ' . $e->getMessage(), 0, $e);
        }
    }

    public function analyzeImage(string $imagePath, string $prompt, array $options = []): array
    {
        try {
            $model = $options['model'] ?? Config::get('ollama-laravel.model');
            $temperature = $options['temperature'] ?? Config::get('ollama-laravel.temperature');
            $topP = $options['top_p'] ?? Config::get('ollama-laravel.top_p');

            $response = $this->ollama->model($model)
                ->prompt($prompt)
                ->image($imagePath)
                ->options([
                    'temperature' => $temperature,
                    'top_p' => $topP
                ])
                ->ask();

            if (!isset($response['response'])) {
                throw new RuntimeException('Respuesta inválida de Ollama: no contiene el campo response');
            }

            return [
                'response' => $response['response'],
                'metadata' => [
                    'model' => $model,
                    'created_at' => now()->toIso8601String(),
                    'temperature' => $temperature,
                    'top_p' => $topP
                ]
            ];
        } catch (\Exception $e) {
            throw new RuntimeException('Error al analizar imagen: ' . $e->getMessage(), 0, $e);
        }
    }

    public function chat(array $messages, array $options = []): array
    {
        try {
            $model = $options['model'] ?? Config::get('ollama-laravel.model');
            $temperature = $options['temperature'] ?? Config::get('ollama-laravel.temperature');
            $topP = $options['top_p'] ?? Config::get('ollama-laravel.top_p');

            $response = $this->ollama->agent('You are a helpful AI assistant.')
                ->model($model)
                ->options([
                    'temperature' => $temperature,
                    'top_p' => $topP
                ])
                ->stream(false)
                ->chat($messages);

            if (!isset($response['message']['content'])) {
                throw new RuntimeException('Respuesta inválida de Ollama: no contiene el campo message.content');
            }

            return [
                'response' => $response['message']['content'],
                'metadata' => [
                    'model' => $model,
                    'created_at' => now()->toIso8601String(),
                    'temperature' => $temperature,
                    'top_p' => $topP
                ]
            ];
        } catch (\Exception $e) {
            throw new RuntimeException('Error en el chat: ' . $e->getMessage(), 0, $e);
        }
    }

    private function getSystemPrompt(): string
    {
        return 'Eres un agente especializado llamado' . \config('app.name'). ', da la respuesta mas corta que puedas.';
    }
}
