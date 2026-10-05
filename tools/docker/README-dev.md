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

Since v7.4.1 the suites seed themselves: `tests/TestCase.php` and
`tests/DuskTestCase.php` use `RefreshDatabase` with `CiTestDataSeeder`, which
loads `data/ci/ci_test_db_data.sql`. You no longer need to reseed by hand
between runs, and tests no longer inherit each other's writes.

The database does need to exist and be migrated once:

```bash
docker compose -f tools/docker/docker-compose.dev.yml exec www php artisan migrate --force
```

Note this makes the suites considerably slower than they were in v7.4.0, since
each test refreshes the database.

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

### Known-failing tests at v7.4.1

The Dusk suite is substantially broken in this environment on a clean upstream
checkout - not by anything local. Measured on plain `v7.4.1`:

```
Tests: 37, Assertions: ~1240-1340, Errors: 20-21, Failures: 1, Risky: 8
```

The error count varies by one between runs of identical code, so compare
**failing test names**, not counts. The set is stable:

```
ApiKeyControllerTest::test
RsFilterControllerTest::testSuperUser / testCustAdmin / testCustUser
SettingsControllerTest::testSettings
SwitchControllerTest::testAdd
SwitchPortControllerTest::testSwitchPort
SwitchUserControllerTest::testLoginAs / testLoginAs2FA
User2FAControllerTest::test / testWithRememberMe
UserControllerTest::testAdd / testAddCustAdmin / testSuperAdminPrivs
UserRememberTokenControllerTest::testAdd
ValidationControllerTest::testRunValidations
VendorControllerTest::testVendor
VirtualInterfaceControllerTest::testAddWizard
    / testDisabledMaxPrefixesPerVlan / testViRateLimitAndAutoneg
VlanControllerTest::testAdd
Failure: ExampleTest::test_basic_example   (Laravel's stock scaffold test)
```

**Always re-measure the baseline back to back with the branch**, on the same
day and the same container. Four separate comparisons during the v7.4.1
integration produced confident-looking but meaningless numbers, each for a
different reason:

- a `git checkout` that **aborted** on uncommitted changes, so the "baseline"
  was actually the branch;
- `.env` reverting to `DB_HOST=127.0.0.1`, so an entire run executed with no
  database and failed 37/37 on `Connection refused` - `.env` is gitignored, so
  nothing restores it;
- a `grep` anchored to `^Tests:` that never matched, because PHPUnit wraps the
  summary in ANSI colour codes - use `--colors=never`;
- `composer update` reinstalling `laravel/dusk` and deleting the chromedriver.

A comparison script should assert its own preconditions - which ref is checked
out, that the server answers, that the database has rows - and print them, so
the output carries its own proof. Silence is not success.

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

**ChromeDriver must match Chromium, and gets wiped easily.** The image installs
Debian's `chromium` and `chromium-driver` together, so they always match. Two
things remove the binary Dusk actually uses:

- `php artisan dusk:chrome-driver`, which downloads a version tied to upstream
  Chrome and will usually *not* match;
- **any `composer update`/`require` that reinstalls `laravel/dusk`**, which
  replaces `vendor/laravel/dusk/bin/` wholesale.

The symptom of the second is every Dusk class erroring at once with
`Invalid path to Chromedriver`. In either case, point Dusk back at the system
driver:

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
