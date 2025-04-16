<?php

use Illuminate\Support\Facades\Route;
use Src\LLM\Infrastructure\Http\Controllers\LLMController;
use Src\RAG\Infrastructure\Http\Controllers\RAGController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // LLM Routes
    Route::prefix('llm')->group(function () {
        Route::post('/generate', [LLMController::class, 'generate']);
        Route::post('/embedding', [LLMController::class, 'getEmbedding']);
        Route::post('/chat', [LLMController::class, 'chat']);
        Route::post('/analyze-image', [LLMController::class, 'analyzeImage']);

        Route::get('/models', [LLMController::class, 'getModels']);
        Route::get('/models/{modelName}', [LLMController::class, 'getModel']);
    });

    // RAG Routes
    Route::prefix('rag')->group(function () {
        Route::post('search', [RAGController::class, 'search']);
        Route::post('documents', [RAGController::class, 'store']);
        Route::delete('documents/{id}', [RAGController::class, 'delete']);
    });
});
