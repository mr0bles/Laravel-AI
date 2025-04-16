<?php

declare(strict_types=1);

namespace Src\LLM\Infrastructure\Repositories;

use Cloudstudio\Ollama\Ollama;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;

class OllamaLLMRepository implements LLMRepositoryInterface
{
    private const  SYSTEM_PROMPT = 'Eres un agente de IA especializado, da la respuesta mas corta que puedas.';
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

            $response = $this->ollama->agent($this->getSystemPrompt())
                ->prompt($prompt)
                ->model($model)
                ->options([
                    'temperature' => (float)$temperature,
                    'top_p' => (float)$topP
                ])
                ->stream(false)
                ->ask();

            if (!isset($response['response'])) {
                Log::error('Error', [
                    'response' => $response,
                ]);
                if (isset($response['error'])) {
                    throw new RuntimeException('Invalid response from Ollama:' . $response['error']);
                }
                throw new RuntimeException('Invalid response from Ollama: does not contain the response field');
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
        } catch (Exception|GuzzleException $e) {
            throw new RuntimeException('Error generating text: ' . $e->getMessage(), 0, $e);
        }
    }


    public function getEmbedding(string $prompt): array
    {
        try {
            $model = Config::get('llm.embedding_model', 'nomic-embed-text');

            $response = $this->ollama->model($model)->embeddings($prompt);

            return [
                'response' => $response['embedding'],
                'metadata' => [
                    'model' => $model,
                    'created_at' => now()->toIso8601String()
                ]
            ];
        } catch (Exception|GuzzleException $e) {
            throw new RuntimeException('Error generating embedding: ' . $e->getMessage(), 0, $e);
        }
    }

    public function chat(array $messages, array $options = []): array
    {
        try {
            $model = $options['model'] ?? Config::get('ollama-laravel.model');
            $temperature = $options['temperature'] ?? Config::get('ollama-laravel.temperature');
            $topP = $options['top_p'] ?? Config::get('ollama-laravel.top_p');

            $response = $this->ollama->agent($this->getSystemPrompt())
                ->model($model)
                ->options([
                    'temperature' => (float)$temperature,
                    'top_p' => (float)$topP
                ])
                ->stream(false)
                ->chat($messages);

            if (!isset($response['message']['content'])) {
                throw new RuntimeException('Invalid response from Ollama: does not contain the message.content field');
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
        } catch (Exception $e) {
            throw new RuntimeException('Error in the chat: ' . $e->getMessage(), 0, $e);
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
                    'temperature' => (float)$temperature,
                    'top_p' => (float)$topP
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
        } catch (Exception $e) {
            throw new RuntimeException('Error al analizar imagen: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getModels(): array
    {
        try {
            $response = $this->ollama->models();
            if (!isset($response['models'])) {
                throw new RuntimeException('Invalid response from Ollama: does not contain the list of models');
            }
            return $response['models'];
        } catch (Exception $e) {
            throw new RuntimeException('Error getting models: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getModel(string $modelName): array
    {
        try {
            $response = $this->ollama->model($modelName)->show();
            if (isset($response['error'])) {
                throw new RuntimeException('Error getting models information: ' . $response['error']);
            }
            return $response;
        } catch (Exception $e) {
            throw new RuntimeException('Error al obtener información del modelo: ' . $e->getMessage(), 0, $e);
        }
    }

    private function getSystemPrompt(): string
    {
        return self::SYSTEM_PROMPT;
    }
}
