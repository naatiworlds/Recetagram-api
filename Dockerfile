FROM webdevops/php-nginx:8.2

WORKDIR /var/www/html

# Copiar todos los archivos del proyecto al contenedor
COPY . .

# Dar permisos de ejecución al script de despliegue
RUN chmod +x deploy.sh

# Instalar dependencias y optimizar Laravel
RUN composer install --no-dev --optimize-autoloader

# Definir el script de despliegue como comando de inicio
CMD ["./deploy.sh"]
