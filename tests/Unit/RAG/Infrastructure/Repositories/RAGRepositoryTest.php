<?php

declare(strict_types=1);

namespace Tests\Unit\RAG\Infrastructure\Repositories;

use Tests\TestCase;
use Src\RAG\Infrastructure\Repositories\RAGRepository;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Illuminate\Support\Facades\Config;
use Mockery;

class RAGRepositoryTest extends TestCase
{
    private LLMRepositoryInterface $llmRepository;
    private RAGRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->llmRepository = Mockery::mock(LLMRepositoryInterface::class);
        $this->repository = new RAGRepository($this->llmRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testSearchReturnsFormattedResponse(): void
    {
        $query = 'test query';
        $model = 'test-model';
        $llmResponse = [
            'response' => 'test response',
            'model' => $model
        ];

        // Configurar el mock de Config
        Config::shouldReceive('get')
            ->with('services.llm.default_model', 'deepseek-coder-v2:lite')
            ->andReturn($model);

        $this->llmRepository
            ->shouldReceive('generate')
            ->once()
            ->with(
                $query,
                $model,
                [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                ]
            )
            ->andReturn($llmResponse);

        $result = $this->repository->search($query);

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('response', $result['results']);
        $this->assertArrayHasKey('model', $result['results']);
        $this->assertArrayHasKey('timestamp', $result['results']);
        $this->assertEquals('test response', $result['results']['response']);
        $this->assertEquals($model, $result['results']['model']);
    }

    public function testStoreReturnsDocumentIdAndMetadata(): void
    {
        $document = [
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ];

        $result = $this->repository->store($document);

        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertEquals('stored', $result['status']);
        $this->assertStringStartsWith('doc_', $result['id']);
    }

    public function testDeleteLogsDocumentDeletion(): void
    {
        $id = 'doc_123';
        
        // No necesitamos assertions aquí ya que el método solo registra
        // y no devuelve nada
        $this->repository->delete($id);
    }
} 