<?php

declare(strict_types=1);

namespace Src\LLM\Domain\Tools;

interface ToolFunctionInterface
{
    public function name(): string;

    public function definition(): array;

    public function execute(array $arguments): array;
}
