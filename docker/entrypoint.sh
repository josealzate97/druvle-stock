#!/usr/bin/env sh
set -e

# Directorios base
APP_DIR="/var/www"

# Copiar solo imágenes estáticas si es necesario
if [ -d "$APP_DIR/resources/images" ]; then
  mkdir -p "$APP_DIR/public/images"
  cp -r "$APP_DIR/resources/images/"* "$APP_DIR/public/images/" 2>/dev/null || true
fi

# Permisos
chown -R www-data:www-data "$APP_DIR/public" 2>/dev/null || true

# Iniciar PHP-FPM
exec php-fpm