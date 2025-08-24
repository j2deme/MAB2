# MAB2

Aplicación web para la gestión académica y administrativa de carreras, materias, grupos, semestres y usuarios.

## Descripción

MAB2 es una plataforma desarrollada en Laravel que permite administrar y visualizar información académica de una institución educativa. Incluye módulos para la gestión de movimientos, usuarios, carreras, materias, grupos y semestres.

## Funcionalidades principales

-   Gestión de usuarios y roles
-   Administración de carreras y materias
-   Control de grupos y semestres
-   Registro y consulta de movimientos académicos
-   Paneles y tablas interactivas con Livewire

## Tecnologías utilizadas

-   Laravel (backend y framework principal)
-   Livewire (componentes interactivos)
-   Tailwind CSS (estilos)
-   Docker (contenedores)
-   SQLite (base de datos por defecto)

## Instalación y configuración

1. Clona el repositorio:

```sh
git clone https://github.com/j2deme/MAB2.git
cd MAB2
```

2. Copia el archivo de entorno y configura tus variables:

```sh
cp .env.example .env
# Edita .env según tus necesidades
```

3. Instala las dependencias:

```sh
composer install
npm install && npm run build
```

4. Genera la clave de la aplicación:

```sh
php artisan key:generate
```

5. Ejecuta las migraciones y seeders:

```sh
php artisan migrate --seed
```

## Uso con Docker

La aplicación incluye configuración para ejecutarse en contenedores Docker.

1. Levanta los contenedores:

```sh
docker-compose up -d
```

2. Accede al contenedor web para ejecutar comandos de Laravel:

```sh
docker exec mab2-web-1 php artisan migrate --seed
docker exec mab2-web-1 php artisan key:generate
```

