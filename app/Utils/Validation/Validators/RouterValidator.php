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

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Infrastructure;
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
            $backend->warning("Did you know that IXP Manager can generate configuration for route servers?");
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
                $backend->warning("Did you know IXP-Manager supports RPKI for route server configuration?");
            } else if (!is_string(config('ixp.rpki.rtr1.host')) || !is_string(config('ixp.rpki.rtr2.host'))) {
                $backend->warning("A second RPKI instance is recommended for redundancy.");
            }
        }
    }
}