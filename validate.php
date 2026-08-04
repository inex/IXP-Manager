#!/usr/bin/env php
<?php

declare(strict_types=1);


/**
 * This function prints a pre-populated GitHub Issue Template.
 */
function printIssueAssistanceInfo(): void {
    // OS Detection
    switch (PHP_OS_FAMILY) {
        case 'BSD':
            $osInfo = file_get_contents('/etc/os-release');
            if ($osInfo === false) {
                exec("uname -mrs", $uname, $exitCode);
                if ($exitCode === 0) {
                    $osInfo = implode("\n", $uname);
                }
                unset($uname, $exitCode);
            }
            break;
        case 'Linux':
            $osInfo = file_get_contents('/etc/os-release');
            if ($osInfo === false) {
                $osInfo = file_get_contents('/etc/lsb-release');
            }
            break;
        case 'Darwin':
            if ($pfile = file_get_contents('/System/Library/CoreServices/SystemVersion.plist')) {
                // The regex pattern
                $pattern = '/<key>ProductUserVisibleVersion<\/key>\s*<string>([^<]+)<\/string>/';
                if (preg_match($pattern, $pfile, $matches)) {
                    // $matches[1] contains the value inside the capture group
                    $osInfo = "macOS " . $matches[1];
                }
            }
            break;
        default:
            $osInfo = false;
            break;
    }

    // IXP Manager version
    $versionInfo = false;
    if ($versionFile = file_get_contents(__DIR__ . "/version.php")) {
        // Regex targeting both constants with named capture groups
        $pattern = "/define\s*\(\s*['\"]APPLICATION_VERSION['\"]\s*,\s*['\"](?<version>[^'\"]+)['\"]\s*\).*?define\s*\(\s*['\"]APPLICATION_VERDATE['\"]\s*,\s*['\"](?<verdate>[^'\"]+)['\"]\s*\)/s";

        if (preg_match($pattern, $versionFile, $matches)) {
            $version = $matches['version'];
            $verdate = $matches['verdate'];

            $versionInfo =
                "APPLICATION_VERSION: " . $version . "\n" .
                "APPLICATION_VERDATE: " . $verdate . "\n";
        }
    }

    // PHP Environment
    $environmentInfoPhp = false;
    exec("php -v", $output, $exitCode);
    if ($exitCode === 0) {
        $environmentInfoPhp = implode("\n", $output) . "\n\n";
    }

    $exitCode = null;
    $output = null;
    if (PHP_OS_FAMILY === "BSD") {
        exec("pkg list | grep php", $output, $exitCode);
    } if (PHP_OS_FAMILY === "Linux") {
        if (!empty(shell_exec("which dpkg"))) {
            exec("dpkg -l | grep php", $output, $exitCode);
        } elseif (!empty(shell_exec("which yum"))) {
            exec("yum list installed | grep php", $output, $exitCode);
        } elseif (!empty(shell_exec("which dnf"))) {
            exec("dnf list installed | grep php", $output, $exitCode);
        }
    }

    if ($exitCode === 0) {
        $environmentInfoPackages = implode("\n", $output);
    } else {
        $environmentInfoPackages = "COULDN'T CALL PACKAGE MANAGER - please provide the list of PHP packages installed on your system.";
    }
    unset($output, $exitCode);

    // Configuration: Non-critical environment variables.
    $envInfo = false;
    if (PHP_OS_FAMILY === "Linux" || PHP_OS_FAMILY === "BSD" || PHP_OS_FAMILY === "Darwin") {
        exec("grep -Ev '(^#|^\s*$|^DB_|^APP_KEY|^HELPDESK|^IDENTITY|^MAIL_|^IXP_API_RIR_PASSWORD|^IXP_API_PEERING_DB_)' .env", $output, $exitCode);
        if ($exitCode === 0) {
            $envInfo = implode("\n", $output);
        }
        unset($output, $exitCode);
    }

    echo "
##### ISSUE TYPE

Bug Report

##### OS
<!---
Mention the OS you are running IXP Manager on (including Linux variant if relevant)
-->

" . $osInfo . "

##### VERSION

<!--- Paste verbatim the output between quotes below. NB: run this command
from IXP Manager's root directory (e.g. /srv/ixpmanager)

cat version.php | grep APPLICATION
-->

```
$versionInfo
```

##### ENVIRONMENT

<!--- Paste verbatim the output from the following commands between quotes below
php -v
dpkg -l | grep php   (or equivalent for your OS - list of php packages installed)
-->

```
" .
            $environmentInfoPhp.
            $environmentInfoPackages.
"

```

<!--- You can also use gist.github.com links for larger files -->

##### CONFIGURATION

<!--- Paste the output of the following between quotes below:
(run from IXP Manager's root directory (e.g. /srv/ixpmanager)
NB: sanity check the output to make sure you are happy you are not leaking any security information!

cat .env | egrep -v '(^#|^\s*$|^DB_|^APP_KEY|^HELPDESK|^IDENTITY|^MAIL_|^IXP_API_RIR_PASSWORD|^IXP_API_PEERING_DB_)'
-->

```
" . $envInfo . "
```

<!--- You can also use gist.github.com links for larger files -->
";
}

/**
 * Discourage running this script as root.
 */
function requireConfirmationIfRunningRoot(): void
{
    exec( 'id -u', $idOutput, $idExitCode );
    if ( $idExitCode !== 0 ) {
        echo "Warning: unable to determine if running as root.";
    } else {
        $id = (int) trim( $idOutput[0] );
        if ( $id === 0 ) {
            echo "WARNING: you are running this script as root, this is not recommended. Are you sure you want to continue? (y/N)?: ";
            $stdin = fopen( "php://stdin", "r" );
            $line = fgets( $stdin );
            if ( trim( $line ) === 'y' ) {
                return;
            } else {
                echo "Terminating script.\n";
                exit(0);
            }
        }
    }
}

/**
 * Parses a .env file at $path and returns an array of vars if successful. False on failure
 * ($errorMessage will be written in this case)
 */
function loadEnv(string $path, &$errorMessage): array|false
{
    $env = [];

    if ( !file_exists( $path ) ) {
        $errorMessage = "env file not found: {$path}";
        return false;
    }

    if ( ($lines = file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) ) === false ) {
        $errorMessage = "failed to read env file: $path";
        return false;
    }

    foreach ( $lines as $line ) {
        // ignore whole line comment
        if ( str_starts_with( trim( $line ), '#' ) ) {
            continue;
        }

        // look for a setting
        if ( str_contains( $line, '=' ) ) {
            // split into name & value
            [$name, $value] = explode( '=', $line, 2 );

            $name  = trim( $name  );
            $value = trim( $value );

            // handle inline comments:
            if ( str_contains( $value, '#' ) ) {
                $value = trim( substr( $value, 0, strpos( $value, '#' ) ) );
            }

            // extract from optional quotes
            $value = preg_replace( '/^["\'](.*)["\']$/', '$1', $value );

            $env[$name] = $value;
        }
    }

    return $env;
}