3. La aplicación estará disponible en [http://localhost:8091](http://localhost:8091) (puerto configurable en docker-compose.yml).

## Uso con Docker

La aplicación incluye configuración para ejecutarse en contenedores Docker.

1. Levanta los contenedores:

    ```sh
    docker-compose up -d
    ```

2. Accede al contenedor web para ejecutar comandos de Laravel:

    ```sh
    docker exec mab2-web-1 php artisan migrate --seed
    docker exec mab2-web-1 php artisan key:generate
    ```

3. La aplicación estará disponible en [http://localhost:8091](http://localhost:8091) (puerto configurable en docker-compose.yml).

## Estructura de carpetas

-   `app/Models`: Modelos Eloquent
-   `app/Livewire`: Componentes Livewire
-   `app/Console/Commands`: Comandos personalizados
-   `database/migrations`: Migraciones de base de datos
-   `resources/views`: Vistas Blade
-   `routes/`: Archivos de rutas

## Contribuciones

El presente proyecto es de contexto educativo y no se aceptan contribuciones externas.

## Licencia

Este proyecto está bajo la licencia MIT.

---

# 🐛 Instructivo para Resolución de Problemas Comunes en Docker + Laravel

## 📋 Índice de Problemas y Soluciones

### 1. Error: `Class "DOMDocument" not found`

**Causa:** Falta la extensión DOM de PHP en el contenedor
**Solución:**

```dockerfile
# En Dockerfile
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    && docker-php-ext-install dom xml
```

### 2. Error: `Connection refused` o `Temporary failure in name resolution`

**Causa:** Contenedores en redes diferentes o resolución DNS incorrecta
**Solución:**

```bash
# Verificar redes
docker network ls
docker network inspect [nombre_red]

# Conectar contenedores a misma red
docker network connect [nombre_red] [nombre_contenedor]

# Verificar conectividad
docker exec [contenedor] ping [otro_contenedor]
docker exec [contenedor] nslookup [otro_contenedor]
```

### 3. Error: `htmlspecialchars(): Argument must be of type string, Closure given`

**Causa:** Inyección de Closures en el contexto de excepciones de Laravel
**Solución de emergencia:**

```bash
# Parche nuclear - Deshabilitar contexto problemático
docker exec [contenedor] bash -c 'cat > /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/resources/exceptions/renderer/components/context.blade.php << "EOF"
{{-- Contexto deshabilitado --}}
<div style="display: none;"></div>
EOF'

# Limpieza completa de cache
docker exec [contenedor] bash -c '
php artisan optimize:clear
composer dump-autoload
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*
'
```

### 4. Error: `a2enmod: not found` en construcción

**Causa:** Uso de imagen incorrecta (FPM instead of Apache)
**Solución:**

```dockerfile
# Usar imagen Apache en lugar de FPM
FROM php:8.3.10-apache AS web  # ✅ Correcto
# FROM php:8.3.10-fpm AS web   # ❌ Incorrecto
```

### 5. Error: `npm ERR! code EBADENGINE`

**Causa:** Versiones incompatibles de Node.js/npm
**Solución:**

```dockerfile
# Instalar Node.js moderno correctamente
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest
```

### 6. Error: `Undefined variable $context`

**Causa:** Variable de contexto no disponible en vistas de excepción
**Solución:**

```bash
# Crear/editar Handler de excepciones
docker exec [contenedor] bash -c 'cat > /var/www/html/app/Exceptions/Handler.php << "EOF"
<?php
namespace App\Exceptions;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    protected function prepareExceptionContext(Throwable $e)
    {
        $context = parent::prepareExceptionContext($e);
        $sanitized = [];
        foreach ($context as $key => $value) {
            if ($value instanceof \Closure) {
                $sanitized[$key] = "[Closure]";
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
}
EOF'
```

## 🔧 Comandos Esenciales de Diagnóstico

### Verificación de Redes

```bash
# Listar redes y contenedores
docker network ls
docker network inspect [nombre_red]
docker ps --format "table {{.Names}}\t{{.Networks}}"

# Diagnosticar conectividad
docker exec [contenedor] ping [otro_contenedor]
docker exec [contenedor] nslookup [otro_contenedor]
docker exec [contenedor] cat /etc/resolv.conf
```

### Verificación de PHP

```bash
# Versiones y módulos
docker exec [contenedor] php -v
docker exec [contenedor] php -m

# Verificar configuración
docker exec [contenedor] php --ini
docker exec [contenedor] php -i | grep opcache
```

### Verificación de Laravel

```bash
# Estado de la aplicación
docker exec [contenedor] php artisan env
docker exec [contenedor] php artisan route:list | head -5
docker exec [contenedor] php artisan tinker
>>> DB::connection()->getPdo()

# Limpieza de cache
docker exec [contenedor] php artisan optimize:clear
docker exec [contenedor] composer dump-autoload
```

### Verificación de Base de Datos

```bash
# Desde contenedor de aplicación
docker exec [contenedor] mysql -h [db_host] -u [user] -p -e "SELECT 1;"

# Desde contenedor de base de datos
docker exec [db_contenedor] mysql -u root -p -e "SHOW DATABASES;"
```

## 🚀 Flujo de Resolución de Problemas

### Paso 1: Identificación

```bash
# Verificar logs de aplicación
docker exec [contenedor] tail -f storage/logs/laravel.log

# Verificar logs del servidor web
docker exec [contenedor] tail -f /var/log/nginx/error.log
```

### Paso 2: Contención

```bash
# Parche temporal si es crítico
docker exec [contenedor] [comando_parche]

# Limpieza de cache
docker exec [contenedor] php artisan optimize:clear
```

### Paso 3: Diagnóstico Profundo

```bash
# Comparar entornos
docker exec [contenedor] php -v
docker exec [contenedor] php -m

# Verificar variables de entorno
docker exec [contenedor] cat .env | grep -E "DB_|APP_|REDIS_"
```

### Paso 4: Solución Permanente

```dockerfile
# Modificar Dockerfile para prevenir recurrencia
# [Agregar instalación de extensiones necesarias]
# [Corregir versiones de dependencias]
```

## 📝 Mejores Prácticas Preventivas

### Dockerfile Robustecido

```dockerfile
# Especificar versiones exactas
FROM php:8.3.10-apache

# Instalar extensiones esenciales
RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install dom xml pdo pdo_mysql zip

# Instalar Node.js correctamente
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage
```

### docker-compose.yml Optimizado

```yaml
version: "3.8"
services:
    web:
        build: .
        networks:
            - app-network
        depends_on:
            - db

    db:
        image: mariadb:12
        networks:
            - app-network
        environment:
            MARIADB_ROOT_PASSWORD: ${DB_PASSWORD}
            MARIADB_DATABASE: ${DB_DATABASE}

networks:
    app-network:
        driver: bridge
```

### Script de Health Check

```bash
#!/bin/bash
# health-check.sh
echo "=== DOCKER STATUS ==="
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

echo "=== NETWORK VERIFICATION ==="
docker network inspect app-network | grep -E "Name|IPv4Address"

echo "=== APPLICATION STATUS ==="
docker exec [contenedor] php artisan env | grep -E "Environment|Database"
```

## 🆘 Resolución Rápida de Crisis

### Si la aplicación no carga:

1. **Parche inmediato:** Aplicar parche nuclear del Closure
2. **Limpiar cache:** `php artisan optimize:clear`
3. **Verificar redes:** `docker network inspect`
4. **Verificar BD:** `php artisan tinker` → `DB::connection()->getPdo()`

### Si la construcción falla:

1. **Verificar versiones:** Asegurar consistencia PHP/Node.js
2. **Reconstruir sin cache:** `docker-compose build --no-cache`
3. **Verificar logs de construcción:** `docker-compose build --no-cache --progress=plain`
