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

use Exception;
use Illuminate\Support\Facades\DB;
use IXP\Models\IPv4Address;
use IXP\Models\IPv6Address;
use IXP\Models\Vlan;

class IpAddressAggregator
{
    /**
     * Bulk add IP addresses from the given array.
     *
     * The array returned contains two further arrays:
     *
     * * `preexisting` => addresses that already existed in the database.
     * * `new`         => addresses added (if `skip == true`) or addresses that would have been added.
     *
     * @param array $addresses
     * @param Vlan $vlan
     * @param IPv4Address|IPv6Address $model
     * @param bool $skip If the address already exists, then skip over it (default). Otherwise, do not add any addresses.
     *
     * @return array
     *
     * @throws
     */
    public static function bulkAdd( array $addresses, Vlan $vlan, $model, bool $skip = true ): array
    {
        $results = [
            'preexisting'  => [],
            'new'          => []
        ];

        DB::beginTransaction();

        try {

            foreach( $addresses as $a ) {
                // does the address already exist?
                $ipAddress = $model::where( 'address', $a )->where( 'vlanid', $vlan->id )->first();

                if( $ipAddress ) {
                    $results[ 'preexisting' ][] = $ipAddress;
                } else {
                    $ipAddress = new $model;
                    $ipAddress->vlanid = $vlan->id;
                    $ipAddress->address = $a;
                    $ipAddress->save();
                    $results['new'][] = $a;
                }
            }

            if( !$skip && count( $results['preexisting'] ) ) {
                DB::rollBack();
            } else {
                DB::commit();
            }

        } catch ( Exception $e ) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

}