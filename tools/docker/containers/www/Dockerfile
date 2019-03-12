FROM php:7.0-apache

MAINTAINER Barry O'Donovan <barry.odonovan@inex.ie>

RUN apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y install libmcrypt-dev snmp libsnmp-dev libicu-dev librrd-dev libpng-dev \
        libyaml-dev git mysql-client joe wget \
        iputils-ping dnsutils && \
    apt-get -y autoremove --purge && \
    rm -rf /tmp/*

RUN docker-php-source extract && \
    docker-php-ext-install -j$(nproc) mcrypt snmp intl bcmath gd gettext zip pdo_mysql && \
    pecl install ds rrd xdebug && \
    printf "\n" | pecl install yaml && \
    docker-php-source delete

RUN curl -so /usr/local/bin/composer.phar https://getcomposer.org/download/1.6.2/composer.phar && \
    chmod a+x /usr/local/bin/composer.phar

RUN apt-get -y install gnupg && \
    curl -sL https://deb.nodesource.com/setup_11.x | bash - && \
    apt-get -y install nodejs

RUN npm install -g bower

RUN echo "extension=ds.so\nextension=rrd.so\nextension=yaml.so\n" >/usr/local/etc/php/conf.d/local-ixpmanager.ini && \
    echo "[xdebug]\nzend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)\n" >/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini && \
    echo "xdebug.remote_enable=1\nxdebug.remote_port=9001\nxdebug.remote_autostart=0\nxdebug.idekey=PHPSTORM\n" >>/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini && \
    echo "xdebug.profiler_enable=0\nxdebug.profiler_enable_trigger=1\nxdebug.profiler_output_dir=/srv/ixpmanager/storage/tmp\n" >>/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini && \
    echo "xdebug.auto_trace=0\nxdebug.trace_enable_trigger=1\nxdebug.trace_output_dir=/srv/ixpmanager/storage/tmp\n" >>/usr/local/etc/php/conf.d/local-ixpmanager-xdebug.ini

RUN /usr/sbin/a2enmod rewrite

COPY apache-site.conf              /etc/apache2/sites-available/000-default.conf

WORKDIR /srv/ixpmanager
