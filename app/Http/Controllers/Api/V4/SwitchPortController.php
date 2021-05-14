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

use IXP\Models\SwitchPort;

/**
 * SwitchPort Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchPortController extends Controller
{
    /**
     * Get the customer for a switch port
     *
     * @param   SwitchPort $sp The ID of the switch port to query
     *
     * @return  JsonResponse JSON customer object
     */
    public function customer( SwitchPort $sp ): JsonResponse
    {
        return response()->json( [
            'customer' =>  SwitchPort::selectRaw( 'COUNT( c.id ) as nb, c.id, c.name' )
                ->from( 'switchport AS sp' )
                ->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
                ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
                ->leftJoin( 'cust AS c', 'c.id', 'vi.custid' )
                ->where( 'sp.id', $sp->id )
                ->groupBy( 'c.id' )
                ->first()->toArray()
        ] );
    }

    /**
     * Check if the switch port has a physical interface set
     *
     * @param   SwitchPort $sp  Id of the switchport
     *
     * @return  JsonResponse JSON response
     */
    public function physicalInterface( SwitchPort $sp ): JsonResponse
    {
        if( ( $pi = $sp->physicalInterface ) ){
            return response()->json([
                'pi' => [
                    'id'         => $pi->id,
                    'status'     => $pi->status,
                    'statusText' => $pi->status(),
                ]
            ]);
        }
        return response()->json( [] );
    }
}