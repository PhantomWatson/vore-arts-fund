FROM php:7.4-fpm

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

#EXPOSE 80
#EXPOSE 443
EXPOSE 9000

#RUN apt-get update \
#    && apt-get -y --no-install-recommends install  php7.4-mysql php7.4-intl php7.4-mbstring php7.4-sqlite3\
#    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions \
    bcmath \
    bz2 \
    curl \
    fileinfo \
    gd \
    intl \
    mbstring \
    mysqlnd \
    pdo \
    simplexml \
    zip

RUN docker-php-ext-install mysqli

RUN install-php-extensions @composer

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY ./90-xdebug.ini /etc/php.d/90-xdebug.ini

WORKDIR "/var/www/html/webroot"