/**
 * Implement truthiness checks for an env value having been parsed and cast to a boolean
 * true, (true), false, (false), null, (null), empty, (empty) are reserved and interpreted
 * as true, false, null, and '' respectively.
 */
function parseBooleanEnvVar( string $value): bool
{
    $lower = strtolower( $value );
    return (bool) match ($lower) {
        'true',  '(true)'  => true,
        'false', '(false)' => false,
        'null',  '(null)'  => null,
        'empty', '(empty)' => "",
        default            => $value
    };
}

function doMinimumPhpVersionCheck( string $minVersion, string $recommendedPrefix, ?string $maxVersion): array
{
    $results = [];

    $version = phpversion();
    $results[] = new SoftwareVersion('PHP', $version);
    if ( version_compare( $version, $minVersion, '<' ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "PHP version " . $minVersion . " or higher required - running " . $version ] );
    } else if ($maxVersion !== null && version_compare( $version, $maxVersion, '>')) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "PHP version exceeds max supported version " . $maxVersion ] );
    } else if ( !str_starts_with( $version, $recommendedPrefix ) ) {
        $results[] = new CheckResult( ResultStatus::WARNING, [ "Not running a recommended PHP version - running " . $version ] );
    }

//    if (ini_get("allow_url_fopen") == 1) {
//        $results[] = new CheckResult( ResultStatus::OK, [ "allow_url_fopen is enabled" ] );
//    } else {
//        $results[] = new CheckResult( ResultStatus::WARNING, [ "allow_url_fopen is disabled, this may impact some features" ] );
//    }

    if ( !extension_loaded('pdo_mysql') ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ 'PDO MySQL extension is not installed' ] );
    }

    return $results;
}

