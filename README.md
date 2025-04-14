# MR-AI

Este proyecto implementa un sistema de RAG (Retrieval Augmented Generation) utilizando Laravel, PostgreSQL con pgvector y Ollama.

## Requisitos

- Docker y Docker Compose
- Git
- Ollama instalado y ejecutándose en el host

## Configuración

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/mr0bles/mr-ai.git
   cd mr-ai
   ```

2. Copiar el archivo de entorno de ejemplo:
   ```bash
   cp .env.example .env
   ```

3. Configurar las variables de entorno en `.env`:
   ```env
   # Configuración de la aplicación
   APP_NAME="Ollama API"
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost

   # Configuración de la base de datos
   DB_CONNECTION=pgsql
   DB_HOST=db
   DB_PORT=5432
   DB_DATABASE=mr_ai
   DB_USERNAME=mr_ai
   DB_PASSWORD=mr_ai_password

   # Configuración de Ollama
   OLLAMA_URL=http://192.168.1.67:11434  # Ajustar según tu configuración
   OLLAMA_MODEL=deepseek-coder-v2:lite
   OLLAMA_EMBEDDING_MODEL=nomic-embed-text:latest
   OLLAMA_EMBEDDING_DIMENSIONS=768

   # Configuración de RAG
   RAG_SIMILARITY_THRESHOLD=0.7
   RAG_MAX_RESULTS=5
   RAG_TEMPERATURE=0.7
   RAG_TOP_P=0.9
   ```

## Levantar el Stack

1. Construir y levantar los contenedores:
   ```bash
   docker-compose up -d --build
   ```

2. Instalar las dependencias de Composer:
   ```bash
   docker-compose exec app composer install
   ```

3. Generar la clave de la aplicación:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

4. Ejecutar las migraciones:
   ```bash
   docker-compose exec app php artisan migrate:fresh
   ```

5. Verificar que los servicios estén funcionando:
   ```bash
   docker-compose ps
   ```

## Uso

### LLM API

La API de LLM proporciona los siguientes endpoints:

- Generar texto:
  ```bash
  POST /api/v1/llm/generate
  Content-Type: application/json

  {
      "prompt": "¿Cuál es la capital de Francia?",
      "model": "deepseek-coder-v2:lite",
      "options": {
          "temperature": 0.7,
          "top_p": 0.9
      }
  }
  ```

- Obtener modelos disponibles:
  ```bash
  GET /api/v1/llm/models
  ```

- Obtener información de un modelo:
  ```bash
  GET /api/v1/llm/models/{modelName}
  ```

### RAG API

La API de RAG proporciona los siguientes endpoints:

- Buscar documentos:
  ```bash
  POST /api/v1/rag/search
  Content-Type: application/json

  {
      "query": "¿Qué es la inteligencia artificial?"
  }
  ```

- Almacenar documento:
  ```bash
  POST /api/v1/rag/documents
  Content-Type: application/json

  {
      "content": "La inteligencia artificial es...",
      "metadata": {
          "source": "Wikipedia",
          "author": "John Doe"
      }
  }
  ```

- Eliminar documento:
  ```bash
  DELETE /api/v1/rag/documents/{id}
  ```

## Pruebas

Para probar los endpoints, puedes usar el archivo `tests/Http/llm.http` con la extensión REST Client de VS Code o cualquier cliente HTTP como Postman.

## Detener el Stack

Para detener los contenedores:
```bash
docker-compose down
```

Para detener y eliminar los volúmenes:
```bash
docker-compose down -v
```

## Solución de Problemas

1. Si la aplicación no puede conectarse a Ollama:
   - Verifica que Ollama esté ejecutándose en el host
   - Ajusta la URL de Ollama en el archivo `.env`
   - Verifica que el puerto 11434 esté accesible

2. Si hay problemas con la base de datos:
   - Verifica que PostgreSQL esté ejecutándose
   - Verifica las credenciales en el archivo `.env`
   - Intenta reiniciar el contenedor de la base de datos:
     ```bash
     docker-compose restart db
     ```

3. Si hay problemas con la aplicación:
   - Verifica los logs:
     ```bash
     docker-compose logs app
     ```
   - Intenta reiniciar el contenedor:
     ```bash
     docker-compose restart app
     ```
