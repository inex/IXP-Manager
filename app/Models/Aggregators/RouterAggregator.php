<?php

namespace IXP\Models\Aggregators;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Database\Eloquent\Builder;
use IXP\Models\Customer;
use IXP\Models\Router;
use IXP\Models\User;

class RouterAggregator extends Router
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'routers';

    /**
     * Gather the data for looking glass dropdowns
     *
     * This is the dropdown on the top right of the IXP Manager looking glass interface.
     *
     * @param Customer|null $cust
     * @param User|null     $user
     *
     * @return array
     */
    public static function forDropdown( Customer $cust = null, User $user = null ): array
    {
        $routers = self::whereNotNull( 'api' )
            ->where( 'api_type', 1 )
            ->where( 'lg_access', '<=', $user ? $user->getPrivs() : User::AUTH_PUBLIC )
            ->when( !$user, function( Builder $q ) {
                return $q->where( 'quarantine', false );
            } )
            ->orderBy( 'handle' )
            ->get()->keyBy( 'handle' );

        $result = [];
        foreach( $routers as $key => $r ) {
            if( $r->quarantine && !$user->superUser() && !$cust->hasInterfacesInQuarantine() ) {
                continue;
            }
            $result[ $r->type() ][ $key ] = $r->name;
        }

        return $result;
    }

    /**
     * Gather the data for looking glass dropdowns
     *
     * This is the dropdown on the top right of the IXP Manager looking glass interface.
     *
     * @param Customer|null $cust
     * @param User|null     $user
     *
     * @return array
     */
    public static function forTab( Customer $cust = null, User $user = null )
    {
        $routers = self::
//        select( [
//            'routers.handle', 'routers.name', 'routers.updated_at'
//        ] )
//            leftJoin( 'vlan as v', 'v.id', 'routers.vlan_id' )
//            ->leftJoin( 'infrastructure as i', 'i.id', 'v.infrastructureid' )
            whereNotNull( 'api' )
            ->where( 'api_type', 1 )
            ->where( 'lg_access', '<=', $user ? $user->getPrivs() : User::AUTH_PUBLIC )
            ->when( !$user, function( Builder $q ) {
                return $q->where( 'quarantine', false );
            } )
            ->get();

        $result = [];
        foreach( $routers as $key => $r ) {
            if( $r->quarantine && !$user->superUser() && !$cust->hasInterfacesInQuarantine() ) {
                continue;
            }
            $result[ $r->vlan->infrastructure->name ][ $r->protocol ][] = $r;
        }

        return $result;
    }
}