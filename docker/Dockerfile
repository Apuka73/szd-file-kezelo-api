FROM php:8.2-apache-bullseye

ENV TIMEZONE=Europe/London

ENV PHALCON_VERSION=5.6.2

RUN pecl channel-update pecl.php.net && \
    pecl install phalcon && \
    docker-php-ext-enable phalcon

RUN apt update && apt install -y libmemcached-dev libjpeg-dev libpng-dev zlib1g-dev memcached libzip-dev \
        && printf "\n" | pecl install memcached \
        && docker-php-ext-enable memcached

RUN docker-php-ext-configure gd --with-jpeg

RUN docker-php-ext-install gd pdo_mysql zip

RUN echo '' | pecl install apcu
RUN docker-php-ext-enable apcu


WORKDIR /build
RUN curl -s https://getcomposer.org/installer | php
RUN ln -s /build/composer.phar /usr/local/bin/composer
RUN ln -s /var/www/vendor/bin/phinx /usr/local/bin/phinx


RUN rm /var/log/apache2/access.log
RUN rm /var/log/apache2/other_vhosts_access.log

COPY php.ini /php.ini
RUN mv "/php.ini" "$PHP_INI_DIR/php.ini"

COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY entrypoint.sh /entrypoint.sh

RUN chmod 777 /entrypoint.sh
RUN a2enmod rewrite &&  service apache2 restart

WORKDIR /var/www

ENTRYPOINT "/entrypoint.sh"
