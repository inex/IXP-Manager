FROM ubuntu:18.04

MAINTAINER Barry O'Donovan <barry.odonovan@inex.ie>

RUN apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y autoremove --purge

RUN apt-get -y install build-essential wget flex bison libncurses-dev libreadline-dev libssh-dev git && \
    apt-get -y clean

RUN TZ=Europe/Dublin DEBIAN_FRONTEND=noninteractive apt-get -y install php-cgi php-mbstring php-xml unzip lighttpd wget bzip2 sudo joe curl \
        iputils-ping dnsutils && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    lighty-enable-mod fastcgi && \
    lighty-enable-mod fastcgi-php

RUN cd /usr/local/src && \
    wget ftp://bird.network.cz/pub/bird/bird-2.0.4.tar.gz && \
    tar zxf bird-2.0.4.tar.gz && \
    cd bird-2.0.4 && \
    ./configure && \
    make && \
    make install && \
    mkdir -p /run/bird /var/log/bird


COPY lighttpd.conf              /etc/lighttpd/lighttpd.conf
COPY start-rs.sh                /usr/local/sbin
COPY api-reconfigure-all-v4.sh  /usr/local/sbin
COPY api-reconfigure-v4.sh      /usr/local/sbin

RUN cd /srv && \
    wget https://github.com/inex/birdseye/archive/master.zip && \
    unzip master.zip  && \
    ln -s birdseye-master birdseye  && \
    cd birdseye  && \
    chown -R www-data: storage && \
    echo "www-data        ALL=(ALL)       NOPASSWD: /srv/birdseye/bin/birdc\n" >/etc/sudoers.d/birdseye && \
    chmod a+x /usr/local/sbin/start-rs.sh /usr/local/sbin/api-reconfigure-all-v4.sh /usr/local/sbin/api-reconfigure-v4.sh

COPY birdseye-rs1-ipv4.env      /srv/birdseye/birdseye-rs1-ipv4.env
COPY birdseye-rs1-ipv6.env      /srv/birdseye/birdseye-rs1-ipv6.env
COPY skipcache_ips.php          /srv/birdseye/skipcache_ips.php

EXPOSE 80 179
WORKDIR /

ENTRYPOINT [ "/usr/local/sbin/start-rs.sh" ]
