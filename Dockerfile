FROM php:8.3-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libpq-dev libzip-dev libmcrypt-dev libssl-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

RUN apt-get update && apt-get install -y default-mysql-client


# Instalar Node.js para Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . /var/www

# Instalar dependencias de Node y compilar assets
RUN npm install && npm run build

# Crear directorios necesarios si no existen
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache
RUN mkdir -p /var/www/storage/app/public /var/www/storage/framework/cache /var/www/storage/framework/sessions /var/www/storage/framework/views /var/www/storage/logs

# Copiar solo imágenes estáticas a public (favicon, logo, etc.)
RUN mkdir -p /var/www/public/images \
    && if [ -d /var/www/resources/images ]; then cp -r /var/www/resources/images/* /var/www/public/images/; fi

# Asignar permisos correctos
RUN chown -R www-data:www-data /var/www
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
CMD ["/usr/local/bin/entrypoint.sh"]
