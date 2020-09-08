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

use Illuminate\Database\Eloquent\{
    Builder,
};

use IXP\Models\Customer;

/**
 * IXP\Models\Aggregators\CustomerAggregator
 */
class CustomerAggregator extends Customer
{

    /**
     * Get All customer by vlan and protocol
     *
     * @param int|null $vlanid
     * @param int|null $protocol
     *
     * @return array
     */
    public static function getByVlanAndProtocol( int $vlanid = null, int $protocol = null ): array
    {
        return self::select( [ 'c.id', 'c.name' ] )
            ->from( 'cust AS c' )
            ->leftJoin( 'virtualinterface AS vi', 'vi.custid', 'c.id' )
            ->leftJoin( 'vlaninterface AS vli', 'vli.virtualinterfaceid', 'vi.id' )
            ->leftJoin( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->leftJoin( 'routers AS r', 'r.vlan_id', 'v.id' )
            ->where( 'vli.rsclient', true )
            ->when( $protocol, function( Builder $q, $protocol ) {
                return $q->where( 'r.protocol', $protocol )
                    ->where( "vli.ipv{$protocol}enabled", true );
            }, function( $query ) {
                return $query->where( function( $q ) {
                    $q->where( 'r.protocol', 4 )
                        ->orWhere( 'r.protocol', 6 );
                } )->where( function( $q ) {
                    $q->where( 'vli.ipv4enabled', true )
                        ->orWhere( 'vli.ipv6enabled', true );
                } );
            } )->when( $vlanid, function( Builder $q, $vlanid ) {
                return $q->where( 'v.id', $vlanid );
            } )->distinct( 'c.id' )->orderBy( 'c.name', 'asc' )->get()->toArray();
    }
}