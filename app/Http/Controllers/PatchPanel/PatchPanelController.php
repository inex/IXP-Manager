<?php

namespace IXP\Http\Controllers\PatchPanel;

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

use Former;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{
    Cabinet,
    Location,
    PatchPanel
};

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\StorePatchPanel;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * PatchPanel Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
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
    public function index( bool $active = true ): View
    {
        return view( 'patch-panel/index' )->with([
            'patchPanels'       => PatchPanel::where( 'active', $active )
                ->with( 'cabinet', 'patchPanelPorts' )->get(),
            'locations'         => Location::select( [ 'id', 'name' ] )
                ->orderBy( 'name' )->get(),
            'cabinets'          => Cabinet::select( [ 'id', 'name', 'locationid' ] )
                ->orderBy( 'name' )->get()->toArray(),
            'active'            => $active
        ]);
    }

    /**
     * @return View
     */
    public function indexInactive(): View
    {
        return $this->index( false );
    }

    /**
     * Allow to display the form to create a patch panel
     *
     * @return  View
     */
    public function create(): View
    {
        return view( 'patch-panel/edit' )->with([
            'pp'            => false,
            'cabinets'      => Cabinet::selectRaw( "id, concat( name, ' [', colocation, ']') AS name" )
                ->orderBy( 'name' )->get(),
        ]);
    }

    /**
     * Allow to create a patch panel
     *
     * @param   StorePatchPanel $r instance of the current HTTP request
     *
     * @return RedirectResponse
     */
    public function store( StorePatchPanel $r ): RedirectResponse
    {
        $pp = PatchPanel::create( $r->merge( [
                'location_notes' => clean( $r->location_notes ),
                'active' => true,
                'port_prefix' => $r->port_prefix ?? '',
            ])->all()
        );

        // create the patch panel ports
        $pp->createPorts( $r->numberOfPorts );
        return redirect( route( 'patch-panel-port@list-for-patch-panel', [ "pp" => $pp->id ] )  );
    }

    /**
     * Allow to display the form to edit a patch panel
     *
     * @param Request       $r
     * @param PatchPanel    $pp      the patch panel
     *
     * @return  View
     */
    public function edit( Request $r, PatchPanel $pp ): View
    {
        Former::populate([
            'cabinet_id'                => $r->old( 'cabinet_id',          $pp->cabinet_id          ),
            'name'                      => $r->old( 'name',                $pp->name                ),
            'colo_reference'            => $r->old( 'colo_reference',      $pp->colo_reference      ),
            'cable_type'                => $r->old( 'cable_type',          $pp->cable_type          ),
            'connector_type'            => $r->old( 'connector_type',      $pp->connector_type      ),
            'installation_date'         => $r->old( 'installation_date',   $pp->installation_date   ),
            'port_prefix'               => $r->old( 'port_prefix',         $pp->port_prefix         ),
            'location_notes'            => $r->old( 'location_notes',      $pp->location_notes      ),
            'u_position'                => $r->old( 'u_position',          $pp->u_position          ),
            'mounted_at'                => $r->old( 'mounted_at',          $pp->mounted_at          ),
            'numberOfPorts'             => $r->old( 'numberOfPorts',0                         ),
        ]);

        return view( 'patch-panel/edit' )->with([
            'pp'            => $pp,
            'cabinets'      => Cabinet::selectRaw( "id, concat( name, ' [', colocation, ']') AS name" )
                ->orderBy( 'name' )->get(),
        ]);
    }

    /**
     * Allow to update a patch panel
     *
     * @param StorePatchPanel   $r
     * @param PatchPanel        $pp
     *
     * @return  RedirectResponse
     */
    public function update( StorePatchPanel $r, PatchPanel $pp): RedirectResponse
    {
        $r->merge( [ 'location_notes' => clean( $r->location_notes ) ]);
        $pp->update( $r->all() );

        // create the patch panel ports
        $pp->createPorts( $r->numberOfPorts );
        return redirect( route( 'patch-panel-port@list-for-patch-panel', [ "pp" => $pp->id ] )  );
    }

    /**
     * Change the status to active or inactive for a patch panel
     *
     * @param PatchPanel    $pp
     * @param int           $active
     *
     * @return RedirectResponse
     */
    public function changeStatus( PatchPanel $pp, int $active ): RedirectResponse
    {
        $status = $active ? 'active' : 'inactive';

        if( $pp->patchPanelPorts()->count() === $pp->availableForUsePortCount() ) {
            $pp->update( [ 'active' => (bool)$active ] );
            AlertContainer::push( 'The patch panel has been marked as ' . $status, Alert::SUCCESS );
        } else {
            AlertContainer::push( 'To make a patch panel ' . $status . ', all ports must be available for use.', Alert::DANGER );
        }

        return redirect( route( "patch-panel@list" ) );
    }

    /**
     * Display the patch panel details
     *
     * @param   PatchPanel $pp the patch panel
     *
     * @return  view
     */
    public function view( PatchPanel $pp ): View
    {
        return view( 'patch-panel/view' )->with([
            'pp'    => $pp->load([ 'cabinet', 'patchPanelPorts' ] )
        ]);
    }
}