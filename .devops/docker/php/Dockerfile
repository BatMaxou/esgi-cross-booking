# PHP
ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-fpm-alpine
ENV MEMORY_LIMIT "256M"
COPY ./php.ini $PHP_INI_DIR/conf.d/

# Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# PHP Extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN apk update && apk upgrade
RUN install-php-extensions pdo_mysql

WORKDIR /srv

CMD ["php-fpm", "-R", "-F"]
