FROM ubuntu:16.04

MAINTAINER Barry O'Donovan <barry.odonovan@inex.ie>

RUN apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y autoremove --purge

RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 52463488670E69A092007C24F2331238F9C59A45 && \
    echo "deb http://ppa.launchpad.net/cz.nic-labs/bird/ubuntu xenial main" >>/etc/apt/sources.list && \
    apt-get -y update && \
    apt-get -y install bird && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    mkdir -p /run/bird /var/log/bird && \
    chown bird: /run/bird /var/log/bird

RUN apt-get -y install php-cgi php-mbstring php-xml unzip lighttpd wget bzip2 sudo joe curl \
        iputils-ping dnsutils && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    lighty-enable-mod fastcgi && \
    lighty-enable-mod fastcgi-php

COPY lighttpd.conf              /etc/lighttpd/lighttpd.conf
COPY start-rs.sh                /usr/local/sbin
COPY api-reconfigure-all-v4.sh  /usr/local/sbin
COPY api-reconfigure-v4.sh      /usr/local/sbin

RUN cd /srv && \
    wget https://github.com/inex/birdseye/releases/download/v1.1.0/birdseye-v1.1.0.tar.bz2 && \
    tar jxf birdseye-v1.1.0.tar.bz2  && \
    ln -s birdseye-v1.1.0 birdseye  && \
    cd birdseye-v1.1.0  && \
    chown -R www-data: storage && \
    echo "www-data        ALL=(ALL)       NOPASSWD: /srv/birdseye/bin/birdc\n" >/etc/sudoers.d/birdseye && \
    chmod a+x /usr/local/sbin/start-rs.sh /usr/local/sbin/api-reconfigure-all-v4.sh /usr/local/sbin/api-reconfigure-v4.sh

COPY birdseye-rs1-ipv4.env      /srv/birdseye/birdseye-rs1-ipv4.env
COPY birdseye-rs1-ipv6.env      /srv/birdseye/birdseye-rs1-ipv6.env
COPY skipcache_ips.php          /srv/birdseye/skipcache_ips.php

EXPOSE 80 179
WORKDIR /

ENTRYPOINT [ "/usr/local/sbin/start-rs.sh" ]
