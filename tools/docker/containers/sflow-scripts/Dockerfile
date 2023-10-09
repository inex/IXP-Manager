FROM ubuntu:23.04

# RUN apt-get -y update && \
#     apt-get -y upgrade && \
#     apt-get -y autoremove --purge && \
#     apt-get -y clean && \
#     rm -rf /var/lib/apt/lists/* && \
#     rm -rf /tmp/*

# JvB added for sflow testing
RUN apt-get -y update && apt-get install -y perl-base bash make gcc cpanminus libmysqlclient-dev

RUN cpanm install NetAddr::IP Config::General NetPacket::TCP 

# Tests fail, force install
RUN cpanm install DBD::mysql --force

COPY ixpmanager.conf /usr/local/etc/ixpmanager.conf

# sflowtool for traditional workflow
RUN apt-get install -y git autoconf
RUN git clone https://github.com/sflow/sflowtool.git && \
    cd sflowtool && \
    ./boot.sh && \
    ./configure && \
    make && \
    make install

# goflow2 script dependencies
RUN cpanm install JSON
RUN apt-get install -y wget
RUN wget https://github.com/netsampler/goflow2/releases/download/v1.3.6/goflow2_1.3.6_amd64.deb
RUN apt install -y goflow2_1.3.6_amd64.deb

WORKDIR /

ENTRYPOINT "/bin/bash"
CMD "/bin/bash"
