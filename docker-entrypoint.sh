#!/bin/bash

# Asegurar permisos correctos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Limpiar cachés previas para evitar desincronización
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar solo lo necesario
php artisan view:cache

# Iniciar Apache
apache2-foreground
