<?php
/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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

namespace IXP\Http\Controllers;

use D2EM, Redirect, Former, Input;

use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Http\Controllers\Controller;

use Entities\{
    VlanInterface as VlanInterfaceEntity,
    Vlan as VlanEntity,
    IPv4Address as IPv4AddressEntity,
    IPv6Address as IPv6AddressEntity,
    VirtualInterface as VirtualInterfaceEntity
};

use IXP\Http\Requests\{
    StoreVlanInterface
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};



/**
 * Vlan Interface Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanInterfaceController extends Controller
{
    /**
     * Display all the vlanInterfaces
     *
     * @return  View
     */
    public function list( int $id = null ): View {
        return view(  $id ? 'vlan-interface/view' : 'vlan-interface/index' )->with([
            'listVli'       => D2EM::getRepository( VlanInterfaceEntity::class )->getForList( $id )
        ]);
    }

    /**
     * Display the form to edit a vlan interface
     *
     * @return View
     */
    public function edit( int $id = null, int $viid = null ): View {
        $vli = false;
        /** @var VlanInterfaceEntity $vli */
        if( $id and !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $vi = false;

        if( $viid and !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $viid ) ) ) {
            abort(404);
        }

        if( $vli ) {
            // fill the form with Virtual interface data
            Former::populate([
                'vlan'                      => $vli->getVlan()->getId() ,
                'irrdbfilter'               => $vli->getIrrdbfilter() ? 1 : 0,
                'mcastenabled'              => $vli->getMcastenabled() ? 1 : 0,
                'ipv4-enabled'              => $vli->getIpv4enabled() ? 1 : 0,
                'ipv4-address'              => $vli->getIPv4Address()->getAddress(),
                'ipv4-hostname'             => $vli->getIpv4hostname(),
                'ipv4-bgp-md5-secret'       => $vli->getIpv4bgpmd5secret(),
                'ipv4-can-ping'             => $vli->getIpv4canping() ? 1 : 0,
                'ipv4-monitor-rcbgp'        => $vli->getIpv4canping() ? 1 : 0,
                'maxbgpprefix'              => $vli->getMaxbgpprefix(),
                'rsclient'                  => $vli->getRsclient() ? 1 : 0,
                'as112client'               => $vli->getAs112client() ? 1 : 0,
                'busyhost'                  => $vli->getBusyhost() ? 1 : 0,
                'ipv6-enabled'              => $vli->getIpv6enabled() ? 1 : 0,
                'ipv6-address'              => $vli->getIPv6Address(),
                'ipv6-hostname'             => $vli->getIpv6hostname(),
                'ipv6-bgp-md5-secret'       => $vli->getIpv6bgpmd5secret(),
                'ipv6-can-ping'             => $vli->getIpv6canping()? 1 : 0,
                'ipv6-monitor-rcbgp'        => $vli->getIpv6canping()? 1 : 0,
            ]);
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'vlan-interface/edit' )->with([
            'vlan'                      => D2EM::getRepository( VlanEntity::class )->getNames( false ),
            'vli'                       => $vli ? $vli : false,
            'vi'                        => $vi
        ]);
    }


    /**
     * Edit a vlan interface (set all the data needed)
     *
     * @param   StoreVlanInterface $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function store( StoreVlanInterface $request ): RedirectResponse {

        /** @var VlanInterfaceEntity $vli */
        if( $request->input( 'id', false ) ) {
            // get the existing VlanInterface object for that ID
            if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort( 404, 'Unknown vlan interface' );
            }
        } else {
            $vli = new VlanInterfaceEntity;
            D2EM::persist( $vli );
        }

        if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $request->input( 'viid' ) ) ) ) {
            abort(404 , 'Unknown vlan Interface');
        }

        /** @var VlanEntity $vl */
        if( !( $vl = D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlan' ) ) ) ){
            abort(404, 'Unknown vlan');
        }

        if( $request->input('ipv4-enabled' ) ){
            $ipv4Set = $this->setIp($request,$vl, $vli, false );
        }

        if( $request->input('ipv6-enabled' ) ){
            $ipv6Set = $this->setIp($request,$vl, $vli, true );
        }

        if( ( $request->input('ipv4-enabled' ) && $ipv4Set == false ) || ( $request->input('ipv6-enabled' ) && $ipv6Set == false ) ) {
            return Redirect::to('virtualInterface/add-wizard' )->withInput( Input::all() );
        }

        $vli->setVirtualInterface( $vi );
        $vli->setVlan( $vl );
        $vli->setIrrdbfilter( $request->input( 'irrdbfilter' ) ? 1 : 0 );
        $vli->setMcastenabled( $request->input( 'mcastenabled' ) ? 1 : 0 );


        $vli->setMaxbgpprefix( $request->input( 'maxbgpprefix' ) );

        $vli->setRsclient( $request->input( 'rsclient' ) ? 1 : 0 );
        $vli->setAs112client( $request->input( 'as112client' ) ? 1 : 0 );
        $vli->setBusyhost( $request->input( 'busyhost' ) ? 1 : 0 );

        D2EM::flush();

        AlertContainer::push( 'Vlan Interface updated successfully.', Alert::SUCCESS );

        return Redirect::to( 'virtualInterface/edit/'.$vli->getVirtualInterface()->getId() );

    }

    /**
     *
     * DUPLICATED !!!!
     *
     * Sets IPv4 or IPv6 from form to given VlanInterface.
     *
     * Function checks if IPvX address is provided if IPvX is enabled. Then
     * it checks if given IPvX address exists for current Vlan:
     *
     * * if it exists, it ensures is is not assigned to another interface;
     * * if !exists, creates a new one.
     *
     * @param \Entities\VlanInterface      $vli  Vlan interface to assign IP to
     * @param bool                         $ipv6 Bool to define if IP address is IPv4 or IPv6
     * @return bool
     */
    private function setIp($request, $vl, $vli, $ipv6 = false )
    {

        // Can do better
        $iptype = $ipv6 ? "ipv6" : "ipv4";
        $ipVer  = $ipv6 ? "IPv6" : "IPv4";
        $setterIPv = "set{$ipVer}Address";
        $setterEnabled = "set{$ipVer}enabled";
        $setterHostname = "set{$ipVer}hostname";
        $setterSecret = "set{$ipVer}bgpmd5secret";
        $setterPing = "set{$ipVer}canping";
        $setterMonitor = "set{$ipVer}monitorrcbgp";

        $entity = $ipv6 ? IPv6AddressEntity::class : IPv4AddressEntity::class;

        $addressValue = $request->input( $iptype . '-address' );

        if( !$addressValue ) {
            AlertContainer::push( "Please select or enter an ".$ipVer." address.", Alert::DANGER );
            return false;
        }

        if( !($ip = D2EM::getRepository( $entity )->findOneBy( [ "Vlan" => $vl->getId(), 'address' => $addressValue ] )  ) ){

            $ip = new $entity();
            D2EM::persist( $ip );
            $ip->setVlan( $vl );
            $ip->setAddress( $addressValue );
        }
        else if( $ip->getVlanInterface() && $ip->getVlanInterface() != $vli )
        {
            AlertContainer::push( $ipVer."address ".$addressValue." is already in use.", Alert::DANGER );
            return false;
        }


        // ???
        $vli->$setterIPv( $ip );
        $vli->$setterHostname( $request->input( $iptype . '-hostname' ) );
        $vli->$setterEnabled( true );
        $vli->$setterSecret( $request->input( $iptype . '-bgp-md5-secret' ) );
        $vli->$setterPing( $request->input( $iptype . '-can-ping' ) );
        $vli->$setterMonitor( $request->input( $iptype . '-monitor-rcbgp' ) );

        return true;
    }

}