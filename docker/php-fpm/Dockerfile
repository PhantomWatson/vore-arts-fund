FROM php:8.1-fpm

ARG WEB_USER
ARG WEB_GROUP
ARG PHP_ROOT_DIR

COPY www.conf ${PHP_ROOT_DIR}/php-fpm.d/www.conf

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apt-get update
#    && apt-get -y --no-install-recommends install  php7.4-mysql php7.4-intl php7.4-mbstring php7.4-sqlite3\
#    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions \
    bcmath \
    bz2 \
    curl \
    dom \
    fileinfo \
    gd \
    intl \
    mbstring \
    mysqlnd \
    pdo_mysql \
    pdo \
    simplexml \
    zip

RUN docker-php-ext-install mysqli

RUN docker-php-ext-enable sodium

RUN install-php-extensions @composer

RUN pecl install xdebug
# && docker-php-ext-enable xdebug

RUN curl -sL https://deb.nodesource.com/setup_14.x | bash

RUN apt-get install nodejs -y

# Used so the database backup script has acces to mysqldump
RUN apt-get install default-mysql-client -y

WORKDIR "/var/www/html/webroot"

RUN usermod -u 1000 ${WEB_USER} \
 && groupmod -g 1000 ${WEB_GROUP} \
 && chgrp -R staff ${PHP_ROOT_DIR}/php-fpm.d/www.conf
