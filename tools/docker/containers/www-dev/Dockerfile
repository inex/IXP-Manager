# Development / test container for IXP Manager v7.x
#
# This exists because tools/docker/containers/www is still on php:7.4 from the
# v5 era and cannot run v7 (composer.json requires php ^8.4). It deliberately
# mirrors what .github/workflows/ci-dusk.yml installs so that a local Dusk run
# matches CI.

FROM php:8.4-apache

# ---------------------------------------------------------------------------
# OS packages
#
# chromium + chromium-driver are for the Laravel Dusk browser tests. bgpq3 and
# bgpq4 are needed by the IRRDB tests (see IXP_IRRDB_BGPQ*_PATH in .env.ci) -
# bgpq4 is not in every Debian suite so it is allowed to fail.
# ---------------------------------------------------------------------------
RUN apt-get -y update && \
    apt-get -y install --no-install-recommends \
        git unzip wget joe \
        default-mysql-client \
        libsnmp-dev snmp \
        libicu-dev librrd-dev libyaml-dev libpng-dev libzip-dev libonig-dev \
        iputils-ping dnsutils \
        chromium chromium-driver \
        bgpq3 && \
    ( apt-get -y install --no-install-recommends bgpq4 || echo "bgpq4 unavailable in this suite - IRRDB bgpq4 tests will be skipped" ) && \
    apt-get -y autoremove --purge && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/*

# ---------------------------------------------------------------------------
# PHP extensions
#
# ds     - php-ds/php-ds
# snmp   - opensolutions/oss-snmp
# rrd    - grapher backends
# intl, bcmath, gd, zip, pdo_mysql, pcntl, gettext - framework / app
# ---------------------------------------------------------------------------
RUN docker-php-source extract && \
    docker-php-ext-install -j"$(nproc)" snmp intl bcmath gd gettext zip pdo_mysql pcntl && \
    pecl install ds rrd && \
    printf "\n" | pecl install yaml && \
    docker-php-source delete && \
    printf "extension=ds.so\nextension=rrd.so\nextension=yaml.so\n" > /usr/local/etc/php/conf.d/local-ixpmanager.ini

# ---------------------------------------------------------------------------
# Composer 2 (the v5 container pinned Composer 1.10, which cannot resolve
# Laravel 12)
# ---------------------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# ---------------------------------------------------------------------------
# Node - for `npm run build` (vite)
# ---------------------------------------------------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get -y install --no-install-recommends nodejs && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Dusk shells out to `chromedriver`; Debian installs it as /usr/bin/chromedriver
# and chromium as /usr/bin/chromium. Dusk looks for a `chrome` binary.
RUN ln -sf /usr/bin/chromium /usr/local/bin/chrome

# Chromium cannot use its namespace sandbox inside an unprivileged container.
# Debian's /usr/bin/chromium is a wrapper that sources /etc/chromium.d/*, so we
# add the flags there rather than patching tests/DuskTestCase.php (which is an
# upstream file, and which is correct as-is for CI where Chrome runs unconfined).
RUN printf 'export CHROMIUM_FLAGS="$CHROMIUM_FLAGS --no-sandbox --disable-dev-shm-usage"\n' \
        > /etc/chromium.d/00-container-no-sandbox

RUN /usr/sbin/a2enmod rewrite

COPY php-dev.ini /usr/local/etc/php/conf.d/zz-ixpmanager-dev.ini

WORKDIR /srv/ixpmanager
