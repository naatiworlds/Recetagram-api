#!/bin/sh

echo "Running composer"
composer install --no-dev --working-dir=/var/www/html

# Asegurar permisos correctos
chown -R nobody:nobody /var/www/html/storage
chmod -R 755 /var/www/html/storage
chown -R nobody:nobody /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/bootstrap/cache

echo "Waiting for database connection..."
while ! nc -z mysql 3306; do
  sleep 1
done

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