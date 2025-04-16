<?php

declare(strict_types=1);

namespace Src\RAG\Infrastructure\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\RAG\Application\Services\RAGService;

readonly class RAGController
{
    public function __construct(
        private RAGService $ragService
    ) {}

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|min:1',
            'options' => 'array'
        ]);

        $options = $validated['options'] ?? [];

        try {
            $results = $this->ragService->search($validated['prompt'], $options);
            return response()->json($results);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al realizar la búsqueda',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|min:1',
            'metadata' => 'array'
        ]);

        try {
            $document = [
                'content' => $request->input('content'),
                'metadata' => $request->input('metadata', [])
            ];

            $result = $this->ragService->store($document);
            return response()->json($result, 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al almacenar el documento',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(string $id): JsonResponse
    {
        try {
            $this->ragService->delete($id);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el documento',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
