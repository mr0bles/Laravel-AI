<?php

declare(strict_types=1);

namespace Tests\Unit\RAG\Domain\ValueObjects;

use Tests\TestCase;
use Src\RAG\Domain\ValueObjects\Document;

class DocumentTest extends TestCase
{
    public function testDocumentCanBeCreatedFromArray(): void
    {
        $data = [
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ];

        $document = Document::fromArray($data);

        $this->assertEquals('test content', $document->getContent());
        $this->assertEquals(['key' => 'value'], $document->getMetadata());
    }

    public function testDocumentCanBeCreatedWithEmptyMetadata(): void
    {
        $data = [
            'content' => 'test content'
        ];

        $document = Document::fromArray($data);

        $this->assertEquals('test content', $document->getContent());
        $this->assertEquals([], $document->getMetadata());
    }

    public function testDocumentCanBeConvertedToArray(): void
    {
        $document = new Document(
            content: 'test content',
            metadata: ['key' => 'value']
        );

        $array = $document->toArray();

        $this->assertEquals([
            'content' => 'test content',
            'metadata' => ['key' => 'value']
        ], $array);
    }

    public function testDocumentHandlesEmptyContent(): void
    {
        $data = [
            'content' => '',
            'metadata' => []
        ];

        $document = Document::fromArray($data);

        $this->assertEquals('', $document->getContent());
        $this->assertEquals([], $document->getMetadata());
    }
} 