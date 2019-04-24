FROM ubuntu:latest

RUN DEBIAN_FRONTEND=noninteractive

RUN apt-get update &&\
    apt-get upgrade -y &&\
    apt-get install -y tzdata &&\
    apt-get install -y \
    wget apt-transport-https lsb-release ca-certificates apt-utils

RUN apt-get update &&\
    apt-get upgrade -y &&\
    apt-get install -y \
    apache2 php7.2 php7.2-apcu php7.2-mbstring php7.2-curl php7.2-gd \
    php7.2-imagick php7.2-intl php7.2-bcmath \
    php7.2-mysql php7.2-xdebug php7.2-xml php7.2-zip curl

# apache2 enviroment path
ENV APACHE_CONFDIR /etc/apache2
ENV APACHE_ENVVARS $APACHE_CONFDIR/envvars
RUN set -ex \
    && . "$APACHE_ENVVARS" \
    && ln -sfT /dev/stderr "$APACHE_LOG_DIR/error.log" \
    && ln -sfT /dev/stdout "$APACHE_LOG_DIR/access.log" \
    && ln -sfT /dev/stdout "$APACHE_LOG_DIR/other_vhosts_access.log"

# apache mods enable
RUN a2enmod rewrite \
    && a2enmod headers

EXPOSE 80

WORKDIR /var/www/html
COPY --chown=www-data:www-data . /.

ARG PUID=33
ARG PGID=33
RUN groupmod -g $PGID www-data \
    && usermod -u $PUID www-data
RUN usermod -u 1000 www-data
RUN chown -R www-data:www-data /var/www/html
RUN chmod 755 /var/www/html
# Laravel permission storage and bootstrap
#RUN chown -R $USER:www-data /var/www/html/storage /var/www/html/bootstrap/cache
#RUN chgrp -R www-data /var/www/html/storage /var/www/html/bootstrap/cache
#RUN chmod -R ug+rwx /var/www/html/storage var/www/html/bootstrap/cache


CMD ["/usr/sbin/apachectl", "-D", "FOREGROUND"]
