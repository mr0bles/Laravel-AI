# Estructura del Proyecto

## Estructura de Directorios

```
mr-ai/
├── src/                    # Código fuente principal
│   ├── LLM/               # Módulo LLM (Large Language Models)
│   │   ├── Application/   # Capa de aplicación
│   │   ├── Domain/        # Capa de dominio
│   │   └── Infrastructure/# Capa de infraestructura
│   ├── RAG/               # Módulo RAG (Retrieval-Augmented Generation)
│   │   ├── Application/   # Capa de aplicación
│   │   ├── Domain/        # Capa de dominio
│   │   └── Infrastructure/# Capa de infraestructura
│   └── Infrastructure/    # Infraestructura compartida
│       └── Shared/        # Componentes compartidos
├── app/                    # Código de la aplicación Laravel
│   ├── Console/           # Comandos de consola
│   ├── Http/              # Controladores y middleware
│   └── Models/            # Modelos de Laravel
├── bootstrap/             # Scripts de arranque
├── config/                # Archivos de configuración
├── database/              # Migraciones y seeds
│   ├── migrations/        # Archivos de migración
│   ├── seeders/          # Semillas de datos
│   └── factories/        # Factories para pruebas
├── docker/                # Configuración de Docker
│   ├── nginx/            # Configuración de Nginx
│   └── php/              # Configuración de PHP
├── public/                # Archivos públicos accesibles
├── resources/             # Recursos (vistas, assets)
│   ├── css/              # Estilos CSS
│   ├── js/               # JavaScript
│   └── views/            # Vistas Blade
├── routes/                # Definición de rutas
│   ├── api.php           # Rutas de API
│   ├── web.php           # Rutas web
│   └── console.php       # Rutas de consola
├── storage/               # Almacenamiento
│   ├── app/              # Archivos de la aplicación
│   ├── framework/        # Archivos del framework
│   └── logs/             # Archivos de log
├── tests/                 # Pruebas
│   ├── Feature/          # Pruebas de características
│   └── Unit/             # Pruebas unitarias
└── vendor/                # Dependencias de Composer
```

## Arquitectura del Proyecto

El proyecto sigue una arquitectura hexagonal (ports and adapters) con las siguientes capas:

### Módulos Principales

- **LLM**: Módulo para Large Language Models (`src/LLM/`)
- **RAG**: Módulo para Retrieval-Augmented Generation (`src/RAG/`)
- **Infraestructura Compartida**: Componentes comunes (`src/Infrastructure/Shared/`)

### Capas de Arquitectura

Cada módulo (LLM y RAG) implementa las siguientes capas:

1. **Domain**
    - Entidades y lógica de negocio
    - Interfaces (ports) para adaptadores
    - Componentes:
        - Entities: Objetos de negocio
        - ValueObjects: Objetos inmutables
        - Repositories: Interfaces de acceso a datos
        - Events: Eventos de dominio
        - Exceptions: Excepciones específicas

2. **Application**
    - Casos de uso y servicios
    - Coordinación de lógica de negocio
    - Componentes:
        - Services: Servicios de aplicación
        - DTOs: Objetos de transferencia de datos
        - Commands/Queries: Comandos y consultas
        - Handlers: Manejadores de comandos/consultas

3. **Infrastructure**
    - Implementaciones de adaptadores
    - Servicios externos
    - Componentes:
        - Repositories: Implementaciones concretas
        - Providers: Service Providers
        - Services: Servicios de infraestructura
        - Events: Listeners y subscribers
        - Exceptions: Manejadores de excepciones

## Patrones de Diseño Utilizados

1. **Inyección de Dependencias**
    - Implementado a través de Service Providers
    - Uso de contenedor de servicios de Laravel

2. **Repositorio**
    - Separación entre lógica de negocio y acceso a datos
    - Patrón Unit of Work para transacciones

3. **CQRS (Command Query Responsibility Segregation)**
    - Separación de comandos y consultas
    - Optimización de lecturas y escrituras

4. **Event Sourcing**
    - Captura de cambios como secuencia de eventos
    - Reconstrucción del estado a partir de eventos

5. **Adapter Pattern**
    - Adaptadores para diferentes proveedores
    - Interfaz común para operaciones

6. **Strategy Pattern**
    - Estrategias para diferentes modelos y algoritmos

7. **Factory Pattern**
    - Factories para creación de instancias

## Convenciones de Nombrado

- **Interfaces**: Sufijo `Interface` (ej: `RAGRepositoryInterface`)
- **Implementaciones**: Sin sufijo (ej: `RAGRepository`)
- **Servicios**: Sufijo `Service` (ej: `RAGService`)
- **Providers**: Sufijo `ServiceProvider` (ej: `RAGServiceProvider`)
- **Commands**: Sufijo `Command` (ej: `CreateRAGCommand`)
- **Queries**: Sufijo `Query` (ej: `GetRAGQuery`)
- **Handlers**: Sufijo `Handler` (ej: `CreateRAGHandler`)
- **Events**: Sufijo `Event` (ej: `RAGCreatedEvent`)
- **Listeners**: Sufijo `Listener` (ej: `SendRAGNotificationListener`)

## Configuración

- Variables de entorno en `.env`
- Configuraciones específicas en `config/`
- Docker y Docker Compose para contenedorización
- Configuración de PHP en `php.ini`
- Configuración de Nginx en `docker/nginx/`
- Configuración de PHPUnit en `phpunit.xml`

## Pruebas

- Pruebas unitarias en `tests/Unit/`
- Pruebas de características en `tests/Feature/`
- Factories para datos de prueba en `database/factories/`
- Seeds para datos de prueba en `database/seeders/`
- Configuración de PHPUnit en `phpunit.xml`

## Documentación

- Documentación de API en `docs/api/`
- Documentación de arquitectura en `docs/architecture/`
- Guías de contribución en `docs/contributing/`
- Documentación de despliegue en `docs/deployment/` 
