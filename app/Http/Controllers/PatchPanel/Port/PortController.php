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

use Auth, Former, Redirect, Str;

use Illuminate\Http\{
    RedirectResponse,
    Request,
    JsonResponse
};

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\StorePatchPanelPort as StorePatchPanelPortRequest;

use IXP\Models\{
    Aggregators\PatchPanelPortAggregator,
    Cabinet,
    Customer,
    Location,
    PatchPanel,
    PatchPanelPort,
    Switcher,
    SwitchPort
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * PatchPanelPort Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\PatchPanel\Port
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PortController extends Controller
{
    /**
     * Display all the patch panel ports (optionally limited to a specific patch panel)
     *
     * @param   PatchPanel|null $pp Show all ports for a patch panel
     *
     * @return  View
     */
    public function index( PatchPanel $pp = null ): View
    {
        return view( 'patch-panel-port/index' )->with([
            'patchPanelPorts'   => PatchPanelPortAggregator::list( $pp->id ?? null ),
            'pp'                => $pp ?: false,
        ]);
    }

    /**
     * Display all the patch panel ports based on a search
     *
     * @param   Request $r
     *
     * @return  View
     */
    public function advancedIndex( Request $r ): View
    {
        $location           = is_numeric( $r->location  )       ? (int)$r->location  : 0;
        $cabinet            = is_numeric( $r->cabinet   )       ? (int) $r->cabinet  : 0;
        $cabletype          = is_numeric( $r->type      )       ? (int) $r->type     : 0;
        $availableForUse    = (bool) $r->available;

        $summary = "Filtered for: ";
        $summary .= $location ? Location::find( $location )->name : 'all locations';
        $summary .= ', ' . ( $cabinet ? Cabinet::find( $cabinet )->name : 'all cabinets' );
        $summary .= ', ' . ( $cabletype ? PatchPanel::$CABLE_TYPES[ $cabletype ] : 'all cable types' );
        $summary .= ( $availableForUse ? ', available for use.' : '.' );

        return view( 'patch-panel-port/index' )->with([
            'patchPanelPorts'               => PatchPanelPortAggregator::list(
                null, true, $location, $cabinet, $cabletype, $availableForUse
            ),
            'pp'                            => false,
            'summary'                       => $summary,
        ]);
    }

    /**
     * Display the form to edit allocate a patch panel port
     *
     * @param   PatchPanelPort $ppp patch panel port that need to be edited
     *
     * @return  View
     */
    public function editAllocate( PatchPanelPort $ppp ): View
    {
        return $this->edit( request(),  $ppp, 'allocate' );
    }

    /**
     * Display the form to edit prewired a patch panel port
     *
     * @param  PatchPanelPort $ppp patch panel port that need to be edited
     *
     * @return  View
     */
    public function editPrewired( PatchPanelPort $ppp ): View
    {
        return $this->edit( request(), $ppp, 'prewired' );
    }

    /**
     * Display the form to edit a patch panel port
     *
     * @param Request           $r
     * @param PatchPanelPort    $ppp        Patch panel port that need to be edited
     * @param string|null       $formType   Which type of form to show
     *
     * @return View
     */
    public function edit( Request $r, PatchPanelPort $ppp, string $formType = null ): View
    {
        // if this is a slave port in a duplex port, swap for the master:
        $ppp = $ppp->duplexMasterPort ?? $ppp;

        switch ( $formType )  {
            case 'allocate' :
                $allocating = true;
                $prewired   = false;
                $states     = PatchPanelPort::$ALLOCATED_STATES_TEXT;
                break;
            case 'prewired' :
                $allocating = false;
                $prewired   = true;
                $states     = [ PatchPanelPort::STATE_PREWIRED => PatchPanelPort::$STATES[ PatchPanelPort::STATE_PREWIRED ] ];
                break;
            default :
                $allocating = false;
                $prewired   = false;
                $states     = PatchPanelPort::$STATES;
                break;
        }

        // If we're allocating this port, set the chargable flag to the patch panel's default:
        $chargeable = $allocating && $ppp->stateAvailable() ? $ppp->patchPanel->chargeable : $ppp->isChargeable();

        if( $sp = $ppp->switchPort ) {
             $switchPorts = SwitchPort::selectRaw( 'sp.name, sp.type, sp.id' )
                ->from( 'switchport AS sp' )
                ->leftJoin( 'switch AS s', 's.id', 'sp.switchid' )
                ->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
                ->where( 's.id', $sp->switchid )
                ->whereNull( 'pi.id' )->orWhere( 'sp.id', $sp->id )
                ->orderBy( 'sp.id' )->get()->keyBy( 'id' )->toArray();
        }

        $duplexSlaveId = $ppp->duplexSlavePorts()->exists() ? $ppp->duplexSlavePorts()->first()->id : null;
        // fill the form with patch panel port data
        Former::populate( [
            'switch_port_id'            => $r->old( 'switch_port_id',          $ppp->switch_port_id     ),
            'patch_panel'               => $ppp->patchPanel->name,
            'customer_id'               => $r->old( 'customer_id',             $ppp->customer_id        ),
            'state'                     => $r->old( 'state',                   $ppp->state              ),
            'notes'                     => $r->old( 'notes',                   $ppp->notes              ),
            'assigned_at'               => $r->old( 'assigned_at',             $ppp->assigned_at        ),
            'connected_at'              => $r->old( 'connected_at',            $ppp->connected_at       ),
            'cease_requested_at'        => $r->old( 'cease_requested_at',     $ppp->cease_requested_at  ),
            'ceased_at'                 => $r->old( 'ceased_at',               $ppp->ceased_at          ),
            'last_state_change'         => $r->old( 'last_state_change',       $ppp->last_state_change  ),
            'chargeable'                => $r->old( 'chargeable',              $chargeable              ),
            'number'                    => $ppp->number,
            'colo_circuit_ref'          => $r->old( 'colo_circuit_ref',        $ppp->colo_circuit_ref   ),
            'ticket_ref'                => $r->old( 'ticket_ref',              $ppp->ticket_ref         ),
            'private_notes'             => $r->old( 'private_notes',           $ppp->private_notes      ),
            'owned_by'                  => $r->old( 'owned_by',                $ppp->owned_by           ),
            'description'               => $r->old( 'description',             $ppp->description        ),
            'colo_billing_ref'          => $r->old( 'colo_billing_ref',        $ppp->colo_billing_ref   ),
            'cabinet_name'              => $ppp->patchPanel->cabinet->name,
            'colocation_centre'         => $ppp->patchPanel->cabinet->location->name,
            'switch'                    => $r->old( 'switch',           $ppp->switchPort->switchid ?? null    ),
            'partner_port'              => $r->old( 'partner_port',            $duplexSlaveId                       ),
        ]);

        return view( 'patch-panel-port/edit' )->with([
            'states'                => $states,
            'customers'             => Customer::select( [ 'id', 'name' ] )->orderBy( 'name' )->get(),
            'switches'              => Switcher::select( [ 'switch.id', 'switch.name' ] )
                                        ->leftJoin( 'cabinet AS cab', 'cab.id', 'switch.cabinetid' )
                                        ->where( 'active', true )
                                        ->where( 'cab.locationid', $ppp->patchPanel->cabinet->locationid )
                                        ->orderBy( 'name' )->get()->keyBy( 'id' ),
            'switchPorts'           => $switchPorts ?? [],
            'ppp'                   => $ppp,
            'partnerPorts'          => PatchPanelPortAggregator::getAvailablePorts( $ppp->patch_panel_id, [ $ppp->id ], $duplexSlaveId ),
            'hasDuplex'             => (bool) $duplexSlaveId,
            'allocating'            => $allocating,
            'prewired'              => $prewired,
        ]);
    }

    /**
     * Update a patch panel port
     *
     * @param   StorePatchPanelPortRequest  $r instance of the current HTTP request
     * @param   PatchPanelPort              $ppp
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function update( StorePatchPanelPortRequest $r, PatchPanelPort $ppp ): RedirectResponse
    {
        if( $r->switch_port_id ) {
            $sp = SwitchPort::findOrFail( $r->switch_port_id );

            if( $sp->id !== $ppp->switch_port_id ){
                // check if the switch port is available
                if( PatchPanelPort::where( 'switch_port_id', $sp->id )->doesntExist() ){
                    $ppp->update( [ 'switch_port_id' => $sp->id ] );
                } else {
                    AlertContainer::push( 'The switch port selected is already used by an other patch panel port.', Alert::DANGER );
                    return redirect()->back()->withInput( $r->all() );
                }
            }

            if( $r->customer_id ) {
                // check if the switch port can be linked to the customer
                $cid = SwitchPort::select( 'vi.custid' )
                    ->from( 'switchport AS sp' )
                    ->leftJoin( 'physicalinterface AS pi', 'pi.switchportid', 'sp.id' )
                    ->leftJoin( 'virtualinterface AS vi', 'vi.id', 'pi.virtualinterfaceid' )
                    ->where( 'sp.id', $sp->id )->get()->pluck( 'custid' )->first();

                if( $cid !== null && $cid !== (int)$r->customer_id ) {
                    AlertContainer::push( 'The selected customer does not have a relationship with the switch port', Alert::DANGER );
                    return redirect()->back()->withInput( $r->all() );
                }
            }
        } else {
            if( $r->customer_id && $r->switch ) {
                AlertContainer::push( 'You need to select a switch port when a switch is selected', Alert::DANGER );
                return redirect()->back()->withInput( $r->all() );
            }
            $ppp->update( [ 'switch_port_id' => null ] );
        }

        if( $ppp->state !== (int)$r->state ) {
            $ppp->update( [
                'state' => $r->state,
                'last_state_change' => now()
            ] );
        }

        if( ( $r->customer_id && !$r->assigned_at ) ||  $r->allocated ) {
            $assigned_at = now();
        } else {
            $assigned_at = $r->assigned_at;
        }

        if( (int)$r->state === PatchPanelPort::STATE_CONNECTED && !$r->connected_at ) {
            $connected_at = now();
        } else {
            $connected_at = $r->connected_at;
        }

        if( (int)$r->state === PatchPanelPort::STATE_AWAITING_CEASE && !$r->cease_requested_at ) {
            $cease_requested_at = now();
        } else {
            $cease_requested_at = $r->cease_requested_at;
        }

        if( (int)$r->state === PatchPanelPort::STATE_CEASED && !$r->ceased_at ) {
            $ceased_at = now();
        } else {
            $ceased_at = $r->ceased_at;
        }

        $loa_code = $ppp->loa_code;
        if( $r->customer_id && !$ppp->loa_code ) {
            $loa_code = Str::random(25);
        } else if( !$r->customer_id ) {
            $loa_code = '';
        }

        $ppp->update([
            'customer_id'           => $r->customer_id,
            'notes'                 => clean( $r->notes ),
            'assigned_at'           => $assigned_at,
            'connected_at'          => $connected_at,
            'cease_requested_at'    => $cease_requested_at,
            'ceased_at'             => $ceased_at,
            'internal_use'          => (bool)$r->internal_use,
            'chargeable'            => $r->chargeable,
            'colo_circuit_ref'      => $r->colo_circuit_ref,
            'ticket_ref'            => $r->ticket_ref,
            'private_notes'         => clean( $r->private_notes ),
            'owned_by'              => $r->owned_by,
            'loa_code'              => $loa_code,
            'description'           => clean( $r->description ),
            'colo_billing_ref'      => $r->colo_billing_ref,
        ]);

        if( $r->duplex ) {
            $partnerPort = PatchPanelPort::find( $r->partner_port );

            if( $ppp->id !== $partnerPort->duplex_master_id ) {
                foreach( $ppp->duplexSlavePorts as $slave ) {
                    $slave->reset();
                }
                $partnerPort->update( [ 'duplex_master_id' => $ppp->id ] );
            }

        } else {
            // if ppp has a slave port and duplex port is uncheck => unlink the slave port and reset it
            foreach( $ppp->duplexSlavePorts as $slave ){
                $slave->reset();
            }
        }

        // create a history and reset the patch panel port
        if( $ppp->stateCeased() ) {
            $ppp->archive();
            $ppp->reset();
        }

        // set physical interface status if available
        if( $r->allocated && $r->pi_status && $r->pi_status > 0
            && $ppp->switchPort && $ppp->switchPort->physicalInterface ) {
            $ppp->switchPort->physicalInterface->update( [ 'status' => $r->pi_status ] );
        }

        return redirect( route( 'patch-panel-port@list-for-patch-panel', [ "pp" => $ppp->patch_panel_id ] ) );
    }

    /**
     * Display the patch panel port and the patch panel for history (if any).
     *
     * @param  PatchPanelPort $ppp the patch panel
     *
     * @return View
     */
    public function view( PatchPanelPort $ppp ): View
    {
        $listHistory[] = $ppp->load( [ 'patchPanel', 'duplexSlavePorts',
            'switchPort', 'customer'
        ] );

        if( !Auth::getUser()->isSuperUser() ) {
            if( !$ppp->customer || $ppp->customer_id !== Auth::getUser()->custid ) {
                abort(404);
            }
        } else {
            // only load history if we're a super user
            // get the patch panel port histories
            foreach( $ppp->patchPanelPortHistories as $history ){
                $listHistory[] = $history->load( [
                    'patchPanelPort.patchPanel'
                ] );
            }
        }

        return view( 'patch-panel-port/view' )->with([
            'ppp'                       => $ppp,
            'listHistory'               => $listHistory,
        ]);
    }

    /**
     * Set the public and private notes of a patch panel
     *
     * @param   Request         $r      instance of the current HTTP request
     * @param   PatchPanelPort  $ppp    The patch panel port to query
     *
     * @return  JsonResponse JSON customer object
     *
     * @throws
     */
    public function setNotes( Request $r, PatchPanelPort $ppp ) : JsonResponse
    {
        $ppp->update(
            [
                'notes'         => clean( $r->notes ),
                'private_notes' => clean( $r->private_notes ),
            ]
        );

        // we may also pass a new state for a physical interface with this request
        // (because we call this function from set connected / set ceased / etc)
        if( $r->pi_status && $ppp->switchPort && ( $pi = $ppp->switchPort->physicalInterface ) ) {
            $pi->update( [ 'status' => $r->pi_status ] );
        }

        if( $r->colo_circuit_ref ) {
            $ppp->update( [ 'colo_circuit_ref' =>  clean( $r->colo_circuit_ref ) ] );
        }

        return response()->json( [ 'success' => true ] );
    }

    /**
     * Change the status of a patch panel port and set the date value related to the status
     *
     * @param PatchPanelPort    $ppp
     * @param int               $status
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function changeStatus( PatchPanelPort $ppp, int $status ): RedirectResponse
    {
        if( !array_key_exists( $status, PatchPanelPort::$STATES ) ){
            AlertContainer::push( 'The status is invalid.', Alert::DANGER );
            return redirect::back();
        }

        switch ( $status ) {
            case PatchPanelPort::STATE_AVAILABLE:
                if( $ppp->statePrewired() ) {
                    // we get here via 'Unset Prewired'
                    if( $ppp->isDuplexPort() ) {
                        foreach( $ppp->duplexSlavePorts as $slave ){
                            $slave->update( [
                                'state' => PatchPanelPort::STATE_AVAILABLE,
                                'duplex_master_id' => null
                            ] );
                        }
                    }
                    $ppp->update( [ 'switch_port_id' => null ] );
                }
                $ppp->update( ['state' => PatchPanelPort::STATE_AVAILABLE ] );
                break;
            case PatchPanelPort::STATE_CONNECTED :
                $ppp->update( [
                    'state'         => PatchPanelPort::STATE_CONNECTED,
                    'connected_at'  => now(),
                ] );
                break;
            case PatchPanelPort::STATE_AWAITING_CEASE :
                $ppp->update( [
                    'state'                 => PatchPanelPort::STATE_AWAITING_CEASE,
                    'cease_requested_at'    => now(),
                ] );
                break;
            case PatchPanelPort::STATE_CEASED :
                $ppp->update( [
                    'state'                 => PatchPanelPort::STATE_CEASED,
                    'ceased_at'             => now(),
                ] );
                break;
            default:
                $ppp->update( [
                    'state'     => $status,
                ] );
                break;
        }

        $ppp->update( [
            'last_state_change'     => now(),
        ] );

        if( $status === PatchPanelPort::STATE_CEASED ) {
            if( $sp = $ppp->switchPort ) {
                AlertContainer::push( 'The patch panel port has been set to available again. Consider '
                      . 'setting it as  prewired if the cable is still in place. It was connected to '
                      . $sp->switcher->name . ' :: ' . $sp->name,
                  Alert::SUCCESS );
            }

            // create a history and reset the patch panel port
            if( $ppp->stateCeased() ) {
                $ppp->archive();
                $ppp->reset();
            }
        }

        AlertContainer::push( 'The patch panel port has been set to: ' . $ppp->states(), Alert::SUCCESS );
        return redirect::back();
    }
}