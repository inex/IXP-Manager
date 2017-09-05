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
    Request,
    JsonResponse
};

use Entities\{
    SflowReceiver as SflowReceiverEntity,
    VirtualInterface as VirtualInterfaceEntity
};

use IXP\Http\Requests\StoreSflowReceiver;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * SflowReceiver Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SflowReceiverController extends Common
{
    /**
     * Display all the SflowReceivers
     *
     * @return  View
     */
    public function list(): View {
        return view(  'interfaces/sflow-receiver/list' )->with([
            'listSr'       => D2EM::getRepository( SflowReceiverEntity::class )->findAll( )
        ]);
    }

    /**
     * Display the form to add/edit a sflow receiver
     *
     * @return View
     */
    public function edit( int $id = null, int $viid = null ) {
        $sflr = false;
        /** @var SflowReceiverEntity $sflr */
        if( $id and !( $sflr = D2EM::getRepository( SflowReceiverEntity::class )->find( $id ) ) ) {
            abort(404);
        }

        $vi = false;
        /** @var VirtualInterfaceEntity $vi */
        if( $viid and !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $viid ) ) ) {
            AlertContainer::push( 'You need a containing virtual interface before you add a sflow receiver', Alert::DANGER );
            return Redirect::back();
        }

        if( $sflr ) {
            // fill the form with sflow receiver data
            Former::populate([
                'dst_ip'                      => $sflr->getDstIp() ,
                'dst_port'                    => $sflr->getDstPort() ,
            ]);
        }

        return view( 'interfaces/sflow-receiver/edit' )->with([
            'sflr'  => $sflr ? $sflr : false,
            'vi'    => $vi
        ]);
    }


    /**
     * Edit a SflowReceiver (set all the data needed)
     *
     * @param   StoreSflowReceiver $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function store( StoreSflowReceiver $request ): RedirectResponse {

        /** @var SflowReceiverEntity $sflr */
        if( $request->input( 'id', false ) ) {
            // get the existing sflow receiver object for the given ID
            if( !( $sflr = D2EM::getRepository( SflowReceiverEntity::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown sflow receiver when editing' );
                abort(404);
            }
        } else {
            $sflr = new SflowReceiverEntity;
            D2EM::persist( $sflr );
        }

        /** @var VirtualInterfaceEntity $vi */
        if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $request->input( 'viid' ) ) ) ){
            abort(404, 'Unknown virtual interface');
        }

        $sflr->setVirtualInterface( $vi );
        $sflr->setDstIp( $request->input( 'dst_ip' ) );
        $sflr->setDstPort( $request->input( 'dst_port' ) );

        D2EM::flush();

        AlertContainer::push( 'Sflow receiver added/updated successfully.', Alert::SUCCESS );

        return Redirect::to( route( 'interfaces/virtual/edit', [ 'id' => $sflr->getVirtualInterface()->getId() ] ) );
    }

    /**
     * Delete a Sflow receiver
     *
     * @param   int $id ID of the SflowReceiver
     * @return  JsonResponse
     */
    public function delete( int $id ): JsonResponse{
        /** @var SflowReceiverEntity $sflr */
        if( !( $sflr = D2EM::getRepository( SflowReceiverEntity::class )->find( $id ) ) ) {
            return abort( '404' );
        }

        D2EM::remove( $sflr );
        D2EM::flush();

        return response()->json( [ 'success' => true ] );
    }
}