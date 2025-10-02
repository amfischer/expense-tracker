FROM php:8.3.11-fpm-alpine3.20

ARG user
ARG uid

RUN apk add --no-cache $PHPIZE_DEPS \
    freetype-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zip \
    linux-headers

# the install command for an extension must immediately follow the configure command
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install zip

# install redis
RUN pecl install redis-6.2.0 && docker-php-ext-enable redis

# install xdebug
RUN pecl install xdebug-3.4.3 && docker-php-ext-enable xdebug

# install composer from the composer image
COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer

# copy ini settings
COPY php.ini /usr/local/etc/php/php.ini
COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Create system user to run Composer and Artisan Commands
RUN  addgroup -g $uid $user \
    && adduser -D -u $uid -G $user $user \
    && addgroup $user www-data \
    && addgroup $user root

RUN mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

USER $user
