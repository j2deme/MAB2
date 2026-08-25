#!/bin/bash

# Asegurar permisos correctos
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Limpiar cachés previas para evitar desincronización
su -s /bin/bash www-data -c 'php artisan config:clear'
su -s /bin/bash www-data -c 'php artisan route:clear'
su -s /bin/bash www-data -c 'php artisan view:clear'
find storage/framework/views -maxdepth 1 -type f -name '*.php' -delete

# Optimizar solo lo necesario
su -s /bin/bash www-data -c 'php artisan view:cache'

# Iniciar Apache
apache2-foreground
