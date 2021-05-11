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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use App;

use IXP\Models\{
    Aggregators\CustomerAggregator,
    Customer,
    PatchPanel,
    Vlan
};

use Illuminate\Http\{
    JsonResponse,
    Request
};
use IXP\Services\PeeringDb;

/**
 * Customer API v4 Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerController extends Controller
{
    /**
     * Get the switches for a customer
     *
     * @param Request   $r      instance of the current HTTP request
     * @param Customer  $cust
     *
     * @return  JsonResponse
     */
    public function switches( Request $r, Customer $cust ): JsonResponse
    {
        $ppp = PatchPanel::findOrFail( $r->patch_panel_id );

        $switches = Customer::select( [ 's.id AS id', 's.name' ] )
            ->from( 'cust AS c' )
            ->join( 'virtualinterface AS vi', 'vi.custid', 'c.id' )
            ->join( 'physicalinterface AS pi', 'pi.virtualinterfaceid', 'vi.id' )
            ->join( 'switchport AS sp', 'sp.id', 'pi.switchportid' )
            ->join( 'switch AS s', 's.id', 'sp.switchid' )
            ->join( 'cabinet AS cab', 'cab.id', 's.cabinetid' )
            ->where( 'cab.locationid', $ppp->cabinet->locationid )
            ->where( 'c.id', $cust->id )
            ->get()->keyBy( 'id' )->toArray();

        return response()->json( [ 'hasSwitches' => (bool)count( $switches ) , 'switches' => $switches ] );
    }

    /**
     * Get network information from PeeringDb by ASN
     *
     * For return information:
     * @see \IXP\Services\PeeringDb::getNetworkByAsn()
     *
     * @param   string  $asn
     *
     * @return  JsonResponse
     */
    public function queryPeeringDbWithAsn( string $asn ): JsonResponse
    {
        return response()->json( App::make( PeeringDb::class )->getNetworkByAsn( $asn ) );
    }

    /**
     * Get Customer depending on the Vlan and Protocol
     *
     * @param   Request $r instance of the current HTTP request
     *
     * @return  JsonResponse
     */
    public function byVlanAndProtocol( Request $r ): JsonResponse
    {
        $vlanid = null;

        if( $r->vlanid ) {
            $vlan = Vlan::findOrFail( $r->vlanid );
            $vlanid = $vlan->id;
        }

        if( !in_array( $protocol = $r->protocol, [ null, 4, 6 ], false ) ) {
            abort( 404 );
        }

        return response()->json( [ 'listCustomers' => CustomerAggregator::getByVlanAndProtocol( $vlanid, $protocol ) ] );
    }
}