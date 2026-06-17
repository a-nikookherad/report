# Dockerfile - نسخه سبک برای ایران
FROM php:8.4-fpm-alpine AS base

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    mysql-client \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        zip \
        gd \
        bcmath \
        intl \
        opcache \
        pcntl

# نصب Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ایجاد کاربر
RUN addgroup -g ${GROUP_ID} sail \
    && adduser -D -u ${USER_ID} -G sail sail

WORKDIR /var/www/html

# فقط فایل‌های لازم برای نصب وابستگی‌ها رو اول کپی کن (برای کش بهتر)
COPY composer.json composer.lock ./
COPY package*.json ./

RUN chown -R sail:sail /var/www/html

USER sail

RUN composer install --optimize-autoloader --no-dev --prefer-dist --no-scripts \
    && npm ci --production || echo "npm skipped"

# حالا بقیه پروژه
COPY . .

RUN composer dump-autoload --optimize \
    && php artisan optimize:clear || true

USER root
RUN chown -R sail:sail storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

USER sail

EXPOSE 9000

CMD ["php-fpm"]
