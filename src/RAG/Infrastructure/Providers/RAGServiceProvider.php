<?php

namespace Src\RAG\Infrastructure\Providers;

use Src\RAG\Application\Services\RAGService;
use Src\RAG\Domain\Repositories\RAGRepositoryInterface;
use Src\RAG\Infrastructure\Repositories\RAGRepository;
use Illuminate\Support\ServiceProvider;

class RAGServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(RAGRepositoryInterface::class, RAGRepository::class);
        $this->app->singleton(RAGService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 