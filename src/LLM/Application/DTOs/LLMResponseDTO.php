<?php

namespace Src\LLM\Application\DTOs;

class LLMResponseDTO
{
    public function __construct(
        public readonly string $response,
        public readonly array  $metadata = []
    ) {}

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
