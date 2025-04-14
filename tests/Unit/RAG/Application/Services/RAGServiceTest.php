<?php

declare(strict_types=1);

namespace Tests\Unit\RAG\Application\Services;

use Tests\TestCase;
use Src\RAG\Application\Services\RAGService;
use Src\RAG\Domain\Repositories\RAGRepositoryInterface;
use Src\RAG\Domain\ValueObjects\Document;
use Mockery;

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

    public function testSearchReturnsRepositoryResults(): void
    {
        $query = 'test query';
        $expectedResults = [
            'results' => [
                'response' => 'test response',
                'model' => 'test-model',
                'timestamp' => '2024-01-01T00:00:00+00:00'
            ]
        ];

        $this->repository
            ->shouldReceive('search')
            ->once()
            ->with($query)
            ->andReturn($expectedResults);

        $results = $this->service->search($query);

        $this->assertEquals($expectedResults, $results);
    }

    public function testStoreCreatesDocumentAndReturnsRepositoryResult(): void
    {
        $documentData = [
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ];

        $expectedResult = [
            'id' => 'doc_123',
            'status' => 'stored',
            'timestamp' => '2024-01-01T00:00:00+00:00'
        ];

        $this->repository
            ->shouldReceive('store')
            ->once()
            ->with($documentData)
            ->andReturn($expectedResult);

        $result = $this->service->store($documentData);

        $this->assertEquals($expectedResult, $result);
    }

    public function testDeleteCallsRepositoryDelete(): void
    {
        $id = 'doc_123';

        $this->repository
            ->shouldReceive('delete')
            ->once()
            ->with($id);

        $this->service->delete($id);
    }
} 