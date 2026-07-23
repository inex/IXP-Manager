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

use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use Ramsey\Uuid\Uuid;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class ConfigValidator implements Validator
{
    #[\Override]
    public function getName(): string
    {
        return "Configuration validation";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Perform checks of the IXP Manager configuration";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 20;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        $backend->info("Default cache driver is " . config('cache.default') );

        try {
            $key = Uuid::uuid4()->toString();
            $value = Uuid::uuid4()->toString();
            \Cache::put("validation:random:$key", $value, 60);
            $retrieved = \Cache::get("validation:random:$key");
            if ($value !== $retrieved) {
                $backend->error("Cache didn't return the same value that was written");
            }
        } catch ( \Exception $e ) {
            $backend->error("The cache write test encountered an error: " . $e->getMessage());
        }

        if ( !config ( 'ixp.as112.ui_active' ) ) {
            $backend->suggestion( "Did you know IXP-Manager can help you run an AS112 service?")
                ->withDocsPath("features/as112/");
        } else {
            $backend->info("AS112 UI is enabled.");
        }

        if ( config('ixp_fe.frontend.disabled.filtered-prefixes' ) ) {
            $backend->suggestion( "Did you know IXP-Manager has a UI to find filtered prefixes for your " . config('ixp_fe.lang.customer.many') . "?" )
                ->withDocsPath( "features/route-servers/#displaying-filtered-prefixes" );
        } else if ( config('cache.default') === "array" ) {
            $backend->error("A persistent cache is required to use the filtered prefixes feature - cannot use array." );
        }

        if ( config( 'ixp_fe.frontend.disabled.settings' ) ) {
            $backend->suggestion( "Did you know there is a UI for editing the IXP Manager .env file?" )
                ->withDocsPath("features/settings/");
        } else {
            $backend->info( "Settings UI is enabled." );
        }

        if ( config('ixp_fe.frontend.disabled.lg' ) ) {
            $backend->suggestion( "Did you know IXP Manager has a built in Looking Glass UI?")
                ->withDocsPath("features/looking-glass/");
        } else {
            $backend->info( "Looking Glass UI is enabled." );
        }
    }
}