function doComposerCheck(): array
{
    $results = [];

    if ( !file_exists( dirname(__FILE__) . "/vendor/autoload.php" ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, ['composer install has not been run'] );
    }

    return $results;
}

function doEnvFileChecks(): array
{
    $results = [];
    $envfile = dirname(__FILE__) . "/.env";
    if ( !file_exists($envfile)) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ '.env file does not exist' ] );
        return $results;
    }

    if ( ( $parseEnv = loadEnv($envfile, $errorMessage ) ) === false ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ $errorMessage ] );
        return $results;
    }

    if ( !array_key_exists('APP_KEY', $parseEnv) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ 'APP_KEY is not set in .env' ] );
    }

    return $results;
}

function doMySqlCheck(string $minVersion, string $recommendedPrefix, ?string $maxVersion): array|CheckResult
{
    $results = [];
    // Laravel uses pdo-mysql extension to interact with MySQL databases
    if ( !extension_loaded( 'pdo_mysql' ) ) {
        return new CheckResult( ResultStatus::ERROR, [ 'PDO MySQL extension is not installed' ] );
    }

    // Load env file to perform configuration checks
    if ( ( $env = loadEnv( dirname(__FILE__) . "/.env", $errorMessage ) ) === false ) {
        return new CheckResult( ResultStatus::ERROR, [ $errorMessage ] );
    }

    // There are several required keys in the env file.
    $allKeysPresent = true;
    foreach ( [ 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME' ] as $key ) {
        if ( !array_key_exists( $key, $env ) ) {
            $results[] = new CheckResult( ResultStatus::ERROR, [ ".env is missing required environment variable: {$key}" ] );
            $allKeysPresent = false;
        } else if ( empty( $env[$key] ) ) {
            $results[] = new CheckResult( ResultStatus::ERROR, [ ".env is missing required environment variable: {$key} (empty value)" ] );
            $allKeysPresent = false;
        }
    }
    if ( !$allKeysPresent ) {
        return $results;
    }

    // If DB_CONNECTION is set, it must be mysql.
    // Note: if we implement the corresponding config check here (catch defaults) then we protect against accidental sqlite3 default in later versions
    if ( array_key_exists( 'DB_CONNECTION', $env ) && $env['DB_CONNECTION'] !== 'mysql' ) {
        return new CheckResult( ResultStatus::ERROR, [ "DB_CONNECTION must be set to 'mysql'" ] );
    }

    // Generate the DSN from our configuration
    $dsn = "mysql:" . implode( ";", [ "host={$env['DB_HOST']}", "dbname={$env['DB_DATABASE']}" ] + (array_key_exists( 'port', $env ) ? [ "port={$env['port']}" ] : [] ) );

    // Attempt to connect using credentials from Env File
    try {
        $pdo = new \PDO( $dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
        ] );
    } catch (\PDOException $e) {
        return new CheckResult( ResultStatus::ERROR, [ "Connection failed: " . $e->getMessage() ] );
    }

    // Determine MySQL server version
    try {
        $version = $pdo->query( "SELECT VERSION() as version" )->fetchColumn();
    } catch (\PDOException $e) {
        return new CheckResult( ResultStatus::ERROR, [ "Failed to determine server version: " . $e->getMessage() ] );
    }

    // Min/Max/Recommended MySQL server checks:

    if ( version_compare( $version, $minVersion, '<' ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "MySQL version $minVersion or higher required" ] );
    } else if ($maxVersion !== null && version_compare( $version, $maxVersion, '>')) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "MySQL version exceeds max supported version " . $maxVersion ] );
    } else if ( !str_starts_with( $version, $recommendedPrefix ) ) {
        $results[] = new CheckResult( ResultStatus::WARNING, [ "Not running a recommended MySQL version." ] );
    }

    // What schema/migration are we running?
    try {
        $schemaVersion = $pdo->query( "SELECT migration FROM migrations ORDER BY id DESC LIMIT 1" )->fetchColumn();
        $results[] = new SoftwareVersion( "DB Schema", $schemaVersion );
    } catch (\PDOException $e) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "failed to determine schema version: " . $e->getMessage() ] );
    }

    return $results;
}

