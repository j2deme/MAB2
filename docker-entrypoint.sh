#!/bin/bash

# Asegurar permisos correctos
find storage/framework/views -maxdepth 1 -type f -name '*.php' -delete
chown -R www-data:www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 2775 {} +
find storage bootstrap/cache -type f -exec chmod 664 {} +

# Limpiar cachés previas para evitar desincronización
su -s /bin/bash www-data -c 'php artisan config:clear'
su -s /bin/bash www-data -c 'php artisan route:clear'
su -s /bin/bash www-data -c 'php artisan view:clear'

# Iniciar Apache
apache2-foreground
