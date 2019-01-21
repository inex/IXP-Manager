<?php

namespace IXP\Http\Controllers\PatchPanel;

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

use D2EM, Former, Log, Redirect;

use Entities\{
    Cabinet as CabinetEntity,
    PatchPanel as PatchPanelEntity
};

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StorePatchPanel;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

/**
 * PatchPanel Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 *
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelController extends Controller
{

    /**
     * Display the patch panel list
     *
     * @param  bool $active display active or inactive patch panels
     *
     * @return  view
     */
    public function index( bool $active = true ): View {
        return view( 'patch-panel/index' )->with([
            'patchPanels'       => D2EM::getRepository( PatchPanelEntity::class )->findBy( [ 'active' => $active ] ),
            'locations'         => D2EM::getRepository( CabinetEntity::class    )->getByLocationAsArray(),
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
     * @param   int $id EDIT => the ID of the patch panel that we need to modify, CREATE => we don't need it = null
     *
     * @return  View
     */
    public function edit( int $id = null ): View {
        /** @var PatchPanelEntity $pp */
        $pp = false;
        $old = request()->old();

        if( $id != null ) {
            if( !( $pp = D2EM::getRepository( PatchPanelEntity::class )->find( $id) ) ) {
                abort(404);
            }



            Former::populate([
                'name'                      => array_key_exists( 'name',                $old    ) ? $old['name']                : $pp->getName(),
                'colo_reference'            => array_key_exists( 'colo_reference',      $old    ) ? $old['colo_reference']      : $pp->getColoReference(),
                'cabinet'                   => array_key_exists( 'cabinet',             $old    ) ? $old['cabinet']             : $pp->getCabinet()->getId(),
                'mounted_at'                => array_key_exists( 'mounted_at',          $old    ) ? $old['mounted_at']          : $pp->getMountedAt(),
                'u_position'                => array_key_exists( 'u_position',          $old    ) ? $old['u_position']          : $pp->getUPosition(),
                'cable_type'                => array_key_exists( 'cable_type',          $old    ) ? $old['cable_type']          : $pp->getCableType(),
                'connector_type'            => array_key_exists( 'connector_type',      $old    ) ? $old['connector_type']      : $pp->getConnectorType(),
                'installation_date'         => array_key_exists( 'installation_date',   $old    ) ? $old['installation_date']   : $pp->getInstallationDate()->format('Y-m-d'),
                'port_prefix'               => array_key_exists( 'port_prefix',         $old    ) ? $old['port_prefix']         : $pp->getPortPrefix(),
                'numberOfPorts'             => array_key_exists( 'numberOfPorts',       $old    ) ? $old['numberOfPorts']       : 0,
            ]);
        }

        return view( 'patch-panel/edit' )->with([
            'pp'                            => $pp,
            'cabinets'                      => D2EM::getRepository( CabinetEntity::class )->getAsArray(),
            'location_notes'                => $id ? ( array_key_exists( 'location_notes',           $old ) ? $old['location_notes']           : $pp->getLocationNotes() ) : ( array_key_exists( 'location_notes',           $old ) ? $old['location_notes']           : "" )
        ]);
    }

    /**
     * Allow to create/edit a patch panel
     *
     * @param   StorePatchPanel $request instance of the current HTTP request
     *
     * @return  redirect
     *
     * @throws
     */
    public function store( StorePatchPanel $request ) {
        /** @var PatchPanelEntity $pp  */
        if( $request->input( 'id', false ) ) {
            // get the existing patch panel object for that ID
            if( !( $pp = D2EM::getRepository( PatchPanelEntity::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown patch panel when editing patch panel' );
                abort(404);
            }
        } else {
            $pp = new PatchPanelEntity;
            D2EM::persist( $pp );
        }

        $cabinet = D2EM::getRepository( CabinetEntity::class )->find( $request->input( 'cabinet' ) );


        // set the data to the object
        $pp->setName(           $request->input( 'name'             ) );
        $pp->setConnectorType(  $request->input( 'connector_type'   ) );
        $pp->setCableType(      $request->input( 'cable_type'       ) );
        $pp->setColoReference(  $request->input( 'colo_reference'   ) );
        $pp->setChargeable(     $request->input( 'chargeable'       ) );

        $pp->setLocationNotes(  clean( $request->input( 'location_notes' )  ?? '' ) );
        $pp->setPortPrefix(     $request->input( 'port_prefix' )                  ?? '' );

        $pp->setActive(         true );
        $pp->setCabinet(        $cabinet );

        $pp->setInstallationDate(
            ( $request->input( 'installation_date', false ) ? new \DateTime( $request->input( 'installation_date' ) ) : new \DateTime ) 
        );

        if( ( $u = $request->input( 'u_position' ) ) && is_numeric($u) ) {
            $pp->setUPosition( (int)$u );
        }

        if( ( $mp = $request->input( 'mounted_at' ) ) && isset( PatchPanelEntity::$MOUNTED_AT[$mp] ) ) {
            $pp->setMountedAt( (int)$mp );
        }

        // create the patch panel ports
        $pp->createPorts( $request->input( 'numberOfPorts' ) );

        D2EM::flush();

        return redirect( route( "patch-panel-port/list/patch-panel", [ "id" => $pp->getId() ] )  );
    }

    /**
     * Change the status to active or inactive for a patch panel
     *
     * @param int $id
     * @param int $active
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function changeStatus( int $id, int $active ): RedirectResponse {
        /** @var PatchPanelEntity $pp  */
        if( !( $pp = D2EM::getRepository( PatchPanelEntity::class )->find( $id ) ) ) {
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

        return redirect( route( "patch-panel/list" ) )->with( [ 'error' => $error ] );
    }

    /**
     * Display the patch panel informations
     *
     * @param   int $id ID of the patch panel
     *
     * @return  view
     */
    public function view( int $id = null ): View {
        if( !( $pp = D2EM::getRepository( PatchPanelEntity::class )->find( $id ) ) ){
            abort(404);
        }

        return view( 'patch-panel/view' )->with([
            'pp'                        => $pp
        ]);
    }
}