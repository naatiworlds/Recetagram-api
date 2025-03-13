#!/bin/sh

echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

# Asegurar permisos correctos
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/bootstrap/cache

# Crear archivo de log si no existe
touch /var/www/html/storage/logs/laravel.log
chown www-data:www-data /var/www/html/storage/logs/laravel.log
chmod 664 /var/www/html/storage/logs/laravel.log

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

# Reemplazar el puerto en la configuración de nginx
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/nginx.conf

echo "Starting supervisord"
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf