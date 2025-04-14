<?php

declare(strict_types=1);

namespace Tests\Feature\LLM\Infrastructure\Http\Controllers;

use Tests\TestCase;
use Src\LLM\Application\Services\LLMService;
use Src\LLM\Domain\DTOs\LLMResponseDTO;
use Mockery;
use Illuminate\Http\Response;

class LLMControllerTest extends TestCase
{
    private LLMService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(LLMService::class);
        $this->app->instance(LLMService::class, $this->service);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGenerateReturnsResponse(): void
    {
        $requestData = [
            'prompt' => 'test prompt',
            'options' => [
                'temperature' => 0.8,
                'top_p' => 0.9
            ]
        ];

        $responseData = [
            'response' => 'test response',
            'metadata' => [
                'model' => 'llama2:latest',
                'created_at' => '2024-01-01T00:00:00Z'
            ]
        ];

        $this->service
            ->shouldReceive('generate')
            ->once()
            ->with($requestData['prompt'], $requestData['options'])
            ->andReturn(LLMResponseDTO::fromArray($responseData));

        $response = $this->postJson('/api/v1/llm/generate', $requestData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'response' => 'test response',
                'metadata' => [
                    'model' => 'llama2:latest',
                    'created_at' => '2024-01-01T00:00:00Z'
                ]
            ]);
    }

    public function testGenerateValidatesRequest(): void
    {
        $response = $this->postJson('/api/v1/llm/generate', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['prompt']);
    }

    public function testGenerateHandlesError(): void
    {
        $requestData = [
            'prompt' => 'test prompt'
        ];

        $this->service
            ->shouldReceive('generate')
            ->once()
            ->with($requestData['prompt'], [])
            ->andThrow(new \Exception('Test error'));

        $response = $this->postJson('/api/v1/llm/generate', $requestData);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'error' => 'Test error'
            ]);
    }

    public function testGetModelsReturnsList(): void
    {
        $expectedModels = [
            ['name' => 'llama2:latest', 'size' => '7B'],
            ['name' => 'mistral:latest', 'size' => '7B']
        ];

        $this->service
            ->shouldReceive('getModels')
            ->once()
            ->andReturn($expectedModels);

        $response = $this->getJson('/api/v1/llm/models');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson($expectedModels);
    }

    public function testGetModelsHandlesError(): void
    {
        $this->service
            ->shouldReceive('getModels')
            ->once()
            ->andThrow(new \Exception('Test error'));

        $response = $this->getJson('/api/v1/llm/models');

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'error' => 'Test error'
            ]);
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

        $this->service
            ->shouldReceive('getModel')
            ->once()
            ->with($modelName)
            ->andReturn($expectedInfo);

        $response = $this->getJson("/api/v1/llm/models/{$modelName}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson($expectedInfo);
    }

    public function testGetModelHandlesError(): void
    {
        $modelName = 'llama2:latest';

        $this->service
            ->shouldReceive('getModel')
            ->once()
            ->with($modelName)
            ->andThrow(new \Exception('Test error'));

        $response = $this->getJson("/api/v1/llm/models/{$modelName}");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'error' => 'Test error'
            ]);
    }
} 