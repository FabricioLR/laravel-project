FROM php:8.4-fpm AS base
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip mbstring intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt-get install -y nodejs && apt-get clean
RUN docker-php-ext-install pdo pdo_pgsql pgsql zip mbstring intl
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www


FROM base AS dev
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["entrypoint.sh"]


FROM base AS build-assets
COPY . .
RUN npm install && npm run build


FROM base AS test
COPY . .
COPY --from=build-assets /app/public/build ./public/build
RUN composer install
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
CMD ["php-fpm"]


FROM base AS prod
COPY . .
COPY --from=build-assets /app/public/build ./public/build
RUN composer install
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
CMD ["php-fpm"]