function doLaravelRequiredExtensionChecks(array $requiredByLaravel): array
{
    $results = [];

    // Check that required extensions are installed
    $missingExtensions = [];
    foreach ( $requiredByLaravel as $extension ) {
        if ( !extension_loaded( $extension ) ) {
            $missingExtensions[] = $extension;
        }
    }

    if ( count( $missingExtensions ) > 0 ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ 'Missing required PHP extensions: ' . implode(', ', $missingExtensions) ] );
    }

    return $results;
}

/**
 * Checks local version against latest version on Github
 * See https://docs.github.com/en/rest/repos/repos?apiVersion=2026-03-10#list-repository-tags for this api call
 * A user agent is required for this call
 * @param string $localVersion
 * @return array
 */
function doIxpManagerReleaseCheck(string $localVersion): array
{
    $results = [];

    // Include the current IXP Manager version
    $results[] = new SoftwareVersion( "IXP-Manager", APPLICATION_VERSION );

    // Lookup tags. Use file_get_contents with curl fallback
    if ( ini_get( 'allow_url_fopen' ) == 1 ) {
        // Create stream context containing required HTTP headers
        $context = stream_context_create( [
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: IXP-Manager-validation-tool\r\n",
            ]
        ] );

        if ( ( $tagsJson = file_get_contents( 'https://api.github.com/repos/inex/IXP-Manager/tags', false, $context ) ) === false ) {
            $results[] = new CheckResult( ResultStatus::ERROR, [ "Failed to fetch IXP-Manager release information (file_get_contents)" ] );
            return $results;
        }

        // Extract HTTP response code
        $regexReturnCode = preg_match( '/([0-9])\d+/', $http_response_header[0],$matches );
        if ( $regexReturnCode === false ) {
            $results[] = new CheckResult(ResultStatus::ERROR, [ "Invalid regex for HTTP response code" ] );
            return $results;
        } else if ( $regexReturnCode === 0 ) {
            $results[] = new CheckResult(ResultStatus::ERROR, [ "Did not find a status in HTTP response" ] );
            return $results;
        }

        // Ensure HTTP response was OK
        $responsecode = intval( $matches[0] );
        if ( $responsecode !== 200 ) {
            $results[] = new CheckResult( ResultStatus::ERROR, [ "Received non-OK response code when fetching IXP-Manager release information (file_get_contents): $responsecode" ] );
            return $results;
        }

        // we have $tagsJson and the response was OK
    } else if ( extension_loaded( 'curl' ) ) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL,            'https://api.github.com/repos/inex/IXP-Manager/tags' );
        curl_setopt( $ch, CURLOPT_USERAGENT,      'IXP-Manager-validation-tool' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        if ( ( $tagsJson = curl_exec($ch) ) === false ) {
            $results[] = new CheckResult(ResultStatus::ERROR, [ "Failed to fetch IXP-Manager release information (curl): " . curl_error($ch) ]);
            return $results;
        }

        // Ensure HTTP response was OK
        $info = curl_getinfo( $ch );
        if ( $info['http_code'] !== 200 ) {
            $results[] = new CheckResult( ResultStatus::ERROR, [ "Received non-OK response code when fetching IXP-Manager release information (curl): " . $info['http_code'] ] );
            return $results;
        }

        // we have $tagsJson and the response was OK
    } else {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "there was no usable method to fetch IXP-Manager release information" ] );
        return $results;
    }

    // Extract IXP Manager published versions and compare against the installed version
    $tags = json_decode( $tagsJson );
    if ( version_compare( $localVersion, ltrim( $tags[0]->name, "v" ), '<' ) ) {
        $results[] = new CheckResult( ResultStatus::WARNING, [ "A newer version of IXP-Manager is available: " . $tags[0]->name ] );
    }

    return $results;
}

