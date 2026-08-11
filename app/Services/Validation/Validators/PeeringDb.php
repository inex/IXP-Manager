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
use IXP\Services\PeeringDb as PeeringDbService;
use IXP\Services\Validation\Dto\Result;


/**
 * This class checks if the PeeringDB integration is active, and warns
 * if legacy configuration options are used.
 *
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class PeeringDb implements Validator
{
    #[\Override]
    public function getName(): string
    {
        return "Peering DB Setup Validator";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Checks PeeringDB integration";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 48;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        $this->checkPeeringDbOauthIntegration($backend);
        $this->checkPeeringDbApiIntegration($backend);
    }

    private function checkPeeringDbOauthIntegration( ValidationBackend $backend ): void
    {
        if (!config('auth.peeringdb.enabled')) {
            $backend->suggestion("Did you know that IXP Manager supports login with PeeringDb?")
                ->withDocsPath('features/peeringdb-oauth/')
                ->withSettingsLink("auth", "peeringdb_oauth_enabled")
            ;
        } else {
            $backend->info("Login with PeeringDB is enabled");

            // Key is the last part of the config key string. Value is the field on the settings ui page.
            $configToSetting = [
                "client_id"     => "peeringdb_oauth_client_id",
                "client_secret" => "peeringdb_oauth_client_secret",
                "redirect"      => "peeringdb_oauth_redirect",
            ];

            $missingConfig = array_filter( array_keys($configToSetting), fn( $field ) => config( "services.peeringdb." . $field ) === null );
            if (count($missingConfig) > 0) {
                $backend->error( "PeeringDB OAUTH settings are not complete. Please check your service settings.")
                    ->withDocsPath('features/peeringdb-oauth/')
                    ->withSettingsLink("auth", $configToSetting[$missingConfig[0]])
                    ->each($missingConfig, function (Result $result, $configKey) {
                        $result->addAdditionalInfoText("PeeringDB OAuth " . $configKey . " is missing!");
                    });
                ;
            }
        }
    }

    private function checkPeeringDbApiIntegration( ValidationBackend $backend ): void
    {
        if (getenv("IXP_API_PEERING_DB_USERNAME") != null || getenv("IXP_API_PEERING_DB_PASSWORD") != null) {
            $backend->warning("PeeringDB no longer supports basic authentication. IXP_API_PEERING_DB_USERNAME and IXP_API_PEERING_DB_PASSWORD should be removed from your .env file.");
        }

        if (null === config('ixp_api.peeringDB.api-key')) {
            // have nothing setup
            $backend->suggestion("We recommend configuring a PeeringDB API key for reduced rate limits.")
                ->withSettingsLink("third_party", "peeringdb_api_key");
            return;
        } else {
            $backend->info("PeeringDB API key is correctly set.");
        }

        $pdb = app(PeeringDbService::class);
        try {
            $pdb->getPeeringAsns();
            if ($pdb->status === 200) {
                $backend->info("PeeringDB API integration is successful");
            } else {
                if ($pdb->status === 401) {
                    $backend->error( "Received 401 Not Authorized from PeeringDB. " . ($pdb->error ?? '') );
                } else {
                    $backend->error("Error while performing PeeringDB API request (HTTP status " . $pdb->status . ") : " . ($pdb->error ?? ''));
                }
            }
        } catch (\Exception $e) {
            $backend->error("Error performing PeeringDB test API call! (HTTP status " . $pdb->status . ") " . $e->getMessage());
            \Log::error($e);
        }
    }
}