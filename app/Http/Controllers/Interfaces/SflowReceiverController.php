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
use Exception, Former;

use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Models\{
    SflowReceiver,
    VirtualInterface
};

use IXP\Http\Requests\StoreSflowReceiver;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * SflowReceiver Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Interfaces
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SflowReceiverController extends Common
{
    /**
     * Display all the SflowReceivers
     *
     * @return  View
     */
    public function list(): View
    {
        return view(  'interfaces/sflow-receiver/list' )->with([
            'listSr'       => SflowReceiver::all()
        ]);
    }

    /**
     * Display the form to create a sflow receiver
     *
     * @param VirtualInterface|null $vi ID of the Virtual Interface
     *
     * @return View
     */
    public function create( VirtualInterface $vi = null ): View
    {
        return view( 'interfaces/sflow-receiver/edit' )->with([
            'sflr'      => false,
            'vi'        => $vi
        ]);
    }

    /**
     * Create a SflowReceiver
     *
     * @param   StoreSflowReceiver $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     */
    public function store( StoreSflowReceiver $r ): RedirectResponse
    {
        $sflr = SflowReceiver::create( $r->all() );
        AlertContainer::push( 'Sflow receiver created.', Alert::SUCCESS );
        return redirect( route( 'virtual-interface@edit', [ 'vi' => $sflr->virtual_interface_id ] ) );
    }

    /**
     * Display the form to add/edit a sflow receiver
     *
     * @param Request                   $r
     * @param SflowReceiver             $sflr ID of the Sflow Receiver
     * @param VirtualInterface|null     $vi ID of the Virtual Interface
     *
     * @return View
     */
    public function edit( Request $r, SflowReceiver $sflr, VirtualInterface $vi = null ): View
    {
        Former::populate([
            'dst_ip'        => $r->old( 'dst_ip',      $sflr->dst_ip    ),
            'dst_port'      => $r->old( 'dst_port',    $sflr->dst_port  ),
        ]);

        return view( 'interfaces/sflow-receiver/edit' )->with([
            'sflr'  => $sflr,
            'vi'    => $vi ?: false,
        ]);
    }

    /**
     * Update a SflowReceiver
     *
     * @param   StoreSflowReceiver  $r      instance of the current HTTP request
     * @param   SflowReceiver       $sflr
     *
     * @return  RedirectResponse
     */
    public function update( StoreSflowReceiver $r, SflowReceiver $sflr ): RedirectResponse
    {
        $sflr->update( $r->all() );
        AlertContainer::push( 'Sflow receiver updated.', Alert::SUCCESS );
        return redirect( route( 'virtual-interface@edit', [ 'vi' => $sflr->virtualInterface->id ] ) );
    }

    /**
     * Delete a Sflow receiver
     *
     * @param  SflowReceiver  $sflr
     *
     * @return  RedirectResponse
     *
     * @throws Exception
     */
    public function delete( SflowReceiver $sflr ): RedirectResponse
    {
        $sflr->delete();
        AlertContainer::push( 'Sflow receiver deleted.', Alert::SUCCESS );

        if( $_SERVER[ "HTTP_REFERER" ] === route( "sflow-receiver@list" ) ){
            return redirect( route( "sflow-receiver@list" ) );
        }
        return redirect( route( "virtual-interface@edit" , [ "vi" => $sflr->virtualInterface->id ] ) );
    }
}