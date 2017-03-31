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


namespace IXP\Http\Controllers\PatchPanel;

use D2EM;

use Entities\Cabinet;
use Entities\PatchPanel;

use Entities\PatchPanelPort;
use Former;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StorePatchPanel;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

use Log;

use Redirect;


/**
 * PatchPanel Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 *
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelController extends Controller
{

    /**
     * Display the patch panel list
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @param  bool $active display active or inactive patch panels
     * @return  view
     */
    public function index( bool $active = true ): View {
        return view( 'patch-panel/index' )->with([
            'patchPanels'       => D2EM::getRepository( PatchPanel::class )->findBy( [ 'active' => $active ] ),
            'locations'         => D2EM::getRepository( Cabinet::class )->getByLocationAsArray(),
            'active'            => $active
        ]);
    }

    /**
     * @inheritdoc index()
     */
    public function indexInactive(): View {
        return $this->index( false );
    }

    /**
     * Allow to display the form to create/edit a patch panel
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  int $id EDIT => the ID of the patch panel that we need to modify, CREATE => we don't need it = null
     * @return  View|Redirect
     */
    public function edit( int $id = null ): View {
        /** @var PatchPanel $pp */
        $pp = false;

        if( $id != null ) {
            if( !( $pp = D2EM::getRepository( PatchPanel::class )->find( $id) ) ) {
                abort(404);
            }

            Former::populate([
                'name'               => $pp->getName(),
                'colo_reference'     => $pp->getColoReference(),
                'location_notes'     => $pp->getLocationNotes(),
                'cabinet'            => $pp->getCabinet()->getId(),
                'mounted_at'         => $pp->getMountedAt(),
                'u_position'         => $pp->getUPosition(),
                'cable_type'         => $pp->getCableType(),
                'connector_type'     => $pp->getConnectorType(),
                'installation_date'  => $pp->getInstallationDateFormated(),
                'port_prefix'        => $pp->getPortPrefix(),
                'numberOfPorts'      => 0,
            ]);
        }

        Former::open()->rules([
            'name'                  => 'required|max:255',
            'colo_reference'        => 'required|max:255',
            'numberOfPorts'         => 'required|between:0,*|integer',
            'port_prefix'           => 'nullable|string|max:255',
            'installation_date'     => 'date'

        ]);

        return view( 'patch-panel/edit' )->with([
            'pp'                    => $pp,
            'cabinets'              => D2EM::getRepository( Cabinet::class )->getAsArray(),
        ]);
    }

    /**
     * Allow to create/edit a patch panel
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $request instance of the current HTTP request
     * @return  redirect
     */
    public function store( StorePatchPanel $request ) {
        if( $request->input( 'id', false ) ) {
            // get the existing patch panel object for that ID
            if( !( $pp = D2EM::getRepository( PatchPanel::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown patch panel when editing patch panel' );
                abort(404);
            }
        } else {
            $pp = new PatchPanel();
        }

        if( !( $cabinet = D2EM::getRepository( Cabinet::class )->find( $request->input( 'cabinet' ) ) ) ) {
            Log::notice( 'Unknown cabinet when adding patch panel' );
            abort(404);
        }

        /** @var PatchPanel $pp  */
        // set the data to the object
        $pp->setName( $request->input( 'name' ) );
        $pp->setCabinet( $cabinet );
        $pp->setConnectorType( $request->input( 'connector_type' ) );
        $pp->setCableType( $request->input( 'cable_type' ) );
        $pp->setColoReference( $request->input( 'colo_reference' ) );
        $pp->setLocationNotes( clean( $request->input( 'location_notes' ) ?? '' ) );
        $pp->setChargeable( $request->input( 'chargeable' ) );
        $pp->setActive( true );
        $pp->setInstallationDate(
            ( $request->input( 'installation_date', false ) ? new \DateTime( $request->input( 'installation_date' ) ) : new \DateTime ) 
        );
        $pp->setPortPrefix( $request->input( 'port_prefix' ) ?? '' );

        if( ( $u = $request->input( 'u_position' ) ) && is_numeric($u) ) {
            $pp->setUPosition( (int)$u );
        }

        if( ( $mp = $request->input( 'mounted_at' ) ) && isset( PatchPanel::$MOUNTED_AT[$mp] ) ) {
            $pp->setMountedAt( (int)$mp );
        }

        D2EM::persist( $pp );

        // create the patch panel ports
        $pp->createPorts( $request->input( 'numberOfPorts' ) );

        D2EM::flush();

        return Redirect::to( 'patch-panel-port/list/patch-panel/'.$pp->getId() );
    }

    /**
     * change the status to active or inactive for a patch panel
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @param int $id
     * @param bool $active
     * @return RedirectResponse
     */
    public function changeStatus( int $id, int $active ): RedirectResponse {
        if( !( $pp = D2EM::getRepository( PatchPanel::class )->find( $id ) ) ) {
            abort(404);
        }

        $error  = array( 'type' => '', 'message' => '' );
        $status = $active ? 'active.' : 'inactive.';

        if( $pp->areAllPortsAvailable() ) {
            $pp->setActive( ( bool )$active );
            D2EM::persist( $pp );
            D2EM::flush();

            AlertContainer::push( 'The patch panel has been marked as '.$status, Alert::SUCCESS );
        } else {
            AlertContainer::push( 'To make a patch panel '.$status.', all ports must be available for use.', Alert::WARNING );
        }

        return redirect( 'patch-panel/list' )->with( [ 'error' => $error ] );
    }

    /**
     * Display the patch panel informations
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  int $id ID of the patch panel
     * @return  view
     */
    public function view( int $id = null ): View {
        $pp = false;

        if( !( $pp = D2EM::getRepository( PatchPanel::class )->find( $id ) ) ){
            abort(404);
        }

        return view( 'patch-panel/view' )->with([
            'pp'                        => $pp
        ]);
    }
}