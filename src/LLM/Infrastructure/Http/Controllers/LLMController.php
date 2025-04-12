<?php

namespace Src\LLM\Infrastructure\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Src\LLM\Application\Services\LLMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LLMController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(
        private readonly LLMService $service
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string',
            'model' => 'required|string',
            'options' => 'array'
        ]);

        $response = $this->service->generate(
            $validated['prompt'],
            $validated['model'],
            $validated['options'] ?? []
        );

        return response()->json($response->toArray());
    }

    public function getModels(): JsonResponse
    {
        return response()->json($this->service->getModels());
    }

    public function getModel(string $modelName): JsonResponse
    {
        return response()->json($this->service->getModel($modelName));
    }
}
