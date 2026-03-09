FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    unzip \
    git \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env
RUN sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
RUN sed -i 's/^DB_DATABASE=.*/#DB_DATABASE=/' .env
RUN php artisan key:generate
RUN touch database/database.sqlite
RUN php artisan migrate --force
RUN chmod -R 777 storage bootstrap/cache database

RUN npm ci
RUN npm run build

CMD php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
