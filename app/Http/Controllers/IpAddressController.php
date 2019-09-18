<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use D2EM, Redirect;

use IPTools\{
    Network
};

use Entities\{
    IPv4Address            as IPv4AddressEntity,
    IPv6Address            as IPv6AddressEntity,
    Vlan                   as VlanEntity
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse,
    JsonResponse
};

use IXP\Http\Requests\{
    DeleteIpAddressesByNetwork,
    StoreIpAddress
};


/**
 * IP Address Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 *
 * @category   Admin
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class IpAddressController extends Controller
{

    /**
     * Return the entity depending on the protocol
     *
     * @param int       $protocol   Protocol of the IP address
     * @param boolean   $entity     Do we need to return the entity ?
     *
     * @return IPv4AddressEntity | IPv6AddressEntity | integer
     */
    private function processProtocol( int $protocol , bool $entity = true )
    {
        if( !in_array( $protocol, [ 4,6 ] ) ){
            abort( 404 , 'Unknown protocol');
        }

        if( $entity ){
            return $protocol == 4 ? IPv4AddressEntity::class : IPv6AddressEntity::class;
        } else {
            return $protocol;
        }
    }

    /**
     * Display the list of the IP Address (IPv4 or IPv6)
     *
     * @param int $protocol Protocol of the IP address
     * @param int $vid ID of the vlan
     *
     * @return view
     * @throws
     */
    public function list( int $protocol, int $vid = null ): View
    {
        $vlan = null;
        if( $vid ) {
            if( !( $vlan = D2EM::getRepository( VlanEntity::class )->find( $vid ) ) ) {
                abort( 404 , 'Unknown vlan');
            }
        }

        $ips = ( $vlan ) ? D2EM::getRepository( $this->processProtocol( $protocol, true ) )->getAllForList( $vlan->getId() ) : [] ;

        return view( 'ip-address/list' )->with([
            'ips'                       => $ips,
            'vlans'                     => D2EM::getRepository( VlanEntity::class )->getNames(),
            'protocol'                  => $protocol,
            'vlan'                      => $vlan ?? false

        ]);
    }

    /**
     * Display the form to add an IP Address (IPv4 or IPv6)
     *
     * @param int   $protocol   Protocol of the IP address
     *
     * @return view
     */
    public function add( int $protocol ): View
    {
        return view( 'ip-address/add' )->with([
            'vlans'                     => D2EM::getRepository( VlanEntity::class )->getNames(),
            'protocol'                  => $this->processProtocol( $protocol, false )
        ]);
    }


    /**
     * Edit the core links associated to a core bundle
     *
     * @param   StoreIpAddress      $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function store( StoreIpAddress $request ): RedirectResponse {

        /** @var VlanEntity $vlan */
        $vlan     = D2EM::getRepository( VlanEntity::class )->find( $request->input('vlan' ) );
        $network  = Network::parse( trim( htmlspecialchars( $request->input('network' ) )  ) );
        $skip     = (bool)$request->input( 'skip',     false );
        $decimal  = (bool)$request->input( 'decimal',  false );
        $overflow = (bool)$request->input( 'overflow', false );

        if( $network->getFirstIP()->version == 'IPv6' ) {
            $result = D2EM::getRepository( IPv6AddressEntity::class )->bulkAdd(
                D2EM::getRepository( IPv6AddressEntity::class )->generateSequentialAddresses( $network, $decimal, $overflow ),
                $vlan, $skip
            );
        } else {
            $result = D2EM::getRepository( IPv4AddressEntity::class )->bulkAdd(
                D2EM::getRepository( IPv4AddressEntity::class )->generateSequentialAddresses( $network ),
                $vlan, $skip
            );
        }

        if( !$skip && count( $result['preexisting'] ) ) {
            AlertContainer::push( "No addresses were added as the following addresses already exist in the database: "
                . implode( ', ', $result['preexisting'] ) . ". You can check <em>skip</em> below to add only the addresses "
                . "that do not already exist.", Alert::DANGER );
            return Redirect::back()->withInput();
        }

        if( count( $result['new'] ) == 0 ) {
            AlertContainer::push( "No addresses were added. " . count( $result['preexisting'] ) . " already exist in the database.",
                Alert::WARNING
            );
            return Redirect::back()->withInput();
        }

        AlertContainer::push( count( $result['new'] ) . ' new IP addresses added to <em>' . $vlan->getName() . '</em>. '
            . ( $skip ? 'There were ' . count( $result['preexisting'] ) . ' preexisting address(es).' : '' ),
            Alert::SUCCESS
        );

        return Redirect::to( route( 'ip-address@list', [ 'protocol' => $network->getFirstIP()->getVersion() == 'IPv6' ? '6' : '4', 'vlanid' => $vlan->getId() ] ) );
    }


    /**
     * Display the form to delete free IP addresses in a VLAN
     *
     * @param  DeleteIpAddressesByNetwork  $request            Instance of the current HTTP request
     * @param  int      $vlanid                Id of the VLan
     *
     * There's actually three ways into this action:
     *
     * 1. standard GET request which just displays for form asking for the network range to delete
     * 2. POST with the network range: finds addresses and displays them for confirmation
     * 3. POST with 'doDelete' parameter: works as (2) but actually deletes the addressess
     *
     * @return View | Redirect
     *
     * @throws
     */
    public function deleteByNetwork( DeleteIpAddressesByNetwork $request, int $vlanid ) {

        /** @var VlanEntity $v */
        if( !( $v = D2EM::getRepository( VlanEntity::class )->find( $vlanid ) ) ) {
            abort(404);
        }

        if( $request->input( 'network' ) ) {

            $network  = Network::parse( trim( htmlspecialchars( $request->input('network' ) )  ) );

            if( $network->getFirstIP()->version == 'IPv6' ) {
                $ips = D2EM::getRepository( IPv6AddressEntity::class )->getFreeAddressesFromList( $v,
                    D2EM::getRepository( IPv6AddressEntity::class )->generateSequentialAddresses( $network, false, false )
                );
            } else {
                $ips = D2EM::getRepository( IPv4AddressEntity::class )->getFreeAddressesFromList( $v,
                    D2EM::getRepository( IPv4AddressEntity::class )->generateSequentialAddresses( $network )
                );
            }

        } else {
            $ips = [];
        }

        if( $request->input( 'doDelete', false ) == "1" ) {

            foreach( $ips as $ip ) {
                D2EM::remove( $ip );
            }

            D2EM::flush();

            AlertContainer::push( 'IP Addresses deleted.', Alert::SUCCESS );
            return redirect( route( 'ip-address@list', [ 'protocol' => $network->getFirstIP()->version == 'IPv6' ? 6 : 4, 'vlanid' => $v->getId() ] ) );
        }

        return view( 'ip-address/delete-by-network' )->with([
            'vlan'                      => $v,
            'network'                   => $request->input( 'network', '' ),
            'ips'                       => $ips,
        ]);
    }


    /**
     * Delete an IP address
     *
     * @param   int     $protocol   Protocol of the IP address
     * @param   int     $id         Router that need to be deleted
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function delete( int $protocol, int $id ): JsonResponse {

        if( !( $ip = D2EM::getRepository( $this->processProtocol( $protocol, true ) )->find( $id ) ) ) {
            abort(404);
        }

        if( $ip->getVlanInterface() ){
            AlertContainer::push( 'This IP address is assigned to a VLAN interface.', Alert::DANGER );
            return response()->json( [ 'success' => false ] );
        }

        D2EM::remove($ip);
        D2EM::flush();

        AlertContainer::push( 'The IP has been successfully deleted.', Alert::SUCCESS );
        return response()->json( [ 'success' => true ] );
    }
}