<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace IXP\Utils\Validation\Validators;

use Composer\InstalledVersions;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Utils\Validation\Dto\Result;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class Basic implements Validator
{
    #[\Override]
    public function getName(): string
    {
        return "Basic validations";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Perform some basic system checks";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 0;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        $this->doPhpChecks( $backend );
        $this->doComposerChecks( $backend );
        $this->doConfigChecks( $backend );
        $this->doMySqlChecks( $backend );
        $this->doLaravelChecks( $backend );
    }

    private function doPhpChecks(ValidationBackend $backend): void
    {
        $phpVersion = phpversion();
        $manifest = APPLICATION_MANIFEST;

        $backend->software('PHP', $phpVersion);

        if ( version_compare( $phpVersion, $manifest['php_version']['min'], '>=' ) && ($manifest['php_version']['max'] === null || version_compare( $phpVersion, $manifest['php_version']['max'], '<=')) ) {
            $backend->info( "Running a supported PHP version (" . $phpVersion . "). " .
                (str_starts_with( $phpVersion,  $manifest['php_version']['recommended'])
                    ? "Version is a recommended version" : "") );
        } else {
            if ( version_compare( $phpVersion, $manifest['php_version']['min'], '<' ) ) {
                $backend->error( "PHP version " . $manifest['php_version']['min'] . " or higher required (" . $phpVersion . " found)" );
            }
            if ($manifest['php_version']['max'] !== null && version_compare( $phpVersion, $manifest['php_version']['max'], '>' ) ) {
                $backend->error("PHP version exceeds max supported version " . $manifest['php_version']['max'] . " (" . $phpVersion . " found)" );
            }
        }
        if ( !str_starts_with( $phpVersion, $manifest['php_version']['recommended'] ) ) {
            $backend->warning("Not running a recommended PHP version.");
        }

        if (ini_get("allow_url_fopen") == 1) {
            $backend->info("allow_url_fopen is enabled");
        } else {
            $backend->warning("allow_url_fopen is disabled, this may impact some features");
        }

        if ( !extension_loaded('pdo_mysql') ) {
            $backend->error('PDO MySQL extension is not installed');
        } else {
            $backend->software('PDO MySQL', phpversion( 'pdo_mysql' ) ?? 'unknown');
        }
    }

    private function doComposerChecks( ValidationBackend $backend ): void
    {
        if ( !file_exists( base_path( "vendor/autoload.php" ) ) ) {
            $backend->error( "composer install has not been run" );
        } else {
            $backend->info( "composer has been run" );
        }

        $composerPackageData = InstalledVersions::getAllRawData();

        $foundDevRequirement = false;
        foreach ( $composerPackageData[0]['versions'] as $installedPackage ) {
            if ($installedPackage['dev_requirement']) {
                $foundDevRequirement = true;
                break;
            }
        }

        if ($foundDevRequirement) {
            $backend->error("URGENT: Found composer packages installed due to `dev_requirement`. This is a security risk on a production system. Please check the documentation for instructions on installing libraries from composer.")
                ->withDocsPath("install/upgrading/");
        }
    }

    private function doConfigChecks( ValidationBackend $backend ): void
    {
        // The basic validation script has it's own env parser, so we actually do these
        // checks a little different, since we can be sure about how Laravel inteprets
        // the settings here

        if ( !file_exists( base_path( ".env" ) ) ) {
            $backend->error( ".env file does not exist" );
        } else {
            $backend->info( ".env file exists" );
        }

        if ( !config( 'app.key' ) ) {
            $backend->error( "APP_KEY is not set in .env" );
        } else {
            $backend->info( "APP_KEY is set in .env" );
        }

        if ( config( 'app.debug' ) ) {
            $backend->warning( "APP_DEBUG is enabled" );
        } else {
            $backend->info( "APP_DEBUG is disabled" );
        }

        if ( config( 'app.env' ) !== "production" ) {
            $backend->warning( "APP_ENV is not set to production" );
        } else {
            $backend->info( "APP_ENV is set to production" );
        }
    }

    private function doMySqlChecks( ValidationBackend $backend ): void
    {
        // Determine MySQL server version
        try {
            $version = \DB::selectOne( "SELECT VERSION() as version" )->version;
            $backend->software( "MySQL", $version );
        } catch (QueryException $e) {
            $backend->error( "Failed to query for MySQL server version: " . $e->getMEssage() );
            return;
        }

        $minVersion = APPLICATION_MANIFEST['mysql_version']['min'];
        $maxVersion = APPLICATION_MANIFEST['mysql_version']['max'];
        $recommendedPrefix = APPLICATION_MANIFEST['mysql_version']['recommended'];
        if ( version_compare( $minVersion, $version, '<=' ) &&
            ($maxVersion === null || version_compare( $version, $maxVersion, '<=' ) ) ) {
            $backend->info( "Running a supported MySQL version (" . $version . "). " . (str_starts_with( $version, $recommendedPrefix ) ? "Version is a recommended version" : "" ) );
        } else {
            if ( version_compare( $version, $minVersion, '<' ) ) {
                $backend->error( "MySQL version " . $minVersion . " or higher required (" . $version . " found)" );
            }
            if ( !str_starts_with( $version, $recommendedPrefix ) ) {
                $backend->warning( "Not running a recommended MySQL version. (" . $version . " found)" );
            }
            if ($maxVersion !== null && version_compare( phpversion(), $maxVersion, '>' ) ) {
                $backend->error( "MySQL version exceeds max supported version " . $maxVersion . " (" . $version . " found)" );
            }
        }

        // What schema/migration are we running?
        // Determine MySQL server version
        try {
            $schemaVersion = \DB::selectOne( "SELECT migration FROM migrations ORDER BY id DESC LIMIT 1" )->migration;
            $backend->software( "DB Schema", $schemaVersion );
        } catch (QueryException $e) {
            $backend->error( "failed to determine schema version: " . $e->getMessage() );
            return;
        }
    }

    private function doLaravelChecks( ValidationBackend $backend ): void
    {
        // Check that required extensions are installed
        $missingExtensions = [];
        $presentExtensions = [];
        foreach ( APPLICATION_MANIFEST['laravel_required_extensions'] as $extension ) {
            if ( !extension_loaded( $extension ) ) {
                $missingExtensions[] = $extension;
            } else {
                $presentExtensions[] = $extension;
            }
        }

        if ( count( $missingExtensions ) > 0 ) {
            $backend->warning( 'Missing required PHP extensions: ' . implode(', ', $missingExtensions) );
        }
        if ( count( $presentExtensions ) > 0 ) {
            $backend->info( "Required extensions found: " . implode(', ', $presentExtensions) );
        }

        $writableDirectories = ["storage/app/", "storage/docstore/", "storage/docstore_customers/", "storage/files/",
            "storage/framework/cache/", "storage/framework/sessions/", "storage/framework/views/",
            "storage/grapher/", "storage/logs/", "storage/tmp/"];

        $writable = [];
        $unwritable = [];
        foreach ($writableDirectories as $directory) {
            $isWritable = $this->isDirectoryWritable($directory);
            if ($isWritable) {
                $writable[] = $directory;
            } else {
                $unwritable[] = $directory;
            }
        }

        if (count($unwritable) === 0) {
            $backend->info( "All storage directories are writable" )
                ->each( $writable, function( Result $result, $directory ) {
                    $result->addAdditionalInfoText( " - " . $directory . " was writable" );
                });
        } else {
            $backend->error( "Found storage directories without write permission" )
                ->each( $unwritable, function( Result $result, $directory ) {
                    $result->addAdditionalInfoText( " - " . $directory . " was unwritable" );
                });
            if (count($writable) > 0) {
                $backend->info( "Found storage directories with write permission" )
                    ->each( $writable, function( Result $result, $directory ) {
                        $result->addAdditionalInfoText( " - " . $directory . " was writable" );
                    });
            }
        }
    }

    /**
     * Returns true if it was possible to write a file into the provided relative filesystem path (eg storage/logs/))
     */
    private function isDirectoryWritable(string $path): bool
    {
        $testFile = rtrim(base_path($path), "/") . "/.permission-test-" . \Str::random(8);

        try {
            $written = @file_put_contents($testFile, "This file is part of the IXP Manager storage directory permission check. If found outside of testing it can safely be deleted.");
            if ($written === false) {
                return false;
            }
            return true;
        } catch (\Throwable $t) {
            return false;
        } finally {
            if (File::exists($testFile)) {
                @unlink($testFile);
            }
        }
    }

}