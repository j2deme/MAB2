#!/bin/bash
set -e

echo "Obteniendo cambios de Git..."
git pull

CHANGED_FILES=$(git diff --name-only HEAD@{1} HEAD)
NEED_REBUILD=false

if echo "$CHANGED_FILES" | grep -qE "resources/(js|css)|vite.config.js|tailwind.config.js|composer.json|composer.lock|Dockerfile|docker-compose.yml"; then
    NEED_REBUILD=true
fi

if [ "$NEED_REBUILD" = true ]; then
    echo "1. Limpiando posibles webshells residuales..."
    find storage/ -maxdepth 2 -type f -name "*.php" ! -path "storage/framework/views/*" -exec rm -f {} + || true

    echo "2. Construyendo nueva imagen mab:2.0 (Producción sigue activa)..."
    docker compose build --no-cache

    echo "3. Validando la nueva imagen en un contenedor de prueba temporal..."
    docker rm -f mab-test-temp 2>/dev/null || true

    # Obtener el nombre de red de cualquier contenedor del proyecto en ejecución
    CONTAINER_ID=$(docker compose ps -q web 2>/dev/null | head -n 1)
    if [ -n "$CONTAINER_ID" ]; then
        ACTIVE_NETWORK=$(docker inspect "$CONTAINER_ID" --format '{{range $k, $v := .NetworkSettings.Networks}}{{$k}}{{end}}' 2>/dev/null)
    fi
    ACTIVE_NETWORK=${ACTIVE_NETWORK:-mab_app-network}

    # Levantamos el contenedor temporal para prueba de integración
    docker run -d --name mab-test-temp \
        --network "$ACTIVE_NETWORK" \
        --env-file .env \
        --health-cmd="curl -f http://localhost/health || exit 1" \
        --health-interval=2s \
        --health-retries=10 \
        mab:2.0

    echo "4. Esperando Health Check en contenedor de prueba..."
    SUCCESS=false
    for i in {1..15}; do
        STATUS=$(docker inspect --format='{{json .State.Health.Status}}' mab-test-temp 2>/dev/null || echo "unknown")
        if [ "$STATUS" = "\"healthy\"" ]; then
            SUCCESS=true
            break
        fi
        echo "Prueba en progreso ($i/15)... Estado: $STATUS"
        sleep 2
    done

    # Destruir el contenedor temporal de prueba
    docker rm -f mab-test-temp >/dev/null 2>&1

    if [ "$SUCCESS" = true ]; then
        echo "¡Prueba EXITOSA! Reemplazando contenedor de producción..."
        docker compose up -d
        echo "Despliegue completado con éxito y cero caída."
    else
        echo "ERROR: La nueva imagen falló la validación. ABORTANDO DESPLIEGUE."
        echo "Tu producción NO fue tocada y sigue funcionando normalmente."
        exit 1
    fi
else
    echo "Solo cambios en PHP/Blade. No se requiere rebuild."
fi
