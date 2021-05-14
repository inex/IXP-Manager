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

use Auth;

use Illuminate\Http\{
    JsonResponse,
    Request
};

use IXP\Models\{
    Layer2Address,
    User,
    VlanInterface
};

use IXP\Events\Layer2Address\{
    Added       as Layer2AddressAddedEvent,
    Deleted     as Layer2AddressDeletedEvent
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Layer2Address API Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   APIv4
 * @package    IXP\Http\Controllers\Api\V4
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Layer2AddressController extends Controller
{
    /**
     * Create a mac address to a VLAN Interface
     *
     * @param Request   $r                  instance of the current HTTP request
     * @param bool      $showFeMessage      Should we show the frontend message ?
     *
     * @return  JsonResponse
     *
     */
    public function store( Request $r, bool $showFeMessage = false ): JsonResponse
    {
        $vli = VlanInterface::findOrFail( $r->vlan_interface_id );

        if( !Auth::getUser()->isSuperUser() ) {
            if( !config( 'ixp_fe.layer2-addresses.customer_can_edit' ) ) {
                abort( 404 );
            }

            if( Auth::getUser()->custid !== $vli->virtualInterface->custid ) {
                abort( 403, 'VLI / Customer mismatch' );
            }

            if( $vli->layer2addresses()->count() >= config( 'ixp_fe.layer2-addresses.customer_params.max_addresses' ) ) {
                !$showFeMessage ?: AlertContainer::push( 'The maximum possible MAC addresses have been configured. Please delete a MAC before adding.' , Alert::DANGER );
                return response()->json( [ 'danger' => false, 'message' => 'The maximum possible MAC addresses have been configured. Please delete a MAC before adding.' ] );
            }
        }

        $mac = preg_replace( "/[^a-f0-9]/i", '' , strtolower( $r->mac ) );

        if( strlen( $mac ) !== 12 ) {
            !$showFeMessage ?: AlertContainer::push( 'Invalid or missing MAC addresses.' , Alert::DANGER );
            return response()->json( [ 'danger' => false, 'message' => 'Invalid or missing MAC addresses' ] );
        }

        // Get layer2address for a given vlan
        $exist = Layer2Address::from( 'l2address AS l' )
            ->join( 'vlaninterface AS vli', 'vli.id',  'l.vlan_interface_id' )
            ->join( 'vlan AS v', 'v.id', 'vli.vlanid' )
            ->where( 'mac' , $mac )
            ->where( 'v.id', $vli->vlanid )
            ->count();

        if( $exist ) {
            !$showFeMessage ?: AlertContainer::push( 'The MAC address already exists within this IXP VLAN.' , Alert::DANGER );
            return response()->json( [ 'danger' => false, 'message' => 'The MAC address already exists within this IXP VLAN' ] );
        }

        $l2a = Layer2Address::create( [
            'mac' => $mac,
            'vlan_interface_id' => $r->vlan_interface_id
        ] );

        event( new Layer2AddressAddedEvent( $l2a, User::find( Auth::id() ) ) );
        !$showFeMessage ?: AlertContainer::push( 'MAC address created.' , Alert::SUCCESS );
        return response()->json( [ 'success' => true, 'message' => 'MAC address created.' ] );
    }

    /**
     * Delete a mac address from a Vlan Interface
     *
     * @param Layer2Address     $l2a
     * @param bool              $showFeMessage Should we show the frontend message ?
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function delete( Layer2Address $l2a, bool $showFeMessage = false  ): JsonResponse
    {
        if( !Auth::getUser()->isSuperUser() ) {
            if( !config( 'ixp_fe.layer2-addresses.customer_can_edit' ) ) {
                abort( 404 );
            }

            if( Auth::getUser()->custid !== $l2a->vlanInterface->virtualInterface->custid ) {
                abort( 403, 'MAC address / Customer mismatch' );
            }

            if( $l2a->vlanInterface->layer2addresses->count() <= config( 'ixp_fe.layer2-addresses.customer_params.min_addresses' ) ) {
                !$showFeMessage ?: AlertContainer::push( 'The minimum possible MAC addresses have been configured. Please add a MAC before deleting.' , Alert::DANGER );
                return response()->json( [ 'danger' => false, 'message' => 'The minimum possible MAC addresses have been configured. Please add a MAC before deleting.' ] );
            }
        }

        $l2a->delete();

        event( new Layer2AddressDeletedEvent( $l2a->macFormatted( ':' ), $l2a->vlanInterface, User::find( Auth::id() ) ) );
        !$showFeMessage ?: AlertContainer::push( 'MAC address deleted.' , Alert::SUCCESS );
        return response()->json( [ 'success' => true, 'message' => 'MAC address deleted.' ] );
    }
}