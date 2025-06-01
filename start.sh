#!/bin/sh

echo "Running composer..."
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

# Crear enlace simbólico para storage
php artisan storage:link

# Verificar y crear directorios necesarios
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/bootstrap/cache

# Asegurar permisos correctos
chown -R nobody:nobody /var/www/html/storage
chmod -R 755 /var/www/html/storage
chown -R nobody:nobody /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/bootstrap/cache

echo "Caching config..."
php artisan config:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Verificar conexión a PostgreSQL antes de migrar
echo "Checking database connection..."
php artisan tinker --execute="DB::connection()->getPdo();" || { echo "Database connection failed!"; exit 1; }

echo "Running migrations..."
php artisan migrate --force

# Restaurar caché después de migraciones
php artisan config:cache
php artisan route:cache

# Reemplazar el puerto en la configuración de nginx
echo "Configuring Nginx..."
echo "Using PORT: $PORT"
sed -i "s/\${PORT:-80}/$PORT/g" /etc/nginx/nginx.conf

echo "Starting supervisord..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
