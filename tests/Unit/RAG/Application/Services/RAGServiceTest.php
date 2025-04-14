<?php

declare(strict_types=1);

namespace Tests\Unit\RAG\Application\Services;

use Tests\TestCase;
use Src\RAG\Application\Services\RAGService;
use Src\RAG\Domain\Repositories\RAGRepositoryInterface;
use Mockery;
use Illuminate\Support\Facades\Log;

class RAGServiceTest extends TestCase
{
    private RAGRepositoryInterface $repository;
    private RAGService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(RAGRepositoryInterface::class);
        $this->service = new RAGService($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSearchReturnsResponse(): void
    {
        $query = 'test query';
        $options = [
            'similarity_threshold' => 0.7,
            'max_results' => 5,
            'temperature' => 0.8,
            'top_p' => 0.9
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

        Log::shouldReceive('info')
            ->once()
            ->with('Iniciando búsqueda RAG', ['query' => $query, 'options' => $options]);

        Log::shouldReceive('info')
            ->once()
            ->with('Búsqueda RAG completada', ['results' => $expectedResponse]);

        $this->repository
            ->shouldReceive('search')
            ->once()
            ->with($query, $options)
            ->andReturn($expectedResponse);

        $result = $this->service->search($query, $options);

        $this->assertIsArray($result);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testSearchLogsErrorAndThrowsException(): void
    {
        $query = 'test query';
        $exception = new \Exception('Test error');

        Log::shouldReceive('info')
            ->once()
            ->with('Iniciando búsqueda RAG', ['query' => $query, 'options' => []]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en servicio RAG durante búsqueda', [
                'error' => $exception->getMessage(),
                'query' => $query
            ]);

        $this->repository
            ->shouldReceive('search')
            ->once()
            ->with($query, [])
            ->andThrow($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test error');

        $this->service->search($query);
    }

    public function testStoreCreatesDocument(): void
    {
        $documentData = [
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ];

        $expectedResponse = [
            'id' => 1,
            'status' => 'success',
            'timestamp' => '2024-01-01T00:00:00Z'
        ];

        Log::shouldReceive('info')
            ->once()
            ->with('Iniciando almacenamiento de documento', ['document' => $documentData]);

        Log::shouldReceive('info')
            ->once()
            ->with('Documento almacenado exitosamente', ['id' => $expectedResponse['id']]);

        $this->repository
            ->shouldReceive('store')
            ->once()
            ->with($documentData)
            ->andReturn($expectedResponse);

        $result = $this->service->store($documentData);

        $this->assertIsArray($result);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testStoreLogsErrorAndThrowsException(): void
    {
        $documentData = [
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ];
        $exception = new \Exception('Test error');

        Log::shouldReceive('info')
            ->once()
            ->with('Iniciando almacenamiento de documento', ['document' => $documentData]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en servicio RAG durante almacenamiento', [
                'error' => $exception->getMessage(),
                'document' => $documentData
            ]);

        $this->repository
            ->shouldReceive('store')
            ->once()
            ->with($documentData)
            ->andThrow($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test error');

        $this->service->store($documentData);
    }

    public function testDeleteRemovesDocument(): void
    {
        $id = '1';

        Log::shouldReceive('info')
            ->once()
            ->with('Iniciando eliminación de documento', ['id' => $id]);

        Log::shouldReceive('info')
            ->once()
            ->with('Documento eliminado exitosamente', ['id' => $id]);

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn();

        $this->service->delete($id);

        // Verificar que el mock fue llamado correctamente
        $this->assertTrue(true);
    }

    public function testDeleteLogsErrorAndThrowsException(): void
    {
        $id = '1';
        $exception = new \Exception('Test error');

        Log::shouldReceive('info')
            ->once()
            ->with('Iniciando eliminación de documento', ['id' => $id]);

        Log::shouldReceive('error')
            ->once()
            ->with('Error en servicio RAG durante eliminación', [
                'error' => $exception->getMessage(),
                'id' => $id
            ]);

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andThrow($exception);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Test error');

        $this->service->delete($id);
    }
} 