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

use Former;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StorePatchPanel;

use Log;


/**
 * PatchPanel Controller
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelController extends Controller
{

    /**
     * Display the patch panel list
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  boolean $active display active or inactive patch panels
     * @return  view
     */
    public function index( $active = true ): View {
        return view('patch-panel/index')->with([
            'patchPanels'       => D2EM::getRepository( PatchPanel::class )->findBy( [ 'active' => $active ] ),
            'active'            => $active
        ]);
    }

    /**
     * Allow to display the form to create/edit a patch panel
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  int $id EDIT => the ID of the patch panel that we need to modify, CREATE => we don't need it = null
     * @return  View|Redirect
     */
    public function edit( int $id = null ): View {

        $patchPanel = false;

        if( $id != null ) {
            if( !( $patchPanel = D2EM::getRepository( PatchPanel::class )->find($id) ) ) {
                abort(404);
            }

            Former::populate([
                'name'               => $patchPanel->getName(),
                'colo_reference'     => $patchPanel->getColoReference(),
                'cabinet'            => $patchPanel->getCabinet()->getId(),
                'cable_type'         => $patchPanel->getCableType(),
                'connector_type'     => $patchPanel->getConnectorType(),
                'installation_date'  => $patchPanel->getInstallationDateFormated(),
                'port_prefix'        => $patchPanel->getPortPrefix(),
                'numberOfPorts'      => 0,
            ]);
        }

        Former::open()->rules([
            'name'              => 'required|max:255',
            'colo_reference'    => 'required|max:255',
            'numberOfPorts'     => 'required|between:0,*|integer',
            'port_prefix'       => 'max:255',
            'installation_date' => 'date'
        ]);

        return view( 'patch-panel/edit' )->with([
            'patchPanel'        => $patchPanel,
            'cabinets'          => D2EM::getRepository( Cabinet::class )->getAsArray(),
        ]);
    }

    /**
     * Allow to create/edit a patch panel
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @return  redirect
     */
    public function store( StorePatchPanel $request ) {

        if( $request->input( 'id', false ) ) {
            // get the existing patch panel object for that ID
            if( !( $patchPanel = D2EM::getRepository( PatchPanel::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown patch panel when editing patch panel' );
                abort(404);
            }
        } else {
            $patchPanel = new PatchPanel();
        }

        if( !( $cabinet = D2EM::getRepository( Cabinet::class )->find( $request->input( 'cabinet' ) ) ) ) {
            Log::notice( 'Unknown cabinet when adding patch panel' );
            abort(404);
        }

        // set the data to the object
        $patchPanel->setName( $request->input( 'name' ) );
        $patchPanel->setCabinet( $cabinet );
        $patchPanel->setConnectorType( $request->input( 'connector_type' ) );
        $patchPanel->setCableType( $request->input( 'cable_type' ) );
        $patchPanel->setColoReference( $request->input( 'colo_reference' ) );
        $patchPanel->setActive( true );
        $patchPanel->setInstallationDate(
            ( $request->input( 'installation_date', false ) ? new \DateTime : new \DateTime( $request->input( 'installation_date' ) ) )
        );

        D2EM::persist( $patchPanel );

        // create the patch panel ports
        $patchPanel->createPorts( $request->input( 'numberOfPorts' ) );

        D2EM::flush();

        return Redirect::to('patch-panel-port/list/patch-panel/'.$patchPanel->getId());
    }

    /**
     * Allow to delete a patch panel
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @return  redirect
     */
    public function delete($id = null){
        $error = array('type' => '', 'message' => '');
        if($id != null){
            // create a new patch panel object
            $patchPanel = D2EM::getRepository(PatchPanel::class)->find($id);

            if($patchPanel->checkBeforeDelete()){
                $patchPanel->setActive(false);
                D2EM::persist($patchPanel);
                D2EM::flush($patchPanel);
                $error['type'] = 'success';
                $error['message'] = 'The patch panel has been removed.';
            }
            else{
                $error['type'] = 'fail';
                $error['message'] = 'Impossible to delete this patch panel some of the ports are still active.';
            }
        }
        else{
            $error['type'] = 'fail';
            $error['message'] = 'Impossible to delete';
        }
        return Redirect::to('patch-panel/list/')->with($error['type'], $error['message']);
    }

    /**
     * Display the patch panel informations
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  int $id ID of the patch panel
     * @return  view
     */
    public function view(int $id = null) {
        // Get all cable types
        $listCableTypes = \Entities\PatchPanel::$CABLE_TYPES;
        // Get all connector types
        $listConnectorTypes = \Entities\PatchPanel::$CONNECTOR_TYPES;
        // array of params for the view

        if($id != null){
            $patchPanel = D2EM::getRepository(PatchPanel::class)->find($id);
        }
        else{
            return Redirect::to('patch-panel/list');
        }

        $params = array('listCabinets'          => D2EM::getRepository(Cabinet::class)->getForArray(),
                        'listCableTypes'        => $listCableTypes,
                        'listConnectorTypes'    => $listConnectorTypes,
                        'patchPanel'            => $patchPanel
        );

        return view('patch-panel/view')->with('params', $params);
    }
}
