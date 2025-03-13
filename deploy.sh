#!/usr/bin/env bash

echo "Ejecutando Composer..."
composer install --no-dev --working-dir=/var/www/html

echo "Cacheando configuración..."
php artisan config:cache
php artisan route:cache


echo "Ejecutando migraciones..."
php artisan migrate --force
php artisan db:seed --force