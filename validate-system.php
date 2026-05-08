#!/usr/bin/env php
<?php

declare(strict_types=1);

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
    $results[] = new SoftwareVersionCheckResult( "PHP", $version, ResultStatus::OK, [ "PHP version: $version" ] );

    if ( version_compare( phpversion(), $minVersion, '<' ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "PHP version $minVersion or higher required" ] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ "Minimum PHP version requirement met" ] );
    }

    if ( !str_starts_with( phpversion(), $recommendedPrefix ) ) {
        $results[] = new CheckResult( ResultStatus::WARNING, [ "Not running a recommended PHP version." ] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ "Running recommended PHP version" ] );
    }

    if ($maxVersion !== null && version_compare(phpversion(), $maxVersion, '>')) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "PHP version exceeds max supported version " . $maxVersion ] );
    }

    if (ini_get("allow_url_fopen") == 1) {
        $results[] = new CheckResult( ResultStatus::OK, [ "allow_url_fopen is enabled" ] );
    } else {
        $results[] = new CheckResult( ResultStatus::WARNING, [ "allow_url_fopen is disabled, this may impact some features" ] );
    }

    if ( !extension_loaded('pdo_mysql') ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ 'PDO MySQL extension is not installed' ] );
    } else {
        $version = phpversion( 'pdo_mysql' ) ?? 'unknown';
        $results[] =  new SoftwareVersionCheckResult( "pdo_mysql", $version, ResultStatus::OK, [ 'PDO MySQL extension version: ' . $version ] );
    }

    return $results;
}

function doComposerCheck(): array
{
    if ( !file_exists( "vendor/autoload.php" ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, ['composer install has not been run'] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ 'composer has been run' ] );
    }

    return $results;
}

function doEnvFileChecks(): array
{
    $results = [];
    if ( !file_exists(".env") ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ '.env file does not exist' ] );
        return $results;
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ '.env file exists' ] );
    }

    if ( ( $parseEnv = loadEnv(".env", $errorMessage ) ) === false ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ $errorMessage ] );
        return $results;
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ '.env file passed basic checks' ] );
    }

    if ( !array_key_exists('APP_KEY', $parseEnv) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ 'APP_KEY is not set in .env' ] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ 'APP_KEY is set in .env' ] );
    }

    if ( array_key_exists( 'APP_DEBUG', $parseEnv ) && parseBooleanEnvVar( $parseEnv['APP_DEBUG'] ) === true ) {
        $results[] = new CheckResult( ResultStatus::WARNING, [ 'APP_DEBUG is set to true' ] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ 'APP_DEBUG is not set to true' ] );
    }

    if ( array_key_exists( 'APP_ENV', $parseEnv ) && $parseEnv['APP_ENV'] !== 'production' ) {
        $results[] = new CheckResult( ResultStatus::WARNING, [ 'APP_ENV is not set to production' ] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ 'APP_ENV is set to production' ] );
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
    if ( ( $env = loadEnv( '.env', $errorMessage ) ) === false ) {
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
        $results[] = new CheckResult( ResultStatus::OK, [ "Connected to MySQL" ] );
    } catch (\PDOException $e) {
        return new CheckResult( ResultStatus::ERROR, [ "Connection failed: " . $e->getMessage() ] );
    }

    // Determine MySQL server version
    try {
        $version = $pdo->query( "SELECT VERSION() as version" )->fetchColumn();
        $results[] = new SoftwareVersionCheckResult( "MySQL", $version, ResultStatus::OK, [ "MySQL version: " . $version ] );
    } catch (\PDOException $e) {
        return new CheckResult( ResultStatus::ERROR, [ "Failed to determine server version: " . $e->getMessage() ] );
    }

    // Min/Max/Recommended MySQL server checks:
    if ( version_compare( $version, $minVersion, '<' ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "MySQL version $minVersion or higher is required" ] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ "Minimum version requirement met" ] );
    }

    if ( !str_starts_with( $version, $recommendedPrefix ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "Not running a recommended version" ] );
    } else {
        $results[] = new CheckResult( ResultStatus::OK, [ "Running a recommended version." ] );
    }

    if ( $maxVersion !== null && version_compare( $version, $maxVersion, '>' ) ) {
        $results[] = new CheckResult( ResultStatus::ERROR, [ "Exceeds max supported version " . $maxVersion ] );
    }

    // What schema/migration are we running?
    try {
        $schemaVersion = $pdo->query( "SELECT migration FROM migrations ORDER BY id DESC LIMIT 1" )->fetchColumn();
        $results[] = new SoftwareVersionCheckResult( "DB Schema", $schemaVersion, ResultStatus::INFO, [ "Running schema: " . $schemaVersion ] );
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
    $presentExtensions = [];
    foreach ( $requiredByLaravel as $extension ) {
        if ( !extension_loaded( $extension ) ) {
            $missingExtensions[] = $extension;
        } else {
            $presentExtensions[] = $extension;
        }
    }

    if ( count( $missingExtensions ) > 0 ) {
        $results[] = new CheckResult( ResultStatus::WARNING, [ 'Missing required PHP extensions: ' . implode(', ', $missingExtensions) ] );
    }
    if ( count( $presentExtensions ) > 0 ) {
        $results[] = new CheckResult( ResultStatus::OK, [ "Required extensions found: " . implode(', ', $presentExtensions) ] );
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
    $results[] = new SoftwareVersionCheckResult( "IXP-Manager", APPLICATION_VERSION, ResultStatus::INFO, [ "IXP Manager " . APPLICATION_VERSION . " is installed" ] );

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
    } else if ( version_compare( $localVersion, ltrim( $tags[0]->name, "v" ), '=' ) ) {
        $results[] = new CheckResult(ResultStatus::OK, [ "Running latest version of IXP-Manager" ] );
    }

    return $results;
}

