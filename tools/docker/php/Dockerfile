FROM php:5.6.40-alpine

RUN apk add gnu-libiconv --update-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted

ENV LD_PRELOAD="/usr/lib/preloadable_libiconv.so php"

ENV TZ=Asia/Krasnoyarsk

RUN apk add --no-cache git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install mysqli
