#!/bin/bash

CONTAINER="mab-web-1"
TIMESTAMP=$(date "+%Y-%m-%d %H:%M:%S")
LOG_OUTPUT=""
HAS_ALERT=false

# Helper para acumular logs
add_log() {
    LOG_OUTPUT="${LOG_OUTPUT}\n$1"
}

# 1. Verificar Estado
if [ $(docker ps -q -f name=^/${CONTAINER}$) ]; then
    HEALTH=$(docker inspect --format='{{json .State.Health.Status}}' $CONTAINER 2>/dev/null || echo "\"running\"")
    if [ "$HEALTH" != "\"healthy\"" ] && [ "$HEALTH" != "\"running\"" ]; then
        HAS_ALERT=true
        add_log "[ESTADO] ⚠️ El contenedor está en estado no saludable: ${HEALTH}"
    fi
else
    HAS_ALERT=true
    add_log "[ESTADO] 🚨 ALERTA CRÍTICA: El contenedor $CONTAINER está APAGADO o no existe."
fi

# 2. Verificar CPU
CPU_USAGE=$(docker stats $CONTAINER --no-stream --format "{{.CPUPerc}}" 2>/dev/null | tr -d '%' | cut -d'.' -f1)
CPU_USAGE=${CPU_USAGE:-0}
if [ "$CPU_USAGE" -gt 80 ]; then
    HAS_ALERT=true
    add_log "[RECURSOS] ⚠️ Consumo de CPU elevado: ${CPU_USAGE}%"
fi

# 3. Verificar Procesos Sospechosos
PROCS=$(docker top $CONTAINER -o pid,user,args 2>/dev/null | tail -n +2 || true)
SUSPICIOUS_PROCS=$(echo "$PROCS" | grep -vE "apache2|php|bash|sh|docker-entrypoint|find|cat" || true)

if [ -n "$SUSPICIOUS_PROCS" ]; then
    HAS_ALERT=true
    add_log "[PROCESOS] 🚨 PROCESOS DESCONOCIDOS DETECTADOS:\n$SUSPICIOUS_PROCS"
fi

# 4. Escaneo de Webshells en storage/
MALWARE_FILES=$(docker exec $CONTAINER find storage/ -maxdepth 3 -type f -name "*.php" ! -path "storage/framework/views/*" ! -path "storage/framework/cache/*" 2>/dev/null || true)

if [ -n "$MALWARE_FILES" ]; then
    HAS_ALERT=true
    add_log "[SEGURIDAD] 🚨 ARCHIVOS .PHP ENCONTRADOS EN STORAGE:\n$MALWARE_FILES"
fi

# 5. Prueba de Read-Only
TEST_WRITE=$(docker exec $CONTAINER touch /var/www/html/test_security_check.php 2>&1 || true)
if ! echo "$TEST_WRITE" | grep -q "Read-only file system"; then
    HAS_ALERT=true
    add_log "[SEGURIDAD] 🚨 PELIGRO: La protección Read-Only falló o está desactivada."
fi

# Si se detectó alguna anomalía, escribir en el log con fecha y hora
if [ "$HAS_ALERT" = true ]; then
    echo -e "===================================================="
    echo -e " FECHA Y HORA DE ALERTA: $TIMESTAMP"
    echo -e "===================================================="
    echo -e "$LOG_OUTPUT"
    echo -e "\n"
fi
