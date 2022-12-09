<?php

namespace IXP\Http\Controllers\Interfaces;

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

use DB;
use Exception;
use Redirect, Former;

use Illuminate\View\View;

use IXP\Models\{
    Layer2Address,
    VirtualInterface,
    Vlan,
    VlanInterface
};

use Illuminate\Http\{
    RedirectResponse,
    Request
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
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Interfaces
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class VlanInterfaceController extends Common
{
    /**
     * Display all the physical interfaces as a list
     *
     * @return  View
     */
    public function list(): View
    {
        return view( 'interfaces/vlan/list' )->with([
            'vlis'      => VlanInterface::with( 'virtualInterface.customer' )
                ->with( 'vlan' )
                ->with( 'ipv4address' )
                ->with( 'ipv6address' )
                ->get()
        ]);
    }

    /**
     * Duplicate a VLAN interface
     *
     * @param VlanInterface $vli     VLI that we will get the information from
     * @param Vlan          $v       vlan where we will create the new VLI
     *
     * @return  View
     */
    public function duplicateForm( VlanInterface $vli, Vlan $v ): View
    {
        return $this->edit( request(), $vli, null , $v );
    }

    /**
     * Display the form to edit a VLAM interface
     *
     * @param Request           $r
     * @param VirtualInterface  $vi The virtual interface to add this VLI to
     *
     * @return View
     */
    public function create( Request $r, VirtualInterface $vi ): View
    {
        Former::populate([
            'maxbgpprefix'              => $r->old( 'maxbgpprefix', $vi->customer->maxprefixes ),
        ]);

        return view( 'interfaces/vlan/edit' )->with([
            'vlans'                     => Vlan::orderBy('number')->get(),
            'vli'                       => false,
            'vi'                        => $vi,
            'redirect2vi'               => $vi ? true : false,
            'duplicateTo'               => false
        ]);
    }

    /**
     * Create a vlan interface
     *
     * @param   StoreVlanInterface $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     */
    public function store( StoreVlanInterface $r ): RedirectResponse
    {
        $vli    = VlanInterface::make( $r->all() );
        $v      = Vlan::find( $r->vlanid );

        if( !$this->setIp( $r, $v, $vli, false ) || !$this->setIp( $r, $v, $vli, true ) ) {
            return Redirect::back()->withInput( $r->all() );
        }

        $vli->save();

        // add a warning if we're filtering on irrdb but have not configured one for the customer
        $this->warnIfIrrdbFilteringButNoIrrdbSourceSet( $vli );

        AlertContainer::push( 'Vlan Interface created.', Alert::SUCCESS );
        return redirect( route( 'virtual-interface@edit', [ 'vi' => $r->virtualinterfaceid ] ) );
    }

    /**
     * Display the form to edit a VLAN interface
     *
     * @param Request                   $r
     * @param VlanInterface             $vli    The VLAN interface
     * @param VirtualInterface|null     $vi     The virtual interface to add this VLI to
     * @param Vlan|null                 $duplicateTo The ID of the vlan Interface that will receive the data of the the other vli ( $id )
     *
     * @return View
     */
    public function edit( Request $r,  VlanInterface $vli, VirtualInterface $vi = null, Vlan $duplicateTo = null ): View
    {
        Former::populate([
            'vlanid'                    => $r->old( 'vlanid',           $duplicateTo->id ?? $vli->vlanid ),
            'irrdbfilter'               => $r->old( 'irrdbfilter',              $vli->irrdbfilter               ),
            'mcastenabled'              => $r->old( 'mcastenabled',             $vli->mcastenabled              ),

            'ipv4enabled'               => $r->old( 'ipv4enabled',              $vli->ipv4enabled               ),
            'ipv4address'               => $r->old( 'ipv4address',              $vli->ipv4addressid             ),
            'ipv4hostname'              => $r->old( 'ipv4hostname',             $vli->ipv4hostname              ),
            'ipv4bgpmd5secret'          => $r->old( 'ipv4bgpmd5secret',         $vli->ipv4bgpmd5secret          ),
            'ipv4canping'               => $r->old( 'ipv4canping',              $vli->ipv4canping               ),
            'ipv4monitorrcbgp'          => $r->old( 'ipv4monitorrcbgp',         $vli->ipv4monitorrcbgp          ),

            'maxbgpprefix'              => $r->old( 'maxbgpprefix',             $vli->maxbgpprefix              ),
            'rsclient'                  => $r->old( 'rsclient',                 $vli->rsclient                  ),
            'rsmorespecifics'           => $r->old( 'rsmorespecifics',          $vli->rsmorespecifics           ),
            'as112client'               => $r->old( 'as112client',              $vli->as112client               ),
            'busyhost'                  => $r->old( 'busyhost',                 $vli->busyhost                  ),

            'ipv6enabled'               => $r->old( 'ipv6enabled',              $vli->ipv6enabled               ),
            'ipv6address'               => $r->old( 'ipv6address',              $vli->ipv6addressid             ),
            'ipv6hostname'              => $r->old( 'ipv6hostname',             $vli->ipv6hostname              ),
            'ipv6bgpmd5secret'          => $r->old( 'ipv6bgpmd5secret',         $vli->ipv6bgpmd5secret          ),
            'ipv6canping'               => $r->old( 'ipv6canping',              $vli->ipv6canping               ),
            'ipv6monitorrcbgp'          => $r->old( 'ipv6monitorrcbgp',         $vli->ipv6monitorrcbgp          ),
        ]);

        $redirect2vi = (bool) $vi;
        if( !$vi ){
            $vi = $vli->virtualInterface;
        }

        return view( 'interfaces/vlan/edit' )->with([
            'vlans'                     => Vlan::  orderBy('number')->get(),
            'vli'                       => $vli,
            'vi'                        => $vi ?: false,
            'duplicateTo'               => $duplicateTo ?: false,
            'redirect2vi'               => $redirect2vi
        ]);
    }

    /**
     * Update a vlan interface
     *
     * @param   StoreVlanInterface  $r      instance of the current HTTP request
     * @param   VlanInterface       $vli
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function update( StoreVlanInterface $r, VlanInterface $vli ): RedirectResponse
    {
        $v = Vlan::find( $r->vlanid );

        if( !$this->setIp( $r, $v, $vli, false ) || !$this->setIp( $r, $v, $vli, true ) ) {
            return Redirect::back()->withInput( $r->all() );
        }

        $vli->update( $r->all() );

        // add a warning if we're filtering on irrdb but have not configured one for the customer
        $this->warnIfIrrdbFilteringButNoIrrdbSourceSet( $vli );

        AlertContainer::push( 'Vlan Interface updated.', Alert::SUCCESS );

        return redirect( $r->redirect2vi ? route( 'virtual-interface@edit', [ 'vi' => $vli->virtualinterfaceid ] ) : route( 'vlan-interface@list' ) );
    }

    /**
     * Duplicate a vlan interface
     *
     * @param   StoreVlanInterface  $r      instance of the current HTTP request
     * @param   VlanInterface       $vli
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function duplicate( StoreVlanInterface $r, VlanInterface $vli ): RedirectResponse
    {
        // are we duplicating?
        $source = $vli;
        $v      = Vlan::find( $r->vlanid );

        DB::beginTransaction();
        $vli    = VlanInterface::make();

        if( !$this->setIp( $r, $v, $vli, false ) || !$this->setIp( $r, $v, $vli, true ) ) {
            // Rollback if there is issue to avoid to insert the data created above
            DB::rollBack();
            return Redirect::back()->withInput( $r->all() );
        }

        $vli->fill( $r->all() );
        $vli->save();

        foreach( $source->layer2addresses as $l2a ) {
            Layer2Address::create(
                [
                    'vlan_interface_id' => $vli->id,
                    'mac'               => $l2a->mac
                ]
            );
        }

        DB::commit();

        // add a warning if we're filtering on irrdb but have not configured one for the customer
        $this->warnIfIrrdbFilteringButNoIrrdbSourceSet( $vli );

        AlertContainer::push( 'Vlan Interface duplicated.', Alert::SUCCESS );

        return redirect( route( 'virtual-interface@edit', [ 'vi' => $vli->virtualinterfaceid ] ) );
    }

    /**
     * Display a VLAN interface
     *
     * @param VlanInterface $vli
     *
     * @return  View
     */
    public function view( VlanInterface $vli ): View
    {
        return view( 'interfaces/vlan/view' )->with([
            'vli' => $vli
        ]);
    }

    /**
     * Delete a Vlan Interface and the Layer2Address associated
     *
     * @param  VlanInterface  $vli
     *
     * @return  RedirectResponse
     *
     * @throws Exception
     */
    public function delete( VlanInterface $vli ): RedirectResponse
    {
        $vli->layer2addresses()->delete();
        $vli->delete();

        AlertContainer::push( 'The Vlan Interface deleted', Alert::SUCCESS );

        if( $_SERVER[ "HTTP_REFERER" ] === route( 'vlan-interface@list' ) ){
            return redirect( route( 'vlan-interface@list' ) );
        }
        return redirect( route( 'virtual-interface@edit' , [ 'vi' => $vli->virtualinterfaceid ] ) );
    }
}