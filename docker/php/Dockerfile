FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
      libzip \
      postgresql-libs \
      libmemcached-dev \
  && apk add --no-cache --virtual .build-deps \
      oniguruma-dev \
      libzip-dev \
      postgresql-dev \
      autoconf \
      gcc \
      g++ \
      make \
  && docker-php-ext-install \
      pdo \
      pdo_pgsql \
      zip \
      opcache \
  && apk del .build-deps \
  && curl -sS https://getcomposer.org/installer | php -- \
       --install-dir=/usr/local/bin --filename=composer \
  && composer --version

WORKDIR /var/www/html

CMD ["php-fpm"]
