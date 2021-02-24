FROM php:7.4-apache

MAINTAINER Barry O'Donovan <barry.odonovan@inex.ie>

RUN apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y install snmp libsnmp-dev libicu-dev librrd-dev \
        libyaml-dev git default-mysql-client yarn joe wget libpng-dev libzip-dev \
        iputils-ping dnsutils && \
    apt-get -y autoremove --purge && \
    apt-get clean && \
    rm -rf /tmp/*

RUN docker-php-source extract && \
    docker-php-ext-install -j$(nproc) snmp intl bcmath gd gettext zip pdo_mysql pcntl && \
    pecl install ds rrd xdebug-2.9.5 && \
    printf "\n" | pecl install yaml && \
    docker-php-source delete

RUN curl -so /usr/local/bin/composer.phar https://getcomposer.org/download/1.10.5/composer.phar && \
    chmod a+x /usr/local/bin/composer.phar

RUN echo "extension=ds.so\nextension=rrd.so\nextension=yaml.so\n" >/usr/local/etc/php/conf.d/local-ixpmanager.ini && \
    echo "[xdebug]\nzend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)\n" >/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini && \
    echo "xdebug.remote_enable=1\nxdebug.remote_port=9001\nxdebug.remote_autostart=0\nxdebug.idekey=PHPSTORM\n" >>/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini && \
    echo "xdebug.profiler_enable=0\nxdebug.profiler_enable_trigger=1\nxdebug.profiler_output_dir=/srv/ixpmanager/storage/tmp\n" >>/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini && \
    echo "xdebug.auto_trace=0\nxdebug.trace_enable_trigger=1\nxdebug.trace_output_dir=/srv/ixpmanager/storage/tmp\n" >>/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini

RUN /usr/sbin/a2enmod rewrite

COPY apache-site.conf              /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/ixpmanager
