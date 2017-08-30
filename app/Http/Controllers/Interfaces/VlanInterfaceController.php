<?php

namespace IXP\Http\Controllers\Interfaces;

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


use D2EM, Redirect, Former, Input;

use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse,
    JsonResponse,
    Request
};

use IXP\Traits\{
    Common,
    Interfaces
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

use OSS_String;

/**
 * Vlan Interface Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanInterfaceController extends Controller
{
    use Common;

    use Interfaces;
    /**
     * Display all the physical interfaces as a list
     *
     * @return  View
     */
    public function list(): View {
        return view( 'interfaces/vlan/list' )->with([
            'vlis'               => D2EM::getRepository( VlanInterfaceEntity::class )->getForList()
        ]);
    }

    /**
     * Display a vlan Interface
     *
     * @param int $id ID of vlan Interface
     * @return  View
     */
    public function view( int $id ): View {
        if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        return view( 'interfaces/vlan/view' )->with([
            'vli' => $vli
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
        /** @var VirtualInterfaceEntity $vi */
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
                'ipv4-address'              => $vli->getIPv4Address() ? $vli->getIPv4Address()->getAddress() : null,
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
        else{
            // fill the form with Virtual interface data
            $md5 = OSS_String::random();
            Former::populate([
                'maxbgpprefix'              => $vi->getCustomer()->getMaxprefixes(),
                'ipv4-bgp-md5-secret'       => $md5,
                'ipv6-bgp-md5-secret'       => $md5,

            ]);
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'interfaces/vlan/edit' )->with([
            'vlan'                      => D2EM::getRepository( VlanEntity::class )->getNames( false ),
            'vli'                       => $vli ? $vli : false,
            'vi'                        => $vi,
            'as112UiActive'             => $this->as112UiActive()
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
            abort(404 , 'Unknown virtual Interface');
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
            if( $request->input( 'id' ) ) {
                $urlRedirect = 'interfaces/vlan/edit/'.$vli->getId().'vintid/'.$vli->getVirtualInterface()->getId();
            } else {
                $urlRedirect = 'interfaces/vlan/add/0/vintid/'.$vi->getId();
            }

            return Redirect::to( $urlRedirect )->withInput( Input::all() );
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

        if( $request->input('redirect2vi' ) ) {
            $urlRedirect = 'interfaces/virtual/edit/' . $vli->getVirtualInterface()->getId();
        } else {
            $urlRedirect = 'interfaces/vlan/list';
        }

        return Redirect::to( $urlRedirect );

    }

    /**
     * Delete a Vlan Interface and the Layer2Address associated
     *
     * @param   int $id ID of the SflowReceiver
     * @return  JsonResponse
     */
    public function delete( int $id ): JsonResponse {
        /** @var VlanInterfaceEntity $vli */
        if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        foreach( $vli->getLayer2Addresses() as $l2a ) {
            D2EM::remove( $l2a );
        }

        D2EM::remove( $vli );
        D2EM::flush();

        AlertContainer::push( 'The Physical Interface has been deleted successfully.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }

}