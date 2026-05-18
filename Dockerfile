FROM php:8.2-apache

# ── Dependencias del sistema ──
# libpq-dev es necesario para compilar pdo_pgsql (PostgreSQL).
# Se deja pdo_mysql tambien por si se cambia de motor mas adelante.
RUN apt-get update && apt-get install -y \
        libzip-dev zip unzip git curl libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar rewrite (Laravel lo necesita)
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

# Permisos para Laravel (storage y bootstrap/cache deben ser escribibles)
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ── Script de arranque ──
# Las migraciones se ejecutan en RUNTIME (no en build) porque la BD Postgres
# de Render solo es accesible cuando el contenedor esta corriendo.
# Tras cada reinicio: limpia toda la cache, migra, y re-cachea config para
# que los cambios en .env / Environment de Render se apliquen de inmediato.
RUN printf '#!/bin/bash\nset -e\necho ">> Limpiando cache..."\nphp artisan config:clear\nphp artisan cache:clear\nphp artisan route:clear\nphp artisan view:clear\necho ">> Ejecutando migraciones..."\nphp artisan migrate --force\necho ">> Seed inicial (solo si la BD esta vacia)..."\nphp artisan app:seed-if-empty\necho ">> Storage link..."\nphp artisan storage:link 2>/dev/null || true\necho ">> Cacheando configuracion..."\nphp artisan config:cache\necho ">> Arrancando Apache..."\nexec apache2-foreground\n' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]
