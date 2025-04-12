<?php

declare(strict_types=1);

namespace Src\RAG\Domain\ValueObjects;

class Document
{
    public function __construct(
        private readonly string $content,
        private readonly array $metadata = []
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            content: $data['content'] ?? '',
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'metadata' => $this->metadata
        ];
    }
} 