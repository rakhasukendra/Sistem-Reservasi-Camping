FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    npm \
    git \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /app

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev --optimize-autoloader

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

ENV APP_ENV=production
ENV LOG_CHANNEL=stderr

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000

