<?php

namespace IXP\Http\Controllers\Api\V4;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Http\JsonResponse;

use IXP\Models\VlanInterface;

/**
 * VlanInterface API Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanInterfaceController extends Controller
{
    /**
     * Get all Layer2Address for a VlanInterface
     *
     * @param VlanInterface $vli VlanInterface
     *
     * @return  JsonResponse
     */
    public function getL2A( VlanInterface $vli ) : JsonResponse
    {
        return response()->json( $vli->layer2addresses()->pluck('mac', 'id')->toArray() );
    }

    /**
     * Get infra / tag / mac / viid structure for sflow data processing
     *
     * FIXME insert reference to documentation - see islandbridgenetworks/IXP-Manager#34
     *
     * @return JsonResponse
     */
    public function sflowLearnedMacs(): JsonResponse
    {
        $macs = VlanInterface::selectRaw(
            'vli.id AS vliid, ma.mac AS mac, vl.number as tag, vl.infrastructureid as infrastructure'
        )->from( 'vlaninterface AS vli' )
        ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
        ->join( 'macaddress AS ma', 'ma.virtualinterfaceid', 'vi.id' )
        ->leftJoin( 'vlan AS vl', 'vl.id', 'vli.vlanid' )
        ->whereNotNull( 'ma.mac' )->whereNotNull( 'vli.id' )
        ->orderBy( 'vliid' )->distinct()->get()->toArray();

        foreach( $macs as $mac ){
            $output[ $mac[ 'infrastructure' ] ][ $mac[ 'tag' ] ][ $mac[ 'mac' ] ] = $mac[ 'vliid' ];
        }

        return response()->json($output ?? []);
    }

    /**
     * Get infra / tag / mac / viid structure for sflow data processing
     *
     * FIXME insert reference to documentation - see islandbridgenetworks/IXP-Manager#34
     *
     * @return JsonResponse
     */
    public function sflowConfiguredMacs(): JsonResponse
    {
        $macs = VlanInterface::selectRaw(
            'vli.id AS vliid, l2a.mac AS mac, vl.number as tag, vl.infrastructureid as infrastructure'
        )->from( 'vlaninterface AS vli' )
        ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'vli.virtualinterfaceid' )
        ->leftJoin( 'l2address AS l2a', 'l2a.vlan_interface_id', 'vli.id' )
        ->leftJoin( 'vlan AS vl', 'vl.id', 'vli.vlanid' )
        ->whereNotNull( 'l2a.mac' )
        ->orderBy( 'vliid' )->distinct()->get()->toArray();

        foreach( $macs as $mac ) {
            $output[ $mac[ 'infrastructure' ] ][ $mac[ 'tag' ] ][ $mac[ 'mac' ] ] = $mac[ 'vliid' ];
        }
        return response()->json($output ?? []);
    }
}