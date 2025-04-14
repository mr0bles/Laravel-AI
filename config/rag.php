<?php

return [
    /*
    |--------------------------------------------------------------------------
    | RAG Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the RAG (Retrieval Augmented Generation)
    | system. It includes parameters for similarity search, document retrieval,
    | and text generation.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Similarity Search Parameters
    |--------------------------------------------------------------------------
    |
    | These parameters control how the similarity search is performed.
    |
    */
    'similarity_threshold' => (float)env('RAG_SIMILARITY_THRESHOLD', 0.7),
    'max_results' => (int)env('RAG_MAX_RESULTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Text Generation Parameters
    |--------------------------------------------------------------------------
    |
    | These parameters control how the text generation is performed.
    | All float values must be between 0.0 and 1.0
    |
    */
    'temperature' => (float)env('RAG_TEMPERATURE', 0.7),
    'top_p' => (float)env('RAG_TOP_P', 0.9),
]; 