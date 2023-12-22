ARG PHP_VERSION
FROM ghcr.io/nulldark/php:${PHP_VERSION}

RUN set -xe; \
    apk add --update --no-cache \
        git \
        php83-apcu \
        php83-zip \
        php83-soap

COPY php.ini /etc/php83/
COPY conf.d/* /etc/php83/conf.d/

EXPOSE 9000