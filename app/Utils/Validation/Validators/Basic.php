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

        $backend->software('PHP', $phpVersion);

        $minVersion = APPLICATION_MANIFEST['php_version']['min'];
        /** @var null|string $maxVersion */
        $maxVersion = APPLICATION_MANIFEST['php_version']['max'];
        if ( version_compare( $phpVersion, $minVersion, '>=' ) && ($maxVersion === null || version_compare( $phpVersion, $maxVersion, '<=')) ) {
            $backend->info( "Running a supported PHP version (" . $phpVersion . "). " .
                (str_starts_with( $phpVersion,  APPLICATION_MANIFEST['php_version']['recommended'])
                    ? "Version is a recommended version" : "") );
        } else {
            if ( version_compare( $phpVersion, $minVersion, '<' ) ) {
                $backend->error( "PHP version " . $minVersion . " or higher required (" . $phpVersion . " found)" );
            }
            if ($maxVersion !== null && version_compare( $phpVersion, $maxVersion, '>' ) ) {
                $backend->error("PHP version exceeds max supported version " . $maxVersion . " (" . $phpVersion . " found)" );
            }
        }
        if ( !str_starts_with( $phpVersion, APPLICATION_MANIFEST['php_version']['recommended'] ) ) {
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
            $backend->software('PDO MySQL', phpversion( 'pdo_mysql' ) ?: 'unknown');
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
        /** @var null|string $maxVersion */
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
            if ($maxVersion !== null && version_compare( $version, $maxVersion, '>' ) ) {
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

        // storage/grapher is not present in a git clone, so we don't include it in this list.
        // Since we have a need to create additional directories (grapher), add a separate directory create check here.
        // Although file and directory creation have the same permissions, external factors can cause one to fail
        // and not the other.
        $writableDirectories = ["storage/app/", "storage/docstore/", "storage/docstore_customers/", "storage/files/",
            "storage/framework/cache/", "storage/framework/sessions/", "storage/framework/views/",
            "storage/logs/", "storage/tmp/", "storage/"];

        $this->testWritePermission($backend, "file", $writableDirectories);
        $this->testWritePermission($backend, "directory", $writableDirectories);
    }

    /**
     * Parameterized storage directory write test, since we have to test file creation as well as directory creation
     * @param ValidationBackend $backend
     * @param string $type
     * @param string[] $directories
     */
    private function testWritePermission( ValidationBackend $backend, string $type, array $directories ): void
    {
        $writeOk = [];
        $writeFail = [];
        foreach ($directories as $directory) {
            $isWritable = $type === "file"
                ? $this->haveFileWritePermission($directory)
                : $this->haveDirectoryWritePermission($directory);
            if ($isWritable) {
                $writeOk[] = $directory;
            } else {
                $writeFail[] = $directory;
            }
        }

        if (count($writeFail) === 0) {
            $backend->info( "All " . $type . " write permission tests passed" )
                ->each( $writeOk, function( Result $result, $directory ) {
                    $result->addAdditionalInfoText( " - " . $directory . " was writable");
                } );
        } else {
            $backend->error( "Missing " . $type . " write permission for some storage directories:" )
                ->each( $writeFail, function( Result $result, $directory ) use ($type) {
                    $result->addAdditionalInfoText( " - Failed to create test " . $type . " in " . $directory );
                });
            if (count($writeOk) > 0) {
                $backend->info( "Had " . $type . " write permission for some storage directories:" )
                    ->each( $writeOk, function( Result $result, $directory ) {
                        $result->addAdditionalInfoText( " - " . $directory . " was writable" );
                    });
            }
        }
    }

    /**
     * Returns true if it was possible to write a file into the provided relative filesystem path (eg storage/logs/))
     */
    private function haveFileWritePermission( string $path ): bool
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

    /**
     * Returns true if it was possible to write a new directory into the provided relative filesystem path (eg storage/)
     */
    private function haveDirectoryWritePermission( string $path ): bool
    {
        $testDir = rtrim(base_path($path), "/") . "/.permission-test-" . \Str::random(8);

        try {
            $written = @mkdir($testDir);
            if ($written === false) {
                return false;
            }
            return true;
        } catch (\Throwable $t) {
            return false;
        } finally {
            if (File::exists($testDir)) {
                @rmdir($testDir);
            }
        }
    }

}