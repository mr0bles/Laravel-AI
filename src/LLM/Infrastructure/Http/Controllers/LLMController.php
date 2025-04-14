<?php

namespace Src\LLM\Infrastructure\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Src\LLM\Application\Services\LLMService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class LLMController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(
        private readonly LLMService $service
    ) {}

    public function generate(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prompt' => 'required|string',
                'model' => 'string',
                'options' => 'array'
            ]);

            $options = $validated['options'] ?? [];
            if (isset($validated['model'])) {
                $options['model'] = $validated['model'];
            }

            $response = $this->service->generate(
                $validated['prompt'],
                $options
            );

            return response()->json($response->toArray());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getModels(): JsonResponse
    {
        try {
            return response()->json($this->service->getModels());
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getModel(string $modelName): JsonResponse
    {
        try {
            return response()->json($this->service->getModel($modelName));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
