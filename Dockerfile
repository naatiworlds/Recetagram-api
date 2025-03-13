FROM richarvey/nginx-php-fpm:3.1.4

COPY . .

# Configuración de PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Instalar dependencias
RUN apk add --no-cache \
    php81-pdo \
    php81-pdo_mysql \
    php81-tokenizer \
    php81-xml \
    php81-dom \
    php81-xmlwriter \
    supervisor

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio para supervisor
RUN mkdir -p /etc/supervisor/conf.d

# Copiar archivos de configuración
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de inicio
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]