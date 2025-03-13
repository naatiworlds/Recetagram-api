#!/usr/bin/env bash

# Ejecuta Composer para instalar las dependencias de producción
echo "Ejecutando Composer..."
composer install --no-dev --working-dir=/var/www/html

# Asegúrate de que PHP-FPM esté corriendo
echo "Iniciando PHP-FPM..."
php-fpm &  # Esto asegura que PHP-FPM esté en ejecución

# Inicia el servidor web integrado de PHP en el puerto correcto
echo "Iniciando el servidor web integrado de PHP..."
php -S 0.0.0.0:${PORT} -t /var/www/html/public &  # PHP servirá la aplicación en el puerto proporcionado por Render

# Cachea la configuración y las rutas
echo "Cacheando configuración..."
php artisan config:cache
php artisan route:cache

# Ejecuta las migraciones si es necesario
echo "Ejecutando migraciones..."
php artisan migrate --force
