FROM php:7-alpine

RUN set -ex
RUN apk --update --no-cache add postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql

ADD https://getcomposer.org/composer.phar /usr/bin/composer
RUN chmod +x /usr/bin/composer

COPY . /www
WORKDIR /www

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install

CMD cli.php --help