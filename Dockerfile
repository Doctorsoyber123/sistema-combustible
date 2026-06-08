FROM php:8.2-apache

# ── Dependencias del sistema ──
RUN apt-get update && apt-get install -y \
        libzip-dev zip unzip git curl libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar rewrite
RUN a2enmod rewrite

# Cambiar DocumentRoot a /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copiar proyecto
COPY . /var/www/html/

WORKDIR /var/www/html

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos Laravel
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Script de arranque
RUN printf '#!/bin/bash\n\
set -e\n\
echo ">> Limpiando cache..."\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
echo ">> Recreando base de datos..."\n\
php artisan migrate:fresh --seed --force\n\
echo ">> Storage link..."\n\
php artisan storage:link 2>/dev/null || true\n\
echo ">> Cacheando configuracion..."\n\
php artisan config:cache\n\
echo ">> Arrancando Apache..."\n\
exec apache2-foreground\n' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]