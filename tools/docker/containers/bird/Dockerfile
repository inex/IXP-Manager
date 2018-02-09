FROM ubuntu:16.04

MAINTAINER Barry O'Donovan <barry.odonovan@inex.ie>

EXPOSE 179

RUN apt-get -y update && \
    apt-get -y upgrade && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/*

RUN apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 52463488670E69A092007C24F2331238F9C59A45 && \
    echo "\ndeb http://ppa.launchpad.net/cz.nic-labs/bird/ubuntu xenial main" >>/etc/apt/sources.list && \
    apt-get -y update && \
    apt-get -y install bird && \
    apt-get -y autoremove --purge && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /tmp/* && \
    mkdir -p /run/bird /var/log/bird && \
    chown bird: /run/bird /var/log/bird

WORKDIR /

ENTRYPOINT [ "/usr/sbin/bird", "-f", "-u", "bird", "-g", "bird" ]
