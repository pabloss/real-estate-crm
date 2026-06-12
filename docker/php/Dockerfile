FROM php:8.4-fpm-alpine

# Przyjmujemy UID i GID ze zmiennych (domyślnie 1000 - standard dla Ubuntu)
ARG UID=1000
ARG GID=1000

# Instalujemy 'shadow', aby móc nadpisać UID/GID użytkownika www-data
RUN apk add --no-cache shadow \
    && usermod -u ${UID} www-data \
    && groupmod -g ${GID} www-data

# Instalacja niezbędnych pakietów systemowych
RUN apk add --no-cache \
    libpq-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    opcache \
    gd

# Sprawdzanie wymaganych rozszerzeń
RUN echo "Sprawdzanie wymaganych rozszerzeń..." && \
    php -m | grep -iE 'ctype|iconv|pcre|session|simplexml|tokenizer' | wc -l | grep -q '6' || \
    (echo "Brak jednego z wymaganych rozszerzeń!" && exit 1)

# Instalacja Composera
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Gwarantujemy, że katalog początkowy ma właściwego właściciela
RUN chown -R www-data:www-data /var/www/html