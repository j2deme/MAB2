FROM php:8.3-apache-bookworm AS base

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl

# Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install pdo_mysql zip bcmath dom mbstring exif pcntl gd xml

# Apache
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar código
COPY . /var/www/html
WORKDIR /var/www/html

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# Instalar dependencias PHP (incluye dev)
RUN composer install

# Instalar dependencias JS
RUN npm install

# Construir Vite
RUN npm run build

# Limpiar cache de Node
RUN rm -rf node_modules/.cache

# Permisos Laravel
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && find storage bootstrap/cache -type d -exec chmod 2775 {} + \
    && find storage bootstrap/cache -type f -exec chmod 664 {} +

# --- REGLAS DE SEGURIDAD Y BLINDAJE ---

# 1. Deshabilitar funciones peligrosas de PHP en todo el contenedor
RUN echo "disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_multi_exec,parse_ini_file,show_source" > /usr/local/etc/php/conf.d/hardening.ini

# 2. Configurar Apache para prohibir la ejecución de archivos PHP en la carpeta storage
RUN echo '<Directory "/var/www/html/storage">\n\
    AllowOverride None\n\
    Require all denied\n\
    <FilesMatch "\.(php|phtml|phar)$">\n\
    Require all denied\n\
    </FilesMatch>\n\
    </Directory>' > /etc/apache2/conf-available/block-storage-execution.conf \
    && a2enconf block-storage-execution

# Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
