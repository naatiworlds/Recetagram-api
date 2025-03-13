#!/usr/bin/env bash
set -e  # Detiene el script si ocurre un error

# Ejecuta Composer para instalar las dependencias de producción
echo "Ejecutando Composer..."
composer install --no-dev --working-dir=/var/www/html

# Cachea la configuración y las rutas
echo "Cacheando configuración..."
php artisan config:cache
php artisan route:cache

# Ejecuta las migraciones si es necesario
echo "Ejecutando migraciones..."
php artisan migrate --force

# Inicia PHP-FPM
echo "Iniciando PHP-FPM..."
php-fpm &

# Inicia Nginx
echo "Iniciando Nginx..."
nginx -g "daemon off;"