class BasicValidation
{
    /** @var CheckResultInterface[] */
    public private(set) array $results = [];

    public function __construct(public readonly string $name, private \Closure $callable, private ?array $params = null) {}

    public function run(): void
    {
        $results = call_user_func_array($this->callable, $this->params ?? []);

        // can return a single result, or an array of results
        if ($results instanceof CheckResultInterface) {
            $results = [$results];
        }
        $this->results = $results;
    }

    public function hasErrors(): bool
    {
        return array_any( $this->results, fn( $result ) => $result->status === ResultStatus::ERROR );
    }
}

enum ResultStatus
{
    case OK;
    case INFO;
    case WARNING;
    case ERROR;
}

interface CheckResultInterface
{
    public ResultStatus $status { get; }
    public array $messages      { get; }
}

interface SoftwareVersionCheckResultInterface
{
    public string $software { get; }
    public string $version  { get; }
}

readonly class CheckResult implements CheckResultInterface
{
    public function __construct( public ResultStatus $status, public array $messages = []) {}
}

readonly class SoftwareVersionCheckResult implements CheckResultInterface, SoftwareVersionCheckResultInterface
{
    public function __construct( public string $software, public string $version, public ResultStatus $status, public array $messages = []) {}
}


requireConfirmationIfRunningRoot();

$manifest = include "version.php";

$tasks = [];
$tasks[] = new BasicValidation( 'PHP', doMinimumPhpVersionCheck(...), [ $manifest['php_version']['min'], $manifest['php_version']['recommended'], $manifest['php_version']['max'] ] );
$tasks[] = new BasicValidation( 'Composer', doComposerCheck(...), [] );
$tasks[] = new BasicValidation( 'Env File', doEnvFileChecks(...), [] );
$tasks[] = new BasicValidation( 'MySQL', doMySqlCheck(...), [ $manifest['mysql_version']['min'], $manifest['mysql_version']['recommended'], $manifest['mysql_version']['max'] ] );
$tasks[] = new BasicValidation( 'Laravel Required Extensions', doLaravelRequiredExtensionChecks(...), [ $manifest['laravel_required_extensions'] ] );
$tasks[] = new BasicValidation( 'IXP Manager', doIxpManagerReleaseCheck(...), [ APPLICATION_VERSION ] );

$softwareVersionResults = [];

$haveErrors = false;
foreach ( $tasks as $task ) {
    $task->run();
    foreach ( $task->results as $taskResult ) {
        if ( $taskResult instanceof SoftwareVersionCheckResultInterface ) {
            $softwareVersionResults[$taskResult->software] = $taskResult->version;
        }
    }
    $haveErrors = $haveErrors || $task->hasErrors();
}

echo "Software Versions:\n";
foreach ( $softwareVersionResults as $software => $result ) {
    echo "$software: " . $result . "\n";
}

echo "\n";
echo "Checks:\n";
foreach ( $tasks as $task ) {
    echo "task: " . $task->name . "\n";
    foreach ( $task->results as $result ) {
        echo " * " . $result->status->name . ": " . implode("\n * ", $result->messages) . "\n";
    }
    echo "\n";
}

if ( $haveErrors ) {
    echo "There were errors during the validation process. Please review the checks above for details.\n";
    exit(1);
} else {
    echo "No errors detected during basic validations\n";
}

