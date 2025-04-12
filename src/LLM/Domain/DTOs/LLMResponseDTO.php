<?php

namespace Src\LLM\Domain\DTOs;

class LLMResponseDTO
{
    public function __construct(
        private readonly string $response,
        private readonly array $metadata = []
    ) {}

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            response: $data['response'] ?? '',
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'response' => $this->response,
            'metadata' => $this->metadata
        ];
    }
} 