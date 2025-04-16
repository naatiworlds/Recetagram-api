FROM alpine:latest

# ------------------------------
# 1. Variables y actualización base
# ------------------------------
ENV PHP_VERSION=8.2
ENV APP_DIR=/var/www/html

RUN apk update && apk add --no-cache \
    nginx \
    php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-pdo \
    php${PHP_VERSION}-pdo_mysql \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-session \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-fileinfo \
    php${PHP_VERSION}-openssl \
    php${PHP_VERSION}-ctype \
    php${PHP_VERSION}-json \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-phar \
    php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-zip \
    supervisor \
    bash \
    curl \
    unzip \
    git

# ------------------------------
# 2. Configuración del sistema
# ------------------------------
RUN mkdir -p /run/nginx /var/log/supervisor $APP_DIR
COPY . $APP_DIR
WORKDIR $APP_DIR

# ------------------------------
# 3. Permisos y directorios Laravel
# ------------------------------
RUN mkdir -p $APP_DIR/storage \
    $APP_DIR/bootstrap/cache && \
    chown -R nobody:nobody $APP_DIR && \
    chmod -R 755 $APP_DIR/storage $APP_DIR/bootstrap/cache

# ------------------------------
# 4. Instalar Composer
# ------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------
# 5. Copiar configuraciones
# ------------------------------
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY start.sh /start.sh
RUN chmod +x /start.sh

# ------------------------------
# 6. Exponer el puerto y comando final
# ------------------------------
EXPOSE 80
CMD ["/start.sh"]
