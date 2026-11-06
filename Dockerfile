FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
RUN npm run build

FROM dunglas/frankenphp:latest-php8.4
RUN install-php-extensions pdo_pgsql intl zip opcache

WORKDIR /app

COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build
COPY . .

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
RUN chown -R www-data:www-data .

ENV APP_ENV=production
ENV LOG_CHANNEL=stderr

EXPOSE 80

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=80
