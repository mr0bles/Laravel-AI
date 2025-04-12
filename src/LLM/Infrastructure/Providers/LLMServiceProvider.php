<?php

namespace Src\LLM\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Src\LLM\Domain\Repositories\LLMRepositoryInterface;
use Src\LLM\Infrastructure\Repositories\LLMRepository;
use Src\LLM\Application\Services\LLMService;

class LLMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LLMRepositoryInterface::class, LLMRepository::class);
        $this->app->bind(LLMService::class);
    }

    public function boot(): void
    {
        //
    }
} 