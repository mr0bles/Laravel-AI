<?php

declare(strict_types=1);

namespace Src\LLM\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Src\LLM\Infrastructure\Repositories\OllamaLLMRepository;
use Src\LLM\Application\Services\LLMService;

class LLMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LLMRepositoryInterface::class,
            OllamaLLMRepository::class
        );
        $this->app->bind(LLMService::class);
    }

    public function boot(): void
    {
        //
    }
} 