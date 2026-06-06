FROM php:8.4-fpm-alpine

# Instalacja niezbędnych pakietów systemowych i bibliotek
# (ctype, iconv, pcre, session, simplexml, tokenizer są już wbudowane w rdzeń obrazu)
RUN apk add --no-cache \
    libpq-dev \
    icu-dev \
    # docker-php-ext-install instaluje dodatkowe, niewbudowane moduły
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    opcache

# Walidacja: Upewniamy się, że PHP posiada wszystkie wymagane przez Ciebie rozszerzenia
RUN echo "Sprawdzanie wymaganych rozszerzeń..." && \
    php -m | grep -iE 'ctype|iconv|pcre|session|simplexml|tokenizer' | wc -l | grep -q '6' || \
    (echo "Brak jednego z wymaganych rozszerzeń!" && exit 1)

# Instalacja Composera
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html