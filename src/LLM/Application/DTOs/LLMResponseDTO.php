<?php

declare(strict_types=1);

namespace Src\LLM\Application\DTOs;

readonly class LLMResponseDTO
{
    public function __construct(
        public string|array $response,
        public array        $metadata = []
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
