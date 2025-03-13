FROM richarvey/nginx-php-fpm:3.1.4

COPY . .

# Configuración de PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Agregar repositorio para PHP 8.2
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community \
    php82 \
    php82-fpm \
    php82-pdo \
    php82-pdo_mysql \
    php82-tokenizer \
    php82-xml \
    php82-dom \
    php82-xmlwriter \
    php82-session \
    php82-mbstring \
    supervisor

# Configurar PHP-FPM
RUN rm -f /usr/local/sbin/php-fpm && \
    ln -s /usr/sbin/php-fpm82 /usr/local/sbin/php-fpm

# Crear directorios y establecer permisos
RUN mkdir -p /etc/supervisor/conf.d /run/php && \
    mkdir -p /var/www/html/storage/framework/cache && \
    mkdir -p /var/www/html/storage/framework/sessions && \
    mkdir -p /var/www/html/storage/framework/views && \
    mkdir -p /var/www/html/bootstrap/cache && \
    chown -R nobody:nobody /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache

# Instalar composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos de configuración
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Script de inicio
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80