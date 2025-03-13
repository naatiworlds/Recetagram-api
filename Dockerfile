FROM richarvey/nginx-php-fpm:latest

# Copiar el código y el script deploy.sh
COPY . /var/www/html

# Asegurarse de que deploy.sh tenga permisos de ejecución
RUN chmod +x /var/www/html/deploy.sh

# Configuración de la imagen
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Configuración de Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Permitir que Composer se ejecute como root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Exponer el puerto que Render espera
EXPOSE 10000

# Ejecutar el script de despliegue
CMD ["./deploy.sh"]
