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

namespace IXP\Services\Validation\Validators;

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
final class IxpManagerRunningLatestVersion implements Validator
{
    #[\Override]
    public function getName(): string
    {
        return "IXP Manager version check";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Records which version of IXP Manager is installed, and notifies if an update is available.";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 5;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        $backend->software("IXP Manager", APPLICATION_VERSION);

        try {
            $apiResponse = \Http::withHeader('User-Agent', 'IXP-Manager-validation-tool')
                ->get('https://api.github.com/repos/inex/IXP-Manager/tags');
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to lookup tags using Github API: " . $e->getMessage());
        }

        // Extract IXP Manager published versions and compare against the installed version
        // Github sorts results by their version string.
        $tags = $apiResponse->json();
        $runningVersion = ltrim( APPLICATION_VERSION, "v" );
        $latestVersion = ltrim( $tags[0]['name'], "v" );
        if ( version_compare( $runningVersion, $latestVersion, '<' ) ) {
            $backend
                ->warning( "A newer version of IXP Manager is available: " . htmlentities( $tags[0]['name'], ENT_QUOTES, 'UTF-8' ) )
                ->withDocsPath('install/upgrading/');
        } else if ( version_compare( $runningVersion, $latestVersion, '=' ) ) {
            $backend->info( "Running latest version of IXP Manager: v" . APPLICATION_VERSION );
        } else {
            $backend->info( "Local IXP Manager is ahead of GitHub releases: v" . APPLICATION_VERSION );
        }
    }
}
