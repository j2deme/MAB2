FROM php:8.3-apache AS web
RUN apt-get update && apt-get install -y \
  libfreetype-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y \
  libzip-dev \
  zip \
  unzip \
  git

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip bcmath

# Configure Apache DocumentRoot to point to Laravel public directory
# and update Apache configuration files
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the app code
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install app dependencies
# RUN composer update
RUN composer install --no-dev

RUN apt-get update && apt-get install -y \
  software-properties-common \
  npm
RUN npm install npm@latest -g && \
  npm install n -g && \
  n latest

RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN php artisan optimize
RUN php artisan config:cache
RUN php artisan route:cache