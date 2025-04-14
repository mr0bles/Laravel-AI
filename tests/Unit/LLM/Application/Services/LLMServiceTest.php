<?php

declare(strict_types=1);

namespace Tests\Unit\LLM\Application\Services;

use Tests\TestCase;
use Src\LLM\Application\Services\LLMService;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Src\LLM\Domain\DTOs\LLMResponseDTO;
use Mockery;
use Illuminate\Support\Facades\Log;

class LLMServiceTest extends TestCase
{
    private LLMRepositoryInterface $repository;
    private LLMService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(LLMRepositoryInterface::class);
        $this->service = new LLMService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
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
            'metadata' => [
                'model' => 'llama2:latest',
                'created_at' => '2024-01-01T00:00:00Z'
            ]
        ];

        $this->repository
            ->shouldReceive('generate')
            ->once()
            ->with($prompt, $options)
            ->andReturn($expectedResponse);

        $result = $this->service->generate($prompt, $options);

        $this->assertInstanceOf(LLMResponseDTO::class, $result);
        $this->assertEquals('test response', $result->getResponse());
        $this->assertEquals($expectedResponse['metadata'], $result->getMetadata());
    }

    public function testGenerateLogsErrorAndThrowsException(): void
    {
        $prompt = 'test prompt';
        $exception = new \Exception('Test error');

        $this->repository
            ->shouldReceive('generate')
            ->once()
            ->with($prompt, [])
            ->andThrow($exception);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en servicio LLM durante generación', [
                'error' => $exception->getMessage(),
                'prompt' => $prompt
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test error');

        $this->service->generate($prompt);
    }

    public function testGetModelsReturnsList(): void
    {
        $expectedModels = [
            ['name' => 'llama2:latest', 'size' => '7B'],
            ['name' => 'mistral:latest', 'size' => '7B']
        ];

        $this->repository
            ->shouldReceive('getModels')
            ->once()
            ->andReturn($expectedModels);

        $result = $this->service->getModels();

        $this->assertIsArray($result);
        $this->assertEquals($expectedModels, $result);
    }

    public function testGetModelsLogsErrorAndThrowsException(): void
    {
        $exception = new \Exception('Test error');

        $this->repository
            ->shouldReceive('getModels')
            ->once()
            ->andThrow($exception);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en servicio LLM durante obtención de modelos', [
                'error' => $exception->getMessage()
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test error');

        $this->service->getModels();
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

        $this->repository
            ->shouldReceive('getModel')
            ->once()
            ->with($modelName)
            ->andReturn($expectedInfo);

        $result = $this->service->getModel($modelName);

        $this->assertIsArray($result);
        $this->assertEquals($expectedInfo, $result);
    }

    public function testGetModelLogsErrorAndThrowsException(): void
    {
        $modelName = 'llama2:latest';
        $exception = new \Exception('Test error');

        $this->repository
            ->shouldReceive('getModel')
            ->once()
            ->with($modelName)
            ->andThrow($exception);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en servicio LLM durante obtención de modelo', [
                'error' => $exception->getMessage(),
                'model' => $modelName
            ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test error');

        $this->service->getModel($modelName);
    }
} 