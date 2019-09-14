<?php

namespace IXP\Http\Controllers\Interfaces;

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


use D2EM, Redirect, Former;

use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse,
    JsonResponse
};

use Entities\{
    VlanInterface as VlanInterfaceEntity,
    Vlan as VlanEntity,
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
 * @category   Interfaces
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanInterfaceController extends Common
{
    /**
     * Display all the physical interfaces as a list
     *
     * @return  View
     */
    public function list(): View {
        return view( 'interfaces/vlan/list' )->with([
            'vlis'               => D2EM::getRepository( VlanInterfaceEntity::class )->getForList(),
        ]);
    }

    /**
     * Display a VLAN interface
     *
     * @param int $id ID of vlan Interface
     *
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
     * Duplicate a VLAN interface
     *
     * @param int $fromid ID of VLI that we will get the information from
     * @param int $toid   ID of vlan where we will create the new VLI
     *
     * @return  View
     */
    public function duplicate( int $fromid, int $toid )  {
        return $this->edit( $fromid, null , $toid );
    }

    /**
     * Display the form to edit a VLAM interface
     *
     * @param int $id    The VLAN interface ID
     * @param int $viid  The virtual interface to add this VLI to
     * @param int $duplicateTo  The ID of the vlan Interface that will receive the data of the the other vli ( $id )
     * @return View
     */
    public function edit( int $id = null, int $viid = null, int $duplicateTo = null ): View {

        $vli = false; /** @var VlanInterfaceEntity $vli */
        if( $id and !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $vi = false; /** @var VirtualInterfaceEntity $vi */
        if( $viid and !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $viid ) ) ) {
            abort(404);
        } else if( $vli ) {
            $vi = $vli->getVirtualInterface();
        }

        $old = request()->old();

        if( $vli ) {
            // populate the form with VLAN interface data
            Former::populate([
                'vlan'                      => array_key_exists( 'vlan',                    $old    ) ? $old['vlan']                : ( $duplicateTo                ? $duplicateTo : $vli->getVlan()->getId() ) ,
                'irrdbfilter'               => array_key_exists( 'irrdbfilter',             $old    ) ? $old['irrdbfilter']         : ( $vli->getIrrdbfilter()      ? 1 : 0 ),
                'mcastenabled'              => array_key_exists( 'mcastenabled',            $old    ) ? $old['mcastenabled']        : ( $vli->getMcastenabled()     ? 1 : 0 ),

                'ipv4-enabled'              => array_key_exists( 'ipv4-enabled',            $old    ) ? $old['ipv4-enabled']        : ( $vli->getIpv4enabled()      ? 1 : 0 ),
                'ipv4-address'              => array_key_exists( 'ipv4-address',            $old    ) ? $old['ipv4-address']        : ( $vli->getIPv4Address()      ? $vli->getIPv4Address()->getId() : null ),
                'ipv4-hostname'             => array_key_exists( 'ipv4-hostname',           $old    ) ? $old['ipv4-hostname']       :  $vli->getIpv4hostname() ,
                'ipv4-bgp-md5-secret'       => array_key_exists( 'ipv4-bgp-md5-secret',     $old    ) ? $old['ipv4-bgp-md5-secret'] : $vli->getIpv4bgpmd5secret(),
                'ipv4-can-ping'             => array_key_exists( 'ipv4-can-ping',           $old    ) ? $old['ipv4-can-ping']       : ( $vli->getIpv4canping()      ? 1 : 0 ),
                'ipv4-monitor-rcbgp'        => array_key_exists( 'ipv4-monitor-rcbgp',      $old    ) ? $old['ipv4-monitor-rcbgp']  : ( $vli->getIpv4monitorrcbgp() ? 1 : 0 ),

                'maxbgpprefix'              => array_key_exists( 'maxbgpprefix',            $old    ) ? $old['maxbgpprefix']        : $vli->getMaxbgpprefix(),
                'rsclient'                  => array_key_exists( 'rsclient',                $old    ) ? $old['rsclient']            : ( $vli->getRsclient()         ? 1 : 0 ),
                'rsmorespecifics'           => array_key_exists( 'rsmorespecifics',         $old    ) ? $old['rsmorespecifics']     : ( $vli->getRsMoreSpecifics()  ? 1 : 0 ),
                'as112client'               => array_key_exists( 'as112client',             $old    ) ? $old['as112client']         : ( $vli->getAs112client()      ? 1 : 0 ),
                'busyhost'                  => array_key_exists( 'busyhost',                $old    ) ? $old['busyhost']            : ( $vli->getBusyhost()         ? 1 : 0 ),

                'ipv6-enabled'              => array_key_exists( 'ipv6-enabled',            $old    ) ? $old['ipv6-enabled']        : ( $vli->getIpv6enabled()      ? 1 : 0 ),
                'ipv6-address'              => array_key_exists( 'ipv6-address',            $old    ) ? $old['ipv6-address']        : ( $vli->getIPv6Address()      ? $vli->getIPv6Address()->getId() : null ),
                'ipv6-hostname'             => array_key_exists( 'ipv6-hostname',           $old    ) ? $old['ipv6-hostname']       : $vli->getIpv6hostname(),
                'ipv6-bgp-md5-secret'       => array_key_exists( 'ipv6-bgp-md5-secret',     $old    ) ? $old['ipv6-bgp-md5-secret'] : $vli->getIpv6bgpmd5secret(),
                'ipv6-can-ping'             => array_key_exists( 'ipv6-can-ping',           $old    ) ? $old['ipv6-can-ping']       : ( $vli->getIpv6canping()      ? 1 : 0 ),
                'ipv6-monitor-rcbgp'        => array_key_exists( 'ipv6-monitor-rcbgp',      $old    ) ? $old['ipv6-monitor-rcbgp']  : ( $vli->getIpv6monitorrcbgp() ? 1 : 0 ),
            ]);
        } else {
            // populate the form with default data
            Former::populate([
                'maxbgpprefix'              => array_key_exists( 'maxbgpprefix',           $old    ) ? $old['maxbgpprefix']         : $vi->getCustomer()->getMaxprefixes(),
            ]);
        }

        return view( 'interfaces/vlan/edit' )->with([
            'vlans'                     => D2EM::getRepository( VlanEntity::class )->getNames( false ),
            'vli'                       => $vli ? $vli : false,
            'vi'                        => $vi,
            'duplicateTo'               => $duplicateTo ?? false
        ]);
    }


    /**
     * Add / edit a vlan interface (set all the data needed)
     *
     * @param   StoreVlanInterface $request instance of the current HTTP request
     * @return  RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
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

        // are we duplicating?
        if( $request->input( 'duplicate' ) ) {
            $sourceVli = $vli;
            $vli = new VlanInterfaceEntity;
            D2EM::getRepository( VlanInterfaceEntity::class )->copyLayer2Addresses( $sourceVli, $vli );
            D2EM::persist( $vli );
        }

        if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $request->input( 'viid' ) ) ) ) {
            abort(404 , 'Unknown virtual Interface');
        }

        $v = D2EM::getRepository( VlanEntity::class )->find( $request->input( 'vlan' ) );   /** @var VlanEntity $v */

        if( !$this->setIp( $request, $v, $vli, false ) || !$this->setIp( $request, $v, $vli, true ) ) {
            return Redirect::back()->withInput( $request->all() );
        }

        $vli->setVirtualInterface(  $vi );
        $vli->setVlan(              $v  );
        $vli->setIrrdbfilter(       $request->input( 'irrdbfilter',     false ) );
        $vli->setRsMoreSpecifics(   $request->input( 'rsmorespecifics', false ) );
        $vli->setMcastenabled(      $request->input( 'mcastenabled',    false ) );
        $vli->setMaxbgpprefix(      $request->input( 'maxbgpprefix',    null  ) === "0" ? null : $request->input( 'maxbgpprefix', null ) );
        $vli->setRsclient(          $request->input( 'rsclient',        false ) );
        $vli->setAs112client(       $request->input( 'as112client',     false ) );
        $vli->setBusyhost(          $request->input( 'busyhost',        false ) );
        D2EM::flush();

        // add a warning if we're filtering on irrdb but have not configured one for the customer
        $this->warnIfIrrdbFilteringButNoIrrdbSourceSet($vli);

        AlertContainer::push( 'Vlan Interface updated successfully.', Alert::SUCCESS );

        if( $request->input('redirect2vi' ) || $request->input('duplicated' ) ) {
            return Redirect::to( 'interfaces/virtual/edit/' . $vli->getVirtualInterface()->getId() );
        }

        return Redirect::to( 'interfaces/vlan/list' );
    }

    /**
     * Delete a Vlan Interface and the Layer2Address associated
     *
     * @param   int $id ID of the SflowReceiver
     *
     * @return  JsonResponse
     *
     * @throws
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

        AlertContainer::push( 'The Vlan Interface has been deleted successfully.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }
}