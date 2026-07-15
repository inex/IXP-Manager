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

use Illuminate\Database\QueryException;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class BasicValidator implements Validator
{
    public function getName(): string
    {
        return "Basic validations";
    }

    public function getDescription(): string
    {
        return "Perform some basic system checks";
    }

    public function getPriority(): int
    {
        return 0;
    }

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
            $backend->info( "Running a supported PHP version. " .
                (str_starts_with( $phpVersion,  $manifest['php_version']['recommended'])
                    ? "Version is a recommended version" : "") );
        } else {
            if ( version_compare( $phpVersion, $manifest['php_version']['min'], '<' ) ) {
                $backend->error( "PHP version " . $manifest['php_version']['min'] . " or higher required" );
            }
            if ($manifest['php_version']['max'] !== null && version_compare( $phpVersion, $manifest['php_version']['max'], '>' ) ) {
                $backend->error("PHP version exceeds max supported version " . $manifest['php_version']['max']);
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
            $backend->info( "Running a supported MySQL version. " . (str_starts_with( $version, $recommendedPrefix ) ? "Version is a recommended version" : "" ) );
        } else {
            if ( version_compare( $version, $minVersion, '<' ) ) {
                $backend->error( "MySQL version " . $minVersion . " or higher required" );
            }
            if ( !str_starts_with( $version, $recommendedPrefix ) ) {
                $backend->warning( "Not running a recommended MySQL version." );
            }
            if ($maxVersion !== null && version_compare( phpversion(), $maxVersion, '>' ) ) {
                $backend->error( "MySQL version exceeds max supported version " . $maxVersion );
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
    }
}