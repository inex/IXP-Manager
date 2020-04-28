FROM mysql/mysql-server:8.0

MAINTAINER Barry O'Donovan <barry.odonovan@inex.ie>

ENV MYSQL_DATABASE=ixpmanager
ENV MYSQL_USER=ixpmanager
ENV MYSQL_PASSWORD=ixpmanager
ENV MYSQL_ROOT_HOST=%
ENV MYSQL_ALLOW_EMPTY_PASSWORD=true

VOLUME /var/lib/mysql

COPY docker.sql /docker-entrypoint-initdb.d/ixpmanager.sql

# docker inherits entrypoint, cmd and expose from parent.
