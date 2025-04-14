<?php

declare(strict_types=1);

namespace Tests\Feature\RAG\Infrastructure\Http\Controllers;

use Tests\TestCase;
use Src\RAG\Application\Services\RAGService;
use Mockery;
use Illuminate\Http\Response;

class RAGControllerTest extends TestCase
{
    private RAGService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = Mockery::mock(RAGService::class);
        $this->app->instance(RAGService::class, $this->service);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSearchReturnsResponse(): void
    {
        $requestData = [
            'query' => 'test query'
        ];

        $expectedResponse = [
            'response' => 'test response',
            'context' => 'test context',
            'documents' => [
                [
                    'id' => 1,
                    'content' => 'test content',
                    'metadata' => ['test' => 'value'],
                    'similarity' => 0.8
                ]
            ]
        ];

        $this->service
            ->shouldReceive('search')
            ->once()
            ->with($requestData['query'])
            ->andReturn($expectedResponse);

        $response = $this->postJson('/api/v1/rag/search', $requestData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson($expectedResponse);
    }

    public function testSearchValidatesRequest(): void
    {
        $response = $this->postJson('/api/v1/rag/search', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['query']);
    }

    public function testSearchHandlesError(): void
    {
        $requestData = [
            'query' => 'test query'
        ];

        $this->service
            ->shouldReceive('search')
            ->once()
            ->with($requestData['query'])
            ->andThrow(new \Exception('Test error'));

        $response = $this->postJson('/api/v1/rag/search', $requestData);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'error' => 'Error al realizar la búsqueda',
                'message' => 'Test error'
            ]);
    }

    public function testStoreCreatesDocument(): void
    {
        $requestData = [
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ];

        $expectedResponse = [
            'id' => 1,
            'status' => 'success',
            'timestamp' => '2024-01-01T00:00:00Z'
        ];

        $this->service
            ->shouldReceive('store')
            ->once()
            ->with($requestData)
            ->andReturn($expectedResponse);

        $response = $this->postJson('/api/v1/rag/documents', $requestData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson($expectedResponse);
    }

    public function testStoreValidatesRequest(): void
    {
        $response = $this->postJson('/api/v1/rag/documents', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['content']);
    }

    public function testStoreHandlesError(): void
    {
        $requestData = [
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ];

        $this->service
            ->shouldReceive('store')
            ->once()
            ->with($requestData)
            ->andThrow(new \Exception('Test error'));

        $response = $this->postJson('/api/v1/rag/documents', $requestData);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'error' => 'Error al almacenar el documento',
                'message' => 'Test error'
            ]);
    }

    public function testDeleteRemovesDocument(): void
    {
        $id = '1';

        $this->service
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn();

        $response = $this->deleteJson("/api/v1/rag/documents/{$id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteHandlesError(): void
    {
        $id = '1';

        $this->service
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andThrow(new \Exception('Test error'));

        $response = $this->deleteJson("/api/v1/rag/documents/{$id}");

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson([
                'error' => 'Error al eliminar el documento',
                'message' => 'Test error'
            ]);
    }
} 