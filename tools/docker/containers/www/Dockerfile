FROM php:7.0-apache

MAINTAINER Barry O'Donovan <barry.odonovan@inex.ie>

RUN apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y install libmcrypt-dev snmp libsnmp-dev libicu-dev librrd-dev \
        libyaml-dev git mysql-client npm nodejs-legacy joe wget \
        iputils-ping dnsutils && \
    apt-get -y autoremove --purge && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/*

RUN docker-php-source extract && \
    docker-php-ext-install -j$(nproc) mcrypt snmp intl bcmath gd gettext zip pdo_mysql && \
    pecl install ds rrd && \
    printf "\n" | pecl install yaml && \
    echo "extension=ds.so\nextension=rrd.so\nextension=yaml.so\n" >/usr/local/etc/php/conf.d/local-ixpmanager.ini && \
    docker-php-source delete && \
    curl -so /usr/local/bin/composer.phar https://getcomposer.org/download/1.6.2/composer.phar && \
    chmod a+x /usr/local/bin/composer.phar

RUN /usr/sbin/a2enmod rewrite

COPY apache-site.conf              /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/ixpmanager

