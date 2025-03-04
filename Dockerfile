ARG PHP_VERSION=7.4
FROM php:${PHP_VERSION}-cli

ARG PHP_VERSION

LABEL org.opencontainers.image.source=https://github.com/bulkgate/plugin
LABEL php_version=${PHP_VERSION}

RUN apt update && \
    apt install -y libicu-dev git zip unzip && \
    docker-php-ext-install intl

RUN curl -sS https://raw.githubusercontent.com/composer/getcomposer.org/f3108f64b4e1c1ce6eb462b159956461592b3e3e/web/installer | php && \
    mv composer.phar /usr/local/bin/composer
