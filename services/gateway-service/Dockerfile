FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    postgresql-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize --no-scripts

RUN mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache vendor \
    && printf "[www]\ncatch_workers_output = yes\ndecorate_workers_output = no\n" > /usr/local/etc/php-fpm.d/zz-log.conf

USER www-data

EXPOSE 9000

CMD [ "php-fpm" ]