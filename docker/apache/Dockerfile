FROM httpd:2

#RUN apt-get install apache2
#RUN a2enmod ssl && a2enmod rewrite
COPY httpd.conf /usr/local/apache2/conf/httpd.conf
COPY httpd-vhosts.conf /usr/local/apache2/conf/extra/httpd-vhosts.conf
RUN mkdir -p /etc/apache2/ssl
COPY ./ssl/cert.pem /etc/apache2/ssl/cert.pem
COPY ./ssl/cert-key.pem /etc/apache2/ssl/cert-key.pem

#ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/


#RUN pecl install xdebug && docker-php-ext-enable xdebug

#COPY --from=composer /usr/bin/composer /usr/bin/composer

EXPOSE 80

# Start Apache
CMD apachectl -D FOREGROUND
