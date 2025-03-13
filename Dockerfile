FROM richarvey/nginx-php-fpm:latest

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
    php81-xmlwriter

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuración de nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Script de inicio
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]