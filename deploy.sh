#!/usr/bin/env bash

echo "Ejecutando Composer..."
composer install --no-dev --working-dir=/var/www/html


service nginx start


echo "Cacheando configuración..."
php artisan config:cache
php artisan route:cache


echo "Ejecutando migraciones..."
php artisan migrate --force