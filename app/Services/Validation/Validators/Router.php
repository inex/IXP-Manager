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

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use IXP\Contracts\Validation\ValidationBackend;
use IXP\Contracts\Validation\Validator;
use IXP\Models\Customer;
use IXP\Models\Router as RouterModel;
use IXP\Models\VlanInterface;
use IXP\Services\Validation\Dto\Result;

/**
 * @author Thomas Kerin <thomas@islandbridgenetworks.ie>
 */
class Router implements Validator
{

    #[\Override]
    public function getName(): string
    {
        return "RPKI for Router Configuration";
    }

    #[\Override]
    public function getDescription(): string
    {
        return "Check router configuration";
    }

    #[\Override]
    public function getPriority(): int
    {
        return 40;
    }

    #[\Override]
    public function run( ValidationBackend $backend ): void
    {
        if (RouterModel::where('type', RouterModel::TYPE_ROUTE_SERVER)->count() === 0) {
            $backend->suggestion("Did you know that IXP Manager can generate configuration for route servers?")
                ->withDocsPath('features/route-servers/');
        } else {
            $backend->info("Found route servers configured");

            $routeServersNoRpki = RouterModel::where('type', RouterModel::TYPE_ROUTE_SERVER)
                ->where('rpki', 0)
                ->exists();

            if (!config('ixp.rpki.rtr1.host') && !config('ixp.rpki.rtr2.host')) {
                // No RPKI servers configured - suggest they setup the feature
                $backend->suggestion( "Did you know IXP Manager supports RPKI for route server configuration?" )
                    ->withDocsPath( 'features/rpki/' )
                    ->withSettingsLink( "route_servers", "rs_rpki_rtr1_host" );

            } else if (!config('ixp.rpki.rtr1.host')) {
                // have RTR2 but not RTR1
                $backend->error("RPKI RTR1 is the primary RPKI server, RTR2 is secondary/fallback. Without RTR1 your configuration won't use RPKI!")
                    ->withSettingsLink( "route_servers", "rs_rpki_rtr1_host" );
            } else if (!config('ixp.rpki.rtr2.host')) {
                // Only missing one RPKI server.. suggest a second.
                $backend->suggestion("A second RPKI instance is recommended for redundancy.")
                    ->withDocsPath('features/rpki/')
                    ->withSettingsLink("route_servers", "rs_rpki_rtr1_host");
            } else {
                $backend->info("Found RPKI hosts configured");
            }

            if ( ( config('ixp.rpki.rtr1.host') || config('ixp.rpki.rtr2.host') ) && $routeServersNoRpki) {
                // Have an RPKI server, but there are route servers without RPKI:
                $backend->warning("RPKI enabled but found Route Servers without RPKI enabled")
                    ->withDocsPath('features/rpki/')
                ;
            }
        }

        $needsLookingGlass = [];
        foreach (RouterModel::all() as $router) {
            // exclude quarantine?
            if (!$router->quarantine) {
                if ($router->api_type == 0) {
                    $needsLookingGlass[] = $router;
                } else if ($router->api === null) {
                    $backend->error("Router " . $router->handle . " has Looking Glass API type configured, but API endpoint is empty")
                        ->withDocsPath('features/looking-glass/');
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
            $backend->warning("We recommend configuring Looking Glass on all routers - some found without")
                ->each($needsLookingGlass, function (Result $result, RouterModel $router) {
                    $result->addAdditionalInfoUrl(route("router@edit", ['router' => $router->id]), $router->name);
                })
                ->withDocsPath('features/looking-glass/');
        }

        if ( $customerRsVlansWithoutIrrdbFiltering = $this->findRouteServerClientsWithoutIrrdbFiltering() ) {
            $backend->error("Found customer VLAN's that are route server clients without IRRDB filtering enabled!")
                ->withDocsPath("usage/interfaces/#general-vlan-settings")
                ->addAdditionalInfoText("VLAN Interfaces (max 5 returned):")
                ->each($customerRsVlansWithoutIrrdbFiltering, function (Result $result, $vlan) {
                    $result->addAdditionalInfoUrl(route("vlan-interface@edit", ['vli' => $vlan->id]), "Vlan Interface " . $vlan->id . " (".$vlan->virtualInterface->customer->name . ")");
                });
        }
    }

    private function findRouteServerClientsWithoutIrrdbFiltering(): array
    {
        return VlanInterface::with('virtualInterface.customer')
            ->whereRsclient(1)
            ->whereIrrdbfilter(0)
            ->whereHas('virtualInterface.customer', function (Builder $query) {
                $query->whereRaw(Customer::SQL_CUST_CURRENT);
                $query->whereRaw(Customer::SQL_CUST_ACTIVE);
            })
            ->limit(5)
            ->get()
            ->all();
    }
}