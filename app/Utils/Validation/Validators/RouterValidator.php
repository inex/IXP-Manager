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

use Carbon\Carbon;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Router;
use IXP\Models\Vlan;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class RouterValidator implements Validator
{

    public function getName(): string
    {
        return "Router validator";
    }

    public function getDescription(): string
    {
        return "Check router configuration";
    }

    public function getPriority(): int
    {
        return 10;
    }

    public function run( ValidationBackend $backend ): void
    {
        if (Router::where('type', Router::TYPE_ROUTE_SERVER)->count() === 0) {
            $backend->suggestion("Did you know that IXP Manager can generate configuration for route servers?");
        } else {
            $routeServersNoRpki = [];
            foreach (Vlan::all() as $vlan) {
                $rsNoRpki = Router::where('vlan_id', $vlan->id)
                    ->where('type', Router::TYPE_ROUTE_SERVER)
                    ->whereIn('protocol', [4, 6])
                    ->where('rpki', 0)
                    ->get();
                $routeServersNoRpki = array_merge($routeServersNoRpki, $rsNoRpki->all());
            }

            if (count($routeServersNoRpki) > 0) {
                $backend->warning("Found Route Servers without RPKI enabled: " . implode(", ",
                    array_map(fn($router) => $router->handle, $routeServersNoRpki)));
            }

            if (!is_string(config('ixp.rpki.rtr1.host')) && !is_string(config('ixp.rpki.rtr1.host'))) {
                // neither set..
                $backend->suggestion("Did you know IXP-Manager supports RPKI for route server configuration?");
            } else if (!is_string(config('ixp.rpki.rtr1.host')) || !is_string(config('ixp.rpki.rtr2.host'))) {
                $backend->warning("A second RPKI instance is recommended for redundancy.");
            }
        }

        $needsLookingGlass = [];
        foreach (Router::all() as $router) {
            // exclude quarantine?
            if (!$router->quarantine) {
                if ($router->api_type === null || $router->api_type == 0) {
                    $needsLookingGlass[] = $router->handle;
                } else if ($router->api === null) {
                    $backend->error("Router " . $router->handle . " has Looking Glass API type configured, but API endpoint is empty");
                }

                if ($router->updated_at === null) {
                    // should this be a single suggest?
                    $backend->warning("Router " . $router->handle . " has not been updated - are you using the router configuration API?");
                } else if (Carbon::now()->diffInHours($router->updated_at) > 24 ) {
                    $backend->error("Router " . $router->handle . " has not updated for over 24 hours! Is this out of date?");
                }
            }
        }
        if (count($needsLookingGlass) > 0) {
            $backend->warning("We recommend configuring Looking Glass on all routers - some found without: " . implode(", ", $needsLookingGlass));
        }
    }
}