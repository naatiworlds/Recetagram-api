#!/bin/sh

echo "Running composer"
composer install --no-dev --working-dir=/var/www/html
# Crear enlace simbólico para el storage
php artisan storage:link
# Asegurar permisos correctos
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/bootstrap/cache


echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan cache:clear
php artisan config:clear
composer dump-autoload
php artisan migrate --force
php artisan config:cache
php artisan route:cache



# Reemplazar el puerto en la configuración de nginx
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/nginx.conf

echo "Starting supervisord"
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf