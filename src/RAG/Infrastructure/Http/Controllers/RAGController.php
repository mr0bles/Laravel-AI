<?php

namespace Src\RAG\Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\RAG\Application\Services\RAGService;

class RAGController
{
    public function __construct(
        private readonly RAGService $ragService
    ) {}

    /**
     * Busca documentos similares a la consulta dada
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|min:1'
        ]);

        try {
            $results = $this->ragService->search($validated['prompt']);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al realizar la búsqueda',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Almacena un nuevo documento
     *
     * @param Request $request
     * @return JsonResponse
     */
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
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al almacenar el documento',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un documento por su ID
     *
     * @param string $id
     * @return JsonResponse
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $this->ragService->delete($id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el documento',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
