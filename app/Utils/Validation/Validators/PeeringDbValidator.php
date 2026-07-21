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
use IXP\Services\PeeringDb;


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
        return 48;
    }

    public function run( ValidationBackend $backend ): void
    {
        $this->checkPeeringDbOauthIntegration($backend);
        $this->checkPeeringDbApiIntegration($backend);
    }

    private function checkPeeringDbOauthIntegration( ValidationBackend $backend ): void
    {
        if (!config('auth.peeringdb.enabled')) {
            $backend->suggestion("Did you know that IXP Manager supports login with PeeringDb?", documentation_url('features/peeringdb-oauth/'));
        } else {
            $missingConfig = array_filter([ 'client_id', 'client_secret', 'redirect' ], fn( $field ) => config( "services.peeringdb." . $field ) === null );
            if (count($missingConfig) > 0) {
                $backend->error( "PeeringDB OAUTH settings are not complete. Please check your service settings (" . implode(", ", $missingConfig) . ").", documentation_url('features/peeringdb-oauth/'));
            }
        }
    }

    private function checkPeeringDbApiIntegration( ValidationBackend $backend ): void
    {
        if (array_all(['ixp_api.peeringDB.api-key'], fn( $field ) => config( $field ) === null)) {
            // have nothing setup
            $backend->suggestion("Did you know you can integrate with PeeringDB to load network and facility information?");
            return;
        }

        if (config('ixp_api.peeringDB.api-key') != null) {
            // no warning, happy with apikey usage
            if (config('ixp_api.peeringDB.username') != null || config('ixp_api.peeringDB.password') != null) {
                $backend->warning("Your PeeringDB API key is configured, and PeeringDB no longer supports Basic Authentication. Remove the username and password from your configuration.");
            }
            $pdb = app(PeeringDb::class);
            try {
                $pdb->getPeeringAsns();
                if ($pdb->status === 200) {
                    $backend->info("PeeringDB API integration is successful");
                } else {
                    if ($pdb->status === 401) {
                        $backend->error( "Received 401 Not Authorized from PeeringDB. Your API Key may be invalid." );
                    } else if ($pdb->error != null) {
                        $backend->error("Error while performing PeeringDB API request (HTTP status " . $pdb->status . ") : " . $pdb->error);
                    } else {
                        $backend->error("Error while performing PeeringDB API request (HTTP status " . $pdb->status . ") : " . $pdb->error);
                    }
                }
            } catch (\Exception $e) {
                $backend->error("Error performing PeeringDB test API call! (HTTP status " . $pdb->status . ") " . $e->getMessage());
                \Log::error($e);
            }
        } else if (config('ixp_api.peeringDB.username') != null || config('ixp_api.peeringDB.password') != null) {
            $backend->error("PeeringDB no longer supports Basic Authentication. Remove the username and password from your configuration, and provide an API key instead.");
        }
    }
}