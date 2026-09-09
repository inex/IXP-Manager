# Development / test stack (v7.x)

`docker-compose.dev.yml` is a minimal stack for running IXP Manager and its
test suites locally.

## Why this exists

The stack in `docker-compose.yml` alongside it is from the v5 era and **cannot
run v7**:

| Problem | Detail |
|---|---|
| PHP version | `containers/www/Dockerfile` is `FROM php:7.4-apache`; `composer.json` requires `^8.4` |
| Composer | pinned to 1.10.5; Laravel 12 needs Composer 2 |
| Xdebug | `xdebug-2.9.5` is PHP 7 only |
| Missing file | `containers/mysql/Dockerfile` does `COPY docker.sql`, but only `docker.sql.dist` exists |
| Missing directory | compose mounts `./tools/docker/mrtg/`, which is not in the repo |

It also starts twelve services (BIRD route servers, SNMP simulators, MRTG,
Routinator) that application and browser testing do not need.

This file starts two: `www` (PHP 8.4, Composer 2, Node 20, Chromium) and
`mysql` (8.0, matching CI).

## First run

All commands are run **from the repository root**.

```bash
docker compose -f tools/docker/docker-compose.dev.yml up -d --build

# dependencies (the www container waits for vendor/ before serving)
docker compose -f tools/docker/docker-compose.dev.yml exec www composer install

# environment - .env is the single source of truth for DB settings, see NOTE below
docker compose -f tools/docker/docker-compose.dev.yml exec www \
    sh -c 'cp -n .env.ci .env && sed -i "s/^DB_HOST=.*/DB_HOST=mysql/" .env'

docker compose -f tools/docker/docker-compose.dev.yml exec www php artisan migrate
```

The app is then on <http://127.0.0.1:8000> and MySQL on port 33061.

Log in with any user from the seed data, e.g. `travis` / `travisci` (superuser),
`imcustadmin` / `travisci`, `imcustuser` / `travisci`.

> **NOTE — do not put `DB_*` in the compose `environment:` block.**
> `php artisan serve` forwards only a whitelist of environment variables to the
> built-in server it spawns. `DB_HOST` set in compose would apply to `artisan`
> and `phpunit` but *not* to the served application, which then silently falls
> back to `.env` and cannot reach the database.

## Running the tests

The Dusk suite is **destructive** - it writes to the database and does not roll
back. Reseed before each run or you will get cascading, misleading failures:

```bash
# reseed
docker compose -f tools/docker/docker-compose.dev.yml exec -T mysql \
    mysql -uroot -e "DROP DATABASE IF EXISTS ixp_ci; CREATE DATABASE ixp_ci DEFAULT CHARACTER SET utf8mb4;"
docker compose -f tools/docker/docker-compose.dev.yml exec -T mysql \
    sh -c 'mysql --default-character-set=utf8mb4 -uroot ixp_ci' < data/ci/ci_test_db.sql
docker compose -f tools/docker/docker-compose.dev.yml exec www php artisan migrate --force
```

```bash
C="docker compose -f tools/docker/docker-compose.dev.yml exec www"

$C vendor/bin/phpunit --testsuite "IXP Manager Test Suite"
$C vendor/bin/phpunit --testsuite "Docstore Test Suite"
$C vendor/bin/phpunit --testsuite "Dusk / Browser Test Suite"
```

### Use `phpunit.xml`, not `phpunit.dusk.xml`

`phpunit.dusk.xml` is stale: it lacks the `<php><const .../></php>` block that
defines `APPLICATION_VERSION` / `DOCUMENTATION_VERSION`, so every test errors
with `Undefined constant "DOCUMENTATION_VERSION"`. `.github/workflows/ci-dusk.yml`
uses the default `phpunit.xml` with `--testsuite 'Dusk / Browser Test Suite'`,
and so should you.

### Known-failing tests at v7.4.0

These fail on a clean `v7.4.0` checkout with a freshly seeded database. They are
**pre-existing upstream failures**, not caused by local changes - use this as
your baseline:

```
Tests: 35, Assertions: 1822, Errors: 8, Failures: 1

Errors:
  RsFilterControllerTest::testSuperUser / testCustAdmin / testCustUser
  SettingsControllerTest::testSettings
  SwitchControllerTest::testAdd
  UserControllerTest::testAdd
  VirtualInterfaceControllerTest::testDisabledMaxPrefixesPerVlan
  VirtualInterfaceControllerTest::testViRateLimitAndAutoneg
Failure:
  ExampleTest::test_basic_example   (Laravel's stock scaffold test - expects the
                                     text "Laravel" on the home page)
```

## Gotchas worth knowing

**Never start the dev server with `docker compose exec -d`.** The exec client
disconnects immediately, the server's stdout becomes a broken pipe, and PHP
injects

```
Notice: file_put_contents(): Write of N bytes failed with errno=32 Broken pipe
in .../Illuminate/Foundation/resources/server.php on line 21
```

into the body of *every* response. That corrupts the headers, the session cookie
is never set, and every Dusk login fails with a misleading
`Waited 5 seconds for location [/admin/dashboard]`. The compose file therefore
runs the server as the container's main process, with its stdout going to
`docker compose logs www`.

**The container runs as UID/GID 1000** so that files it writes into the bind
mount (`vendor/`, `storage/`, `node_modules/`) belong to you rather than root,
and so Chromium's sandbox is usable. If your host ids differ:

```bash
DOCKER_UID=$(id -u) DOCKER_GID=$(id -g) docker compose -f tools/docker/docker-compose.dev.yml up -d
```

**Chromium** runs with `--no-sandbox --disable-dev-shm-usage`, set via
`/etc/chromium.d/` in the image rather than by patching `tests/DuskTestCase.php`
(an upstream file that is correct as-is for CI).

**ChromeDriver must match Chromium.** The image installs Debian's `chromium` and
`chromium-driver` together, so they always match. `php artisan dusk:chrome-driver`
downloads a version tied to upstream Chrome and will usually *not* match - if you
run it, point Dusk back at the system driver:

```bash
docker compose -f tools/docker/docker-compose.dev.yml exec www \
    cp -f /usr/bin/chromedriver vendor/laravel/dusk/bin/chromedriver-linux
```

## Front end assets

```bash
docker compose -f tools/docker/docker-compose.dev.yml exec www npm install
docker compose -f tools/docker/docker-compose.dev.yml exec www npm run build
```

## Tearing down

```bash
docker compose -f tools/docker/docker-compose.dev.yml down          # keep the database
docker compose -f tools/docker/docker-compose.dev.yml down -v       # and delete it
```
