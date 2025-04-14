FROM postgres:15

# Instalar dependencias necesarias
RUN apt-get update \
    && apt-get install -y \
        build-essential \
        git \
        postgresql-server-dev-15 \
    && rm -rf /var/lib/apt/lists/*

# Clonar e instalar pgvector
RUN git clone --branch v0.8.0 https://github.com/pgvector/pgvector.git \
    && cd pgvector \
    && make \
    && make install

# Configurar PostgreSQL para cargar pgvector
RUN echo "shared_preload_libraries = 'vector'" >> /usr/share/postgresql/postgresql.conf.sample 