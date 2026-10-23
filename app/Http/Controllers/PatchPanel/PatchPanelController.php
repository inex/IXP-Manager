<?php

namespace IXP\Http\Controllers\PatchPanel;

/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use IXP\Http\Requests\TogglePatchPanelActive;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Models\{Cabinet,
    Location,
    PatchPanel,
    PatchPanelPort,
    PatchPanelPortFile,
    PatchPanelPortHistory,
    PatchPanelPortHistoryFile};

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
 * @copyright  Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee
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
        Former::populate( [
            'active'             => 1,
            'initial_port_state' => PatchPanelPort::STATE_AVAILABLE,
        ] );

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
                'port_prefix' => $r->port_prefix ?? '',
            ])->all()
        );

        // Create and initialise the patch panel ports.
        $pp->createPorts( $r->numberOfPorts );
        $pp->setUnusedPortsState( (int)$r->input( 'initial_port_state', PatchPanelPort::STATE_AVAILABLE ) );

        if( $r->boolean( 'create_ports_as_duplex' ) ) {
            $pp->pairUnusedPortsAsDuplex();
        }
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
            'cabinet_id'                => $r->old( 'cabinet_id',          (string)$pp->cabinet_id          ),
            'name'                      => $r->old( 'name',                $pp->name                ),
            'active'                    => $r->old( 'active',              $pp->active              ),
            'colo_reference'            => $r->old( 'colo_reference',      $pp->colo_reference      ),
            'cable_type'                => $r->old( 'cable_type',          (string)$pp->cable_type          ),
            'connector_type'            => $r->old( 'connector_type',      (string)$pp->connector_type      ),
            'installation_date'         => $r->old( 'installation_date',   $pp->installation_date   ),
            'port_prefix'               => $r->old( 'port_prefix',         $pp->port_prefix         ),
            'location_notes'            => $r->old( 'location_notes',      $pp->location_notes      ),
            'u_position'                => $r->old( 'u_position',          (string)$pp->u_position          ),
            'colo_pp_type'              => $r->old( 'colo_pp_type',        (string)$pp->colo_pp_type        ),
            'mounted_at'                => $r->old( 'mounted_at',          (string)$pp->mounted_at          ),
            'numberOfPorts'             => $r->old( 'numberOfPorts','0'                         ),
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
        // Prevent modification of active status if there are ports in use
        if ($pp->active != $r->active && $pp->patchPanelPorts()->count() !== $pp->availableForUsePortCount()) {
            $status = $r->active ? 'active' : 'inactive';
            AlertContainer::push( 'To make a patch panel ' . $status . ', all ports must be available for use.', Alert::DANGER );
            return redirect()->back()->withInput();
        }

        $r->merge( [ 'location_notes' => clean( $r->location_notes ) ]);
        $pp->update( $r->all() );

        // create the patch panel ports
        $pp->createPorts( $r->numberOfPorts );
        return redirect( route( 'patch-panel-port@list-for-patch-panel', [ "pp" => $pp->id ] )  );
    }

    /**
     * Change the status to active or inactive for a patch panel
     *
     * @param PatchPanel              $pp
     * @param TogglePatchPanelActive  $request
     *
     * @return RedirectResponse
     */
    public function changeStatus( PatchPanel $pp, TogglePatchPanelActive $request ): RedirectResponse
    {
        $status = $request->active ? 'active' : 'inactive';

        if( $pp->patchPanelPorts()->count() === $pp->availableForUsePortCount() ) {
            $pp->update( [ 'active' => (bool)$request->active ] );
            AlertContainer::push( 'The patch panel has been marked as ' . $status, Alert::SUCCESS );
        } else {
            AlertContainer::push( 'To make a patch panel ' . $status . ', all ports must be available for use.', Alert::DANGER );
        }

        return redirect( route( "patch-panel@list" ) );
    }

    /**
     * Set a scoped group of unused ports to an allowed state.
     *
     * @param PatchPanel $pp
     * @param string     $scope
     * @param int        $state
     *
     * @return RedirectResponse
     */
    public function setUnusedPortsState( PatchPanel $pp, string $scope, int $state ): RedirectResponse
    {
        if( !array_key_exists( $state, PatchPanelPort::$INITIAL_PORT_STATES ) ) {
            AlertContainer::push( 'The port state is invalid.', Alert::DANGER );
            return redirect()->back();
        }

        switch( $scope ) {
            case 'all':
                $includeStates = [];
                $excludeStates = [ PatchPanelPort::STATE_BROKEN ];
                $description   = 'unused';
                break;

            case 'reserved':
                $includeStates = [ PatchPanelPort::STATE_RESERVED ];
                $excludeStates = [];
                $description   = 'unused reserved';
                break;

            case 'unreserved':
                $includeStates = [];
                $excludeStates = [ PatchPanelPort::STATE_RESERVED, PatchPanelPort::STATE_BROKEN ];
                $description   = 'unused unreserved';
                break;

            default:
                AlertContainer::push( 'The unused port scope is invalid.', Alert::DANGER );
                return redirect()->back();
        }

        $updated = $pp->setUnusedPortsState( $state, $includeStates, $excludeStates );
        AlertContainer::push( $updated . ' ' . $description . ' port(s) set to ' . PatchPanelPort::$STATES[ $state ] . '.', Alert::SUCCESS );

        return redirect()->back();
    }

    /**
     * Set all unused ports as duplex pairs or simplex ports.
     *
     * @param PatchPanel $pp
     * @param string     $mode
     *
     * @return RedirectResponse
     */
    public function setUnusedPortsDuplex( PatchPanel $pp, string $mode ): RedirectResponse
    {
        if( $mode === 'duplex' ) {
            $updated = $pp->pairUnusedPortsAsDuplex();
            $message = $updated . ' unused port pair(s) set as duplex.';
        } else if( $mode === 'simplex' ) {
            $updated = $pp->setUnusedPortsAsSimplex();
            $message = $updated . ' unused port pair(s) set as simplex.';
        } else {
            AlertContainer::push( 'The duplex mode is invalid.', Alert::DANGER );
            return redirect()->back();
        }

        AlertContainer::push( $message, Alert::SUCCESS );
        return redirect()->back();
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



    /**
     * Show a form confirming expunging of a patch panel
     *
     * @param PatchPanel    $pp      the patch panel
     *
     * @return  RedirectResponse|View
     */
    public function expunge( PatchPanel $pp ): RedirectResponse|View
    {
        if ($pp->active) {
            AlertContainer::push("{$pp->name} is active and cannot be expunged.", Alert::DANGER);
            return redirect()->route( 'patch-panel@list', [ 'pp' => $pp ]);
        }

        $numPorts = $pp->patchPanelPorts()->count();

        $numFiles = PatchPanelPortFile::whereHas('patchPanelPort', function ( Builder $query) use ($pp) {
            $query->wherePatchPanelId( $pp->id );
        })->count();

        $numHistories = PatchPanelPortHistory::whereHas('patchPanelPort', function (Builder $query) use ($pp) {
            $query->wherePatchPanelId( $pp->id );
        })->count();

        $numHistoryFiles = PatchPanelPortHistoryFile::whereHas('patchPanelPortHistory.patchPanelPort', function (Builder $query) use ($pp) {
            $query->wherePatchPanelId( $pp->id );
        })->count();

        return view( 'patch-panel/expunge' )->with([
            'pp'            => $pp,
            'numPorts'      => $numPorts,
            'numFiles'      => $numFiles,
            'numHistories'  => $numHistories,
            'numHistoryFiles' => $numHistoryFiles,
        ]);
    }



    /**
     * Accept submission to expunge a patch panel, and expunge it
     *
     * @param PatchPanel    $pp      the patch panel
     *
     * @return RedirectResponse
     */
    public function doExpunge( PatchPanel $pp ): RedirectResponse
    {
        if ($pp->active) {
            AlertContainer::push("{$pp->name} is active and cannot be expunged.", Alert::DANGER);
            return redirect()->route( 'patch-panel@list', [ 'pp' => $pp ]);
        }

        $results = DB::transaction( function () use ($pp) {
            // reset foreign keys before we go deleting
            PatchPanelPortHistory::whereIn( 'patch_panel_port_id', $pp->patchPanelPorts->pluck('id') )
                ->update( [ 'duplex_master_id' => null ] );

            PatchPanelPort::where( 'patch_panel_id', $pp->id )
                ->update( [ 'duplex_master_id' => null ] );

            // fetch file names to delete after transaction commits
            $results = (object) [ 'filesToDelete' => [] ];
            PatchPanelPortHistoryFile::whereHas('patchPanelPortHistory.patchPanelPort', function ($query) use ($pp) {
                $query->wherePatchPanelId( $pp->id );
            })->get()->map(function ($file) use ($results) {
                $results->filesToDelete[] = $file->path();
            });

            PatchPanelPortFile::whereHas('patchPanelPort', function ($query) use ($pp) {
                $query->wherePatchPanelId( $pp->id );
            })->get()->map(function($file) use ($results) {
                $results->filesToDelete[] = $file->path();
            });

            // Collect IDs before unlinking if needed, or query them directly for bulk deletion
            $results->numHistoryFiles = PatchPanelPortHistoryFile::whereHas( 'patchPanelPortHistory.patchPanelPort', fn( $q ) => $q->wherePatchPanelId( $pp->id ) )->delete();
            $results->numPortFiles = PatchPanelPortFile::whereHas( 'patchPanelPort', fn( $q ) => $q->wherePatchPanelId( $pp->id ) )->delete();
            $results->numPortHistories = PatchPanelPortHistory::whereHas( 'patchPanelPort', fn( $q ) => $q->wherePatchPanelId( $pp->id ) )->delete();
            $results->numPorts = PatchPanelPort::wherePatchPanelId( $pp->id )->delete();
            $pp->delete();

            return $results;
        });

        Log::info( "Expunged patch panel {$pp->name} - with {$results->numPorts} ports, {$results->numPortFiles} port files, {$results->numPortHistories} port histories, and {$results->numHistoryFiles} port history files");

        $failedToDelete = [];
        foreach ($results->filesToDelete as $filePath) {
            try {
                Storage::delete( '/files/' . $filePath);
            } catch (\Throwable $e) {
                $failedToDelete[] = $filePath;
            }
        }

        if ($failedToDelete) {
            Log::error( "Error deleting " . count($failedToDelete) . " of " . count($results->filesToDelete) . " expunged patch panel files from filesystem (for panel {$pp->name})" , $failedToDelete);
        } else {
            Log::info( "Successfully deleted all expunged patch panel files from filesystem (for panel {$pp->name})" );
        }

        AlertContainer::push( 'The patch panel has been expunged.', Alert::SUCCESS );
        return redirect( route( "patch-panel@list-inactive" ) );
    }

}