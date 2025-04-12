<?php

declare(strict_types=1);

namespace Tests\Feature\RAG\Infrastructure\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Src\RAG\Application\Services\RAGService;

class RAGControllerTest extends TestCase
{
    use RefreshDatabase;

    private RAGService $ragService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ragService = $this->app->make(RAGService::class);
    }

    public function testSearchEndpointReturnsResults(): void
    {
        $response = $this->postJson('/api/v1/rag/search', [
            'query' => '¿Qué es el machine learning?'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'results' => [
                    'response',
                    'model',
                    'timestamp'
                ]
            ]);
    }

    public function testSearchEndpointValidatesInput(): void
    {
        $response = $this->postJson('/api/v1/rag/search', [
            'query' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    public function testStoreEndpointCreatesDocument(): void
    {
        $response = $this->postJson('/api/v1/rag/documents', [
            'content' => 'El machine learning es una rama de la inteligencia artificial...',
            'metadata' => [
                'title' => 'Introducción a Machine Learning',
                'author' => 'John Doe',
                'tags' => ['AI', 'ML', 'Tutorial']
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'status',
                'timestamp'
            ]);
    }

    public function testDeleteEndpointRemovesDocument(): void
    {
        // Primero creamos un documento
        $storeResponse = $this->postJson('/api/v1/rag/documents', [
            'content' => 'Test content',
            'metadata' => ['test' => 'value']
        ]);
        
        $documentId = $storeResponse->json('id');
        
        // Luego lo eliminamos
        $response = $this->deleteJson("/api/v1/rag/documents/{$documentId}");
        
        $response->assertStatus(204);
    }
} 