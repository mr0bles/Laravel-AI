<?php

declare(strict_types=1);

namespace Src\LLM\Domain\Tools;

use InvalidArgumentException;
use Src\LLM\Domain\Services\ToolServices\GetCurrentWeatherTool;

class ToolFunctionRegistry
{
    private const TOOLS_MODELS = [
        'qwen2.5:3b',
    ];

    /**
     * @var ToolFunctionInterface[]
     */
    protected array $tools;

    public function __construct()
    {
        $this->tools = [
            new GetCurrentWeatherTool(),
        ];
    }

    public function get(string $name): ?ToolFunctionInterface
    {
        $tool = current(array_filter($this->tools, fn($tool) => $tool->name() === $name));

        if (!$tool) {
            throw new InvalidArgumentException("Tool with name '{$name}' not found.");
        }

        return $tool;
    }

    public function allDefinitions(string $model): array
    {
        if (!in_array($model, self::TOOLS_MODELS, true)) {
            return [];
        }

        return array_map(fn($tool) => $tool->definition(), $this->tools);
    }
}
