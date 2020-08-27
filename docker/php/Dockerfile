FROM php:7.4-fpm AS jusbrasil_php

COPY conf/php.ini   /usr/local/etc/php/conf.d/
COPY conf/pool.conf /usr/local/etc/php/

RUN apt-get update && \
    apt-get install -y libzip-dev zlib1g-dev chromium curl git && \
    docker-php-ext-install zip

WORKDIR /srv/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

ENV PANTHER_NO_SANDBOX 1

# Not mandatory, but recommended
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'