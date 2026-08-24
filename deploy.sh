#!/bin/bash

git pull

CHANGED_FILES=$(git diff --name-only HEAD@{1} HEAD)
NEED_REBUILD=false

if echo "$CHANGED_FILES" | grep -qE "resources/(js|css)|vite.config.js|tailwind.config.js"; then
    NEED_REBUILD=true
fi

if echo "$CHANGED_FILES" | grep -qE "composer.json|composer.lock"; then
    NEED_REBUILD=true
fi

if echo "$CHANGED_FILES" | grep -qE "Dockerfile|docker-compose.yml"; then
    NEED_REBUILD=true
fi

if [ "$NEED_REBUILD" = true ]; then
    echo "Reconstruyendo imagen..."
    docker compose build --no-cache

    echo "Levantando contenedor nuevo..."
    docker compose up -d

    echo "Esperando health check..."
    until [ "$(docker inspect --format='{{json .State.Health.Status}}' mab-web-1)" = "\"healthy\"" ]; do
        echo "Contenedor aún no está healthy..."
        sleep 2
    done

    echo "Contenedor healthy. Despliegue completado."
else
    echo "Solo cambios en PHP/Blade. No se requiere rebuild."
fi
