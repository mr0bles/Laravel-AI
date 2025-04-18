<?php

declare(strict_types=1);

namespace Src\LLM\Domain\Tools;

interface ToolFunctionInterface
{
    /**
     * Devuelve el nombre único de la herramienta.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Devuelve una descripción breve de la herramienta.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Devuelve la definición de la herramienta.
     *
     * @return array
     */
    public function definition(): array;

    /**
     * Ejecuta la herramienta con los argumentos dados.
     *
     * @param mixed|null $arguments
     * @return array
     */
    public function execute(mixed $arguments = null): array;
}
