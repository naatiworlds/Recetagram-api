#!/usr/bin/env bash
set -e  # Detiene el script si ocurre un error

# Ejecuta Composer para instalar las dependencias de producción
echo "Ejecutando Composer..."
composer install --no-dev --working-dir=/var/www/html

# Asegúrate de que PHP-FPM esté corriendo
echo "Iniciando PHP-FPM..."
php-fpm &  # Esto asegura que PHP-FPM esté en ejecución

# Cachea la configuración y las rutas
echo "Cacheando configuración..."
php artisan config:cache
php artisan route:cache

# Ejecuta las migraciones si es necesario
echo "Ejecutando migraciones..."
php artisan migrate --force

# Inicia el servidor web integrado de PHP en el puerto correcto
echo "Iniciando servidor PHP en el puerto ${PORT}..."
php -S 0.0.0.0:${PORT} -t /var/www/html/public &

# Verifica que los servicios estén en ejecución
echo "Verificando si PHP-FPM está en ejecución..."
pgrep php-fpm || echo "PHP-FPM no está en ejecución"

echo "Verificando si el servidor PHP está en ejecución..."
pgrep php || echo "Servidor PHP no está en ejecución"

echo "Despliegue completado."