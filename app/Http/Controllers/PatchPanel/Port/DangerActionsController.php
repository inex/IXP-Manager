<?php

namespace IXP\Http\Controllers\PatchPanel\Port;

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

use Auth, Redirect;

use IXP\Exceptions\GeneralException;
use Illuminate\Http\{
    RedirectResponse,
};

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\{
    MovePatchPanelPort      as MovePatchPanelPortRequest
};

use IXP\Models\{
    PatchPanel,
    PatchPanelPort};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * DangerActions Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel\Port
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DangerActionsController extends Controller
{
    /**
     * Access to the form that allow to move the information of a port to an other port
     *
     * @param  PatchPanelPort    $ppp      The patch panel port
     *
     * @return  View
     */
    public function moveForm( PatchPanelPort $ppp ): View
    {
        return view( 'patch-panel-port/move' )->with([
            'ppp'               => $ppp,
            'ppAvailable'       => PatchPanel::select( [ 'pp.id', 'pp.name' ] )
                ->from( 'patch_panel AS pp' )
                ->leftJoin( 'cabinet AS cab', 'cab.id', 'pp.cabinet_id' )
                ->where( 'cab.locationid', $ppp->patchPanel->cabinet->locationid )
                ->where( 'pp.cable_type', $ppp->patchPanel->cable_type )
                ->orderBy( 'pp.id' )->get(),
        ]);
    }

    /**
     * Move a patch panel port information to an other
     *
     * @param  MovePatchPanelPortRequest  $r  instance of the current HTTP request
     * @param  PatchPanelPort  $ppp
     *
     * @return  RedirectResponse
     *
     * @throws GeneralException
     */
    public function move( MovePatchPanelPortRequest $r, PatchPanelPort $ppp ): RedirectResponse
    {
        $newPort  = PatchPanelPort::find( $r->port_id );

        if( $ppp->duplexSlavePorts()->exists() ){
            if( $newPort->duplexSlavePorts()->exists() ){
                $slave = $newPort->duplexSlavePorts->first();
            } else {
                $slave = PatchPanelPort::find( $r->slave_id );
                if( $slave->isDuplexPort() ){
                    AlertContainer::push( 'The slave port is port of a duplex port. The slave port has to be a single port.', Alert::DANGER );
                    return redirect( route( 'patch-panel-port@move-form', [ 'ppp' => $ppp->id ] ) );
                }
            }
        }

        if( $ppp->move( $newPort, $slave ?? null ) ) {
            AlertContainer::push( 'Patch panel port moved.', Alert::SUCCESS );
        } else {
            AlertContainer::push( 'Something went wrong!', Alert::DANGER );
        }

        return redirect( route('patch-panel-port@list-for-patch-panel' ,  [ 'pp' => $newPort->patch_panel_id ] ) );
    }

    /**
     * Remove the linked port from the master and reset it as available.
     *
     * @param  PatchPanelPort $ppp the patch panel **master** port from which to split the slave
     *
     * @return  RedirectResponse
     */
    public function split( PatchPanelPort $ppp ): RedirectResponse
    {
        if( !$ppp->duplexSlavePorts()->count() ) {
            AlertContainer::push( 'This patch panel port does not have any slave port.', Alert::DANGER );
            return redirect( route( 'patch-panel-port@list-for-patch-panel', [ 'pp' => $ppp->patch_panel_id ]  ) );
        }

        foreach( $ppp->duplexSlavePorts as $slave ){
            $ppp->update( [
                'duplex_master_id' => null,
                'private_notes' => "### " . now()->format('Y-m-d') . " - ". Auth::getUser()->username ."\n\nThis port had a slave port: "
                    . $slave->patchPanel->port_prefix . $slave->number . " which was split by " . Auth::getUser()->username
                    . " on " . now()->format('Y-m-d') . ".\n\n"
                    . $ppp->private_notes
            ]);

            $slave->reset();

            $slave->update( [
                'private_notes' => "### " . now()->format('Y-m-d') . " - ". Auth::getUser()->username ."\n\nThis port was a duplex slave port with "
                    . $ppp->patchPanel->port_prefix . $ppp->number . " and was split by " . Auth::getUser()->username
                    . " on " . now()->format('Y-m-d') . ".\n\n"
            ] );
        }

        AlertContainer::push( 'Patch Panel port splited.', Alert::SUCCESS );
        return redirect( route( 'patch-panel-port@list-for-patch-panel', [ 'pp' => $ppp->patch_panel_id ]  ) );
    }

    /**
     * Delete a patch panel port
     *
     * If the patch panel port has a duplex port then it will delete both ports.
     * Also deletes associated files and histories.
     *
     * @param  PatchPanelPort $ppp the patch panel port to delete
     *
     * @return  RedirectResponse
     */
    public function delete( PatchPanelPort $ppp ): RedirectResponse
    {
        $ppp->remove();
        AlertContainer::push( 'Patch Panel port deleted.', Alert::SUCCESS );
        return redirect( route( 'patch-panel-port@list-for-patch-panel', [ 'pp' => $ppp->patch_panel_id ]  ) );
    }
}