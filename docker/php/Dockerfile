FROM php:8.3-cli

RUN docker-php-ext-install pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN apt-get update && apt-get install -y git
RUN composer install --no-dev --optimize-autoloader --prefer-dist --classmap-authoritative

COPY . .

CMD ["php", "app/cli.php"]
