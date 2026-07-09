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
class As112Validator implements Validator
{

    public function getName(): string
    {
        return "AS112 validator";
    }

    public function getDescription(): string
    {
        return "Check AS112 feature settings";
    }

    public function getPriority(): int
    {
        return 5;
    }

    public function run( ValidationBackend $backend ): void
    {
        if ( !config ( 'ixp.as112.ui_active' ) ) {
            $backend->warning( "Did you know IXP-Manager can help you run an AS112 service?" );
        } else {
            foreach ($this->findVlansAndProtocolsMissingAs112Router() as $vlan) {
                $backend->warning("Missing AS112 IPv" . $vlan->protocol . " router on " . $vlan->name);
            }
        }
    }

    /**
     * Find any Vlans, on ipv4 or ipv6, which are not quarantine Vlan's, that don't have an AS112 router
     * VLAN's aren't directly marked as quarantine so we look for routers which are marked quarantine.
     * @return Collection
     */
    private function findVlansAndProtocolsMissingAs112Router(): Collection
    {
        return Vlan::select(["vlan.*", "protocol"])
            ->crossJoinSub(function ($query) {
                $query->select('protocol')
                    ->from(\DB::raw('(SELECT 4 AS protocol UNION SELECT 6) as protocols'));
            }, 'protocols')
            // Exclude quarantine VLAN's, we don't care about them here.
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('routers')
                    ->whereColumn('routers.vlan_id', 'vlan.id')
                    ->whereColumn('routers.protocol', 'protocols.protocol')
                    ->where('routers.quarantine', 1);
            })
            // Only return rows where the required AS112 active router is missing
            ->whereNotExists(function ($query) {
                $query->select(\DB::raw(1))
                    ->from('routers')
                    ->where('routers.type', Router::TYPE_AS112)
                    ->whereColumn('routers.vlan_id', 'vlan.id')
                    ->whereColumn('routers.protocol', 'protocols.protocol')
                    ->where('routers.quarantine', 0);
            })
            ->get();
    }
}