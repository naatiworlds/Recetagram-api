#!/bin/sh

echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force

# Asegurar permisos correctos
chown -R nginx:nginx /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Reemplazar el puerto en la configuración de nginx
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/nginx.conf

echo "Starting supervisord"
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf