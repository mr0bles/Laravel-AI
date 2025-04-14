<?php

declare(strict_types=1);

namespace Tests\Unit\LLM\Infrastructure\Repositories;

use Tests\TestCase;
use Src\LLM\Infrastructure\Repositories\LLMRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use RuntimeException;

class LLMRepositoryTest extends TestCase
{
    private LLMRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
        config(['services.ollama.url' => 'http://ollama:11434']);
        config(['services.ollama.default_model' => 'llama2:latest']);
        config(['services.ollama.embedding_model' => 'nomic-embed-text:latest']);
        $this->repository = new LLMRepository();
    }

    protected function tearDown(): void
    {
        Http::fake();
        parent::tearDown();
    }

    public function testGenerateReturnsResponse(): void
    {
        $prompt = 'test prompt';
        $options = [
            'temperature' => 0.8,
            'top_p' => 0.9
        ];

        $expectedResponse = [
            'response' => 'test response',
            'model' => 'llama2:latest',
            'created_at' => '2024-01-01T00:00:00Z'
        ];

        Http::fake([
            'http://ollama:11434/api/generate' => Http::response($expectedResponse, 200)
        ]);

        $result = $this->repository->generate($prompt, $options);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGenerateHandlesUnsuccessfulResponse(): void
    {
        $prompt = 'test prompt';
        $errorMessage = 'Error en la API';

        Http::fake([
            'http://ollama:11434/api/generate' => Http::response($errorMessage, 500)
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al generar texto: ' . $errorMessage);

        $this->repository->generate($prompt);
    }

    public function testGenerateLogsErrorAndThrowsException(): void
    {
        $prompt = 'test prompt';
        $options = ['temperature' => 0.8];
        $exception = new \Exception('Test error');

        Http::fake([
            'http://ollama:11434/api/generate' => function() use ($exception) {
                throw $exception;
            }
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en repositorio LLM durante generación', [
                'error' => $exception->getMessage(),
                'prompt' => $prompt,
                'options' => $options
            ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al generar texto: Test error');

        $this->repository->generate($prompt, $options);
    }

    public function testGetEmbeddingReturnsVector(): void
    {
        $text = 'test text';
        $expectedEmbedding = [0.1, 0.2, 0.3];

        Http::fake([
            'http://ollama:11434/api/embeddings' => Http::response([
                'embedding' => $expectedEmbedding
            ], 200)
        ]);

        $result = $this->repository->getEmbedding($text);

        $this->assertEquals($expectedEmbedding, $result);
    }

    public function testGetEmbeddingHandlesUnsuccessfulResponse(): void
    {
        $text = 'test text';
        $errorMessage = 'Error en la API';

        Http::fake([
            'http://ollama:11434/api/embeddings' => Http::response($errorMessage, 500)
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al obtener embedding: ' . $errorMessage);

        $this->repository->getEmbedding($text);
    }

    public function testGetEmbeddingLogsErrorAndThrowsException(): void
    {
        $text = 'test text';
        $exception = new \Exception('Test error');

        Http::fake([
            'http://ollama:11434/api/embeddings' => function() use ($exception) {
                throw $exception;
            }
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en repositorio LLM durante obtención de embedding', [
                'error' => $exception->getMessage(),
                'text' => $text
            ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al obtener embedding: Test error');

        $this->repository->getEmbedding($text);
    }

    public function testGetModelsReturnsList(): void
    {
        $expectedModels = [
            ['name' => 'llama2:latest', 'size' => '7B'],
            ['name' => 'mistral:latest', 'size' => '7B']
        ];

        Http::fake([
            'http://ollama:11434/api/tags' => Http::response([
                'models' => $expectedModels
            ], 200)
        ]);

        $result = $this->repository->getModels();

        $this->assertEquals($expectedModels, $result);
    }

    public function testGetModelsHandlesUnsuccessfulResponse(): void
    {
        $errorMessage = 'Error en la API';

        Http::fake([
            'http://ollama:11434/api/tags' => Http::response($errorMessage, 500)
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al obtener modelos: ' . $errorMessage);

        $this->repository->getModels();
    }

    public function testGetModelsLogsErrorAndThrowsException(): void
    {
        $exception = new \Exception('Test error');

        Http::fake([
            'http://ollama:11434/api/tags' => function() use ($exception) {
                throw $exception;
            }
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en repositorio LLM durante obtención de modelos', [
                'error' => $exception->getMessage()
            ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al obtener modelos: Test error');

        $this->repository->getModels();
    }

    public function testGetModelReturnsModelInfo(): void
    {
        $modelName = 'llama2:latest';
        $expectedInfo = [
            'name' => $modelName,
            'size' => '7B',
            'parameters' => [
                'temperature' => 0.8,
                'top_p' => 0.9
            ]
        ];

        Http::fake([
            'http://ollama:11434/api/show?name=llama2:latest' => Http::response($expectedInfo, 200)
        ]);

        $result = $this->repository->getModel($modelName);

        $this->assertEquals($expectedInfo, $result);
    }

    public function testGetModelHandlesUnsuccessfulResponse(): void
    {
        $modelName = 'llama2:latest';
        $errorMessage = 'Error en la API';

        Http::fake([
            'http://ollama:11434/api/show?name=llama2:latest' => Http::response($errorMessage, 500)
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al obtener información del modelo: ' . $errorMessage);

        $this->repository->getModel($modelName);
    }

    public function testGetModelLogsErrorAndThrowsException(): void
    {
        $modelName = 'llama2:latest';
        $exception = new \Exception('Test error');

        Http::fake([
            'http://ollama:11434/api/show?name=llama2:latest' => function() use ($exception) {
                throw $exception;
            }
        ]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en repositorio LLM durante obtención de modelo', [
                'error' => $exception->getMessage(),
                'model' => $modelName
            ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error al obtener información del modelo: Test error');

        $this->repository->getModel($modelName);
    }
} 