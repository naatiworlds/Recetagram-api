#!/bin/sh

echo "Running composer"
composer install --no-dev --working-dir=/var/www/html
# Crear enlace simbólico para el storage
php artisan storage:link
# Asegurar permisos correctos
chown -R nobody:nobody /var/www/html/storage
chmod -R 755 /var/www/html/storage
chown -R nobody:nobody /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/bootstrap/cache

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force


# Ejecutar el seeder de datos de ejemplo
echo "Running UsersAndPostsSeeder..."
php artisan db:seed --class=UsersAndPostsSeeder --force

# Reemplazar el puerto en la configuración de nginx
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/nginx.conf

echo "Starting supervisord"
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf