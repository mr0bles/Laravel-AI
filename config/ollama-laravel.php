<?php

// Config for Cloudstudio/Ollama

return [
    'model' => env('OLLAMA_MODEL', 'llama2'),
    'embedding_model' => env('OLLAMA_EMBEDDING_MODEL', 'nomic-embed-text:latest'),
    'url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
    'temperature' => (float)env('OLLAMA_TEMPERATURE', 0.7),
    'top_p' => (float)env('OLLAMA_TOP_P', 0.9),
    'timeout' => (int)env('OLLAMA_TIMEOUT', 60),
    'connection' => [
        'timeout' => (int)env('OLLAMA_CONNECTION_TIMEOUT', 300),
        'verify_ssl' => env('OLLAMA_VERIFY_SSL', true),
    ],
    'auth' => [
        'type' => env('OLLAMA_AUTH_TYPE', null), // 'bearer' or 'basic' or null
        'token' => env('OLLAMA_AUTH_TOKEN', null),
        'username' => env('OLLAMA_AUTH_USERNAME', null),
        'password' => env('OLLAMA_AUTH_PASSWORD', null),
    ],
    'headers' => [],
];
