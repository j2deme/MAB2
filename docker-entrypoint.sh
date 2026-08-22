#!/bin/bash

# Asegurar permisos correctos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Ejecutar Artisan solo cuando el contenedor ya tiene .env
php artisan optimize
php artisan config:cache
php artisan route:cache

# Iniciar Apache
apache2-foreground