class BasicValidation
{
    /** @var CheckResult[]|SoftwareVersion[] */
    private(set) array $results = [];

    public function __construct(public readonly string $name, private \Closure $callable, private ?array $params = null) {}

    public function run(): void
    {
        $results = call_user_func_array($this->callable, $this->params ?? []);

        // can return a single result, or an array of results
        if ($results instanceof CheckResult) {
            $results = [$results];
        }
        $this->results = $results;
    }

    public function hasErrors(): bool
    {
        return array_any( $this->results, fn( $result ) => $result instanceof CheckResult && $result->status === ResultStatus::ERROR );
    }
}

enum ResultStatus
{
    case WARNING;
    case ERROR;
}


readonly class CheckResult
{
    public function __construct( public ResultStatus $status, public array $messages = []) {}
}

readonly class SoftwareVersion
{
    public function __construct( public string $software, public string $version) {}
}


requireConfirmationIfRunningRoot();

include "version.php";
$manifest = APPLICATION_MANIFEST;

if (array_any($argv, fn($v) => $v === '--github-issue')) {
    printIssueAssistanceInfo();
    return;
}

$tasks = [];
$tasks[] = new BasicValidation( 'PHP', doMinimumPhpVersionCheck(...), [ $manifest['php_version']['min'], $manifest['php_version']['recommended'], $manifest['php_version']['max'] ] );
$tasks[] = new BasicValidation( 'Composer', doComposerCheck(...), [] );
$tasks[] = new BasicValidation( 'Env File', doEnvFileChecks(...), [] );
$tasks[] = new BasicValidation( 'MySQL', doMySqlCheck(...), [ $manifest['mysql_version']['min'], $manifest['mysql_version']['recommended'], $manifest['mysql_version']['max'] ] );
$tasks[] = new BasicValidation( 'Laravel Required Extensions', doLaravelRequiredExtensionChecks(...), [ $manifest['laravel_required_extensions'] ] );
$tasks[] = new BasicValidation( 'IXP Manager', doIxpManagerReleaseCheck(...), [ APPLICATION_VERSION ] );

$softwareVersionResults = [];
$checkResults = [];

$haveErrors = false;
foreach ( $tasks as $task ) {
    $task->run();
    foreach ( $task->results as $taskResult ) {
        if ( $taskResult instanceof SoftwareVersion ) {
            $softwareVersionResults[$taskResult->software] = $taskResult->version;
        } else {
            $checkResults[$task->name][] = $taskResult;
        }
    }
    $haveErrors = $haveErrors || $task->hasErrors();
}

echo "Software Versions:\n";
foreach ( $softwareVersionResults as $software => $result ) {
    echo "$software: " . $result . "\n";
}

echo "\n";
echo "warnings:\n";
foreach ( $checkResults as $taskName => $taskResults ) {
    if (count($taskResults) === 0) {
        continue;
    }
    echo "task: " . $taskName . "\n";
    foreach ( $taskResults as $result ) {
        echo " * " . $result->status->name . ": " . implode("\n * ", $result->messages) . "\n";
    }
    echo "\n";
}

if ( $haveErrors ) {
    echo "There were errors during the validation process. Please review the checks above for details.\n";
    exit(1);
}


echo "No errors detected during basic validations\n";

echo shell_exec(__DIR__ . "/artisan validator:run --simple-output");

