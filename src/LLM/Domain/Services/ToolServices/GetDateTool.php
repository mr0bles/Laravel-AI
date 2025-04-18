<?php

declare(strict_types=1);

namespace Src\LLM\Domain\Services\ToolServices;


use Src\LLM\Domain\Tools\ToolFunctionInterface;
use stdClass;

class GetDateTool implements ToolFunctionInterface
{
    public function definition(): array
    {
        return [
            "type" => "function",
            "function" => [
                "name" => $this->name(),
                "description" => $this->description(),
                "parameters" => [
                    "type" => "object",
                    "properties" => new stdClass(),
                ],
            ],
        ];
    }

    public function name(): string
    {
        return 'get_date_tool';
    }

    public function description(): string
    {
        return "Get the current datetime";
    }

    public function execute(mixed $arguments = null): array
    {
        return [
            'date' => now()->toArray()
        ];
    }

}
