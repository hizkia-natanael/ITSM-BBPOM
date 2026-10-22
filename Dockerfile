# ==============================================================================
# Stage 1: Frontend Asset Builder (Node.js + Vite)
# ==============================================================================
FROM node:20-alpine AS frontend-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY app ./app
COPY resources ./resources
COPY public ./public

RUN npm run build

# ==============================================================================
# Stage 2: PHP-FPM 8.3 Application Runtime
# ==============================================================================
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    curl \
    freetype-dev \
    gmp-dev \
    icu-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxml2-dev \
    libzip-dev \
    mysql-client \
    oniguruma-dev \
    tzdata \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        gmp \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo_mysql \
        xml \
        zip \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY . .
COPY --from=frontend-builder /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && cp -r /var/www/html/public /opt/public-dist \
    && mkdir -p storage/app/private/attachments \
        storage/app/private/ticket-responses \
        storage/app/public/attachments \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/php/local.ini /usr/local/etc/php/conf.d/99-bbpom.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
