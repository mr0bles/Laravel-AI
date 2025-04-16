<?php

declare(strict_types=1);

namespace Src\LLM\Infrastructure\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Src\LLM\Application\Services\LLMService;

class LLMController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct(
        private readonly LLMService $service
    )
    {
    }

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
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEmbedding(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'prompt' => 'required|string'
            ]);

            $response = $this->service->getEmbedding($validated['prompt']);

            return response()->json($response);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function chat(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'messages' => 'required|array',
                'messages.*.role' => 'required|string|in:system,user,assistant',
                'messages.*.content' => 'required|string',
                'options' => 'array'
            ]);

            $response = $this->service->chat(
                $validated['messages'],
                $validated['options'] ?? []
            );

            return response()->json($response->toArray());
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function analyzeImage(Request $request): JsonResponse
    {
        try {
            Log::info('Request completa', $request->all());

            $validated = $request->validate([
                'image' => 'required|image',
                'prompt' => 'required|string',
                'options' => 'array'
            ]);

            $options = $validated['options'] ?? [];
            $imagePath = $validated['image']->store('temp', 'public');

            $response = $this->service->analyzeImage(
                storage_path('app/public/' . $imagePath),
                $validated['prompt'],
                $options
            );

            // Limpiar el archivo temporal
            unlink(storage_path('app/public/' . $imagePath));

            return response()->json($response->toArray());
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function getModels(): JsonResponse
    {
        try {
            return response()->json($this->service->getModels());
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getModel(string $modelName): JsonResponse
    {
        try {
            return response()->json($this->service->getModel($modelName));
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
