<?php

namespace IXP\Utils\Lang;

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

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Which files are scanned for translatable strings, and how a string is
 * described to the translator.
 *
 * Only the user facing parts of IXP Manager are translated. Admin and
 * superuser-only screens stay in English, as do the API and router
 * configuration templates (which are machine facing).
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Utils\Lang
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Scope
{
    /**
     * Directories scanned, relative to the base path.
     *
     * We scan all of app/ and resources/views/: a `__()` outside the user
     * facing scope is a deliberate act by whoever wrote it, and it is better
     * to translate a string we did not plan for than to silently drop one.
     * EXCLUDE below is what keeps machine facing output out.
     *
     * @var string[]
     */
    public const array PATHS = [
        'app',
        'resources/views',
        'resources/skins',
    ];

    /**
     * Path fragments that are never scanned.
     *
     * @var string[]
     */
    public const array EXCLUDE = [
        // machine facing: router configs, JSON exports, SQL
        'resources/views/api/',
        'resources/views/vendor-e2f/',
        'resources/skins/docker/',
        // console output is for operators at a terminal, not end users
        'app/Console/',
    ];

    /**
     * File extensions scanned.
     *
     * @var string[]
     */
    public const array EXTENSIONS = [ 'php' ];

    /**
     * Path prefix => human label, most specific first.
     *
     * Used for the `context` column of the translator spreadsheet so that
     * they can work through one area of the site at a time.
     *
     * @var array<string,string>
     */
    public const array CONTEXTS = [
        'resources/views/layouts/menus/' => 'Menu',
        'resources/views/layouts/'       => 'Page furniture',
        'resources/views/auth/emails/'   => 'Email',
        'resources/views/emails/'        => 'Email',
        'resources/views/auth/'          => 'Login and passwords',
        'resources/views/dashboard/'     => 'Dashboard',
        'resources/views/profile/'       => 'My profile',
        'resources/views/customer/'      => 'Member details',
        'resources/views/user/'          => 'User administration',
        'resources/views/contact/'       => 'Contacts',
        'resources/views/docstore/'      => 'Document store',
        'resources/views/docstore-customer/' => 'Document store',
        'resources/views/peering-manager/'   => 'Peering manager',
        'resources/views/peering-matrix/'    => 'Peering matrix',
        'resources/views/rs-filter/'     => 'Route server filters',
        'resources/views/irrdb/'         => 'IRRDB',
        'resources/views/api-key/'       => 'API keys',
        'resources/views/statistics/'    => 'Statistics',
        'resources/views/services/lg/'   => 'Looking glass',
        'resources/views/patch-panel-port/'  => 'Patch panels',
        'resources/views/errors/'        => 'Error pages',
        'resources/views/content/'       => 'Static content',
        'resources/views/vendor/mail/'   => 'Email furniture',
        'app/Http/Controllers/'          => 'Message or alert',
        'app/Http/Requests/'             => 'Validation message',
        'app/Mail/'                      => 'Email subject',
        'app/Models/'                    => 'Data label',
    ];

    /**
     * All files in scope.
     *
     * @return SplFileInfo[]
     */
    public static function files(): array
    {
        $files = [];

        foreach( self::PATHS as $path ) {
            $dir = base_path( $path );

            if( !is_dir( $dir ) ) {
                continue;
            }

            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS )
            );

            /** @var SplFileInfo $file */
            foreach( $it as $file ) {
                if( !$file->isFile()
                        || !in_array( $file->getExtension(), self::EXTENSIONS, true )
                        || self::excluded( $file->getPathname() ) ) {
                    continue;
                }

                $files[] = $file->getPathname();
            }
        }

        sort( $files );

        return $files;
    }

    /**
     * Is this path excluded?
     *
     * @param   string  $path
     *
     * @return bool
     */
    public static function excluded( string $path ): bool
    {
        $relative = self::relative( $path );

        foreach( self::EXCLUDE as $fragment ) {
            if( str_starts_with( $relative, $fragment ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * A path relative to the base path, for display and matching.
     *
     * @param   string  $path
     *
     * @return string
     */
    public static function relative( string $path ): string
    {
        $base = base_path() . DIRECTORY_SEPARATOR;

        return str_starts_with( $path, $base ) ? substr( $path, strlen( $base ) ) : $path;
    }

    /**
     * A human label for where a string appears.
     *
     * @param   string  $path
     *
     * @return string
     */
    public static function context( string $path ): string
    {
        $relative = self::relative( $path );

        foreach( self::CONTEXTS as $prefix => $label ) {
            if( str_starts_with( $relative, $prefix ) ) {
                return $label;
            }
        }

        return 'Other';
    }
}
