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

use Illuminate\Http\Client\ConnectionException;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Infrastructure;


/**
 * This class checks if the PeeringDB integration is active, and warns
 * if legacy configuration options are used.
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class PeeringDbValidator implements Validator
{
    public function getName(): string
    {
        return "Peering DB Setup Validator";
    }

    public function getDescription(): string
    {
        return "Checks PeeringDB integration";
    }

    public function getPriority(): int
    {
        return 100;
    }

    public function run( ValidationBackend $backend ): void
    {
        if (!config('auth.peeringdb.enabled')) {
            $backend->warning("Did you know that IXP Manager supports login with PeeringDb?");
        } else {
            // no creds provided
            // else
            //   username provided
            //   .:. api-key provided

            if( array_any( [ 'services.peeringdb.client_id', 'services.peeringdb.client_secret', 'services.peeringdb.redirect'], fn( $field ) => config($field) === null ) ) {
                $backend->error("PeeringDB OAUTH settings are not complete. Please check your configuration.");
            }

            if (array_all(['ixp_api.peeringDB.username', 'ixp_api.peeringDB.password', 'ixp_api.peeringDB.api-key'], fn( $field ) => config( $field ) === null)) {
                // send to settings / PeeringDB
                $backend->error("PeeringDB login is enabled but no credentials are configured");
            } else {
                if (config('ixp_api.peeringDB.username') != null || config('ixp_api.peeringDB.password') != null) {
                    $backend->warning("Using PeeringDB username and password credentials is not recommended - setup an API key instead!");

                } else if (config('ixp_api.peeringDB.api-key') != null) {
                    // no warning, happy with apikey usage

                }
            }
        }
    }
}