#!/bin/bash

# 1. Eliminar cualquier script PHP o backdoor inyectado directamente en storage/
find storage/ -maxdepth 2 -type f -name '*.php' ! -path 'storage/framework/views/*' -delete

# 2. Limpiar vistas compiladas
find storage/framework/views -maxdepth 1 -type f -name '*.php' -delete

# 3. Asignar permisos estrictos (carpetas 755, archivos 644)
chown -R www-data:www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 2775 {} +
find storage bootstrap/cache -type f -exec chmod 664 {} +

# 4. Crear protección .htaccess interna en storage
cat << 'EOF' > storage/.htaccess
Require all denied
<FilesMatch "\.(php|phtml|phar)$">
    Require all denied
</FilesMatch>
EOF

# 5. Limpiar cachés de Laravel
su -s /bin/bash www-data -c 'php artisan config:clear'
su -s /bin/bash www-data -c 'php artisan route:clear'
su -s /bin/bash www-data -c 'php artisan view:clear'

# 6. Iniciar Apache
apache2-foreground
