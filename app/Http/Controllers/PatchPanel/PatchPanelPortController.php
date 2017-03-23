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

use Auth;

use D2EM;

use Entities\Customer;
use Entities\PatchPanel;
use Entities\PatchPanelPort;
use Entities\PatchPanelPortFile;
use Entities\PatchPanelPortHistory;
use Entities\PhysicalInterface;
use Entities\Switcher;
use Entities\SwitchPort;

use Former\Facades\Former;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\EmailPatchPanelPort;
use IXP\Http\Requests\StorePatchPanelPort;
use IXP\Mail\PatchPanelPort\Email;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

use Mail;


/**
 * PatchPanelPort Controller
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */


class PatchPanelPortController extends Controller
{
    /**
     * Display all the patch panel ports
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @parama  int $id allow to display all the port for a patch panel => if null display all ports for all patch panel
     * @return  view
     */
    public function index( int $id = null ): View{
        $pp = false;
        if( $id != null ) {
            if ( !( $pp = D2EM::getRepository( PatchPanel::class )->find( $id ) ) ) {
                abort(404);
            }
        }

        return view( 'patch-panel-port/index' )->with([
            'patchPanelPorts'               => D2EM::getRepository( PatchPanelPort::class )->getAllPatchPanelPort( $id ),
            'pp'                            => $pp,
            'user'                          => Auth::user(),

            'physicalInterfaceStatesSubSet' => [
                PhysicalInterface::STATUS_QUARANTINE => PhysicalInterface::$STATES[PhysicalInterface::STATUS_QUARANTINE],
                PhysicalInterface::STATUS_CONNECTED => PhysicalInterface::$STATES[PhysicalInterface::STATUS_CONNECTED]
            ]
        ]);
    }

    /**
     * Display the form to edit a patch panel port
     *
     * @param  int $id patch panel port that need to be edited
     * @return  view
     */
    public function edit( int $id, $allocating = false ) {
        $ppp = false;

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find($id) ) ) {
            abort(404);
        }

        // display master port informations
        if( $ppp->getDuplexMasterPort() != null ){
            $ppp = $ppp->getDuplexMasterPort();
        }

        /** @var PatchPanelPort $ppp */

        $hasDuplex = $ppp->hasSlavePort();

        $chargeable = ($allocating and $ppp->isStateAvailable()) ? $ppp->getPatchPanel()->getChargeable() : $ppp->getChargeableDefaultNo();

        // fill the form with patch panel port data
        Former::populate([
            'number'                => $ppp->getNumber(),
            'patch_panel'           => $ppp->getPatchPanel()->getName(),
            'colo_circuit_ref'      => $ppp->getColoCircuitRef(),
            'ticket_ref'            => $ppp->getTicketRef(),
            'switch'                => $ppp->getSwitchId(),
            'switch_port'           => $ppp->getSwitchPortId(),
            'customer'              => $ppp->getCustomerId(),
            'partner_port'          => $ppp->getDuplexSlavePortId(),
            'state'                 => $ppp->getState(),
            'notes'                 => $ppp->getNotes(),
            'private_notes'         => $ppp->getPrivateNotes(),
            'assigned_at'           => $ppp->getAssignedAtFormated(),
            'connected_at'          => $ppp->getConnectedAtFormated(),
            'ceased_requested_at'   => $ppp->getCeaseRequestedAtFormated(),
            'ceased_at'             => $ppp->getCeasedAtFormated(),
            'last_state_change_at'  => $ppp->getLastStateChangeFormated(),
            'chargeable'            => $chargeable,
            'owned_by'              => $ppp->getOwnedBy()
        ]);

        // display the duplex port if set or the list of all duplex port available
        if( $hasDuplex ) {
            $partnerPorts = [ $ppp->getDuplexSlavePortId() => $ppp->getDuplexSlavePortName() ];
        } else {
            $partnerPorts = D2EM::getRepository( PatchPanelPort::class )->getPatchPanelPortAvailableForDuplex( $ppp->getPatchPanel()->getId(), $ppp->getId() );
        }

        return view( 'patch-panel-port/edit' )->with([
            'states'            => ( $allocating ) ? PatchPanelPort::$ALLOCATE_STATES : PatchPanelPort::$STATES,
            'piStatus'          => PhysicalInterface::$PPP_STATES,
            'customers'         => D2EM::getRepository( Customer::class )->getNames( true ),
            'switches'          => D2EM::getRepository( Switcher::class )->getNamesByLocation( true, Switcher::TYPE_SWITCH,$ppp->getPatchPanel()->getCabinet()->getLocation()->getId() ),
            'switchPorts'       => D2EM::getRepository( Switcher::class )->getAllPortForASwitch( $ppp->getSwitchId(),null, $ppp->getSwitchPortId() ),
            'chargeables'       => PatchPanelPort::$CHARGEABLES,
            'ownedBy'           => PatchPanelPort::$OWNED_BY,
            'ppp'               => $ppp,
            'partnerPorts'      => $partnerPorts,
            'hasDuplex'         => $hasDuplex,
            'user'              => Auth::user(),
            'allocating'        => ($allocating) ? true : false
        ]);
    }

    /**
     * Display the form to edit a patch panel port
     *
     * @param  int $id patch panel port that need to be edited
     * @return  view
     */
    public function editToAllocate( int $id ) {
        return $this->edit( $id, true );
    }

    /**
     * Add or edit a patch panel port (set all the data needed)
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $request instance of the current HTTP request
     * @return  redirect
     */
    public function store( StorePatchPanelPort $request ){
        if( $request->input( 'id', false ) ) {
            // get the existing patch panel object for that ID
            if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown patch panel port when editing patch panel' );
                abort(404);
            }
        } else {
            $ppp = new PatchPanelPort();
        }

        if( $request->input( 'switch_port' ) ){
            if( !( $sp = D2EM::getRepository( SwitchPort::class )->find( $request->input( 'switch_port' ) ) ) ) {
                Log::notice( 'Unknown switch port when adding patch panel' );
                abort(404);
            }

            if( $sp->getId() != $ppp->getSwitchPortId() ){
                // check if the switch port is available
                if( D2EM::getRepository( PatchPanelPort::class )->isSwitchPortAvailable( $sp->getId() ) ){
                    $ppp->setSwitchPort($sp);
                } else {
                    AlertContainer::push( 'The switch port selected is already used by an other patch panel Port !', Alert::DANGER );
                    return Redirect::to( 'patch-panel-port/edit/'.$request->input( 'id' ) )
                        ->withInput( Input::all() );
                }
            }

            if( $request->input( 'customer' ) ){
                // check if the switch port can be link to the customer
                $custId = D2EM::getRepository( SwitchPort::class )->getCustomerForASwitchPort( $sp->getId() );

                if( $custId != null ){
                    if( $custId != $request->input( 'customer' ) ){
                        AlertContainer::push( 'Customer not allowed for this switch port !', Alert::DANGER );
                        return Redirect::to( 'patch-panel-port/edit/'.$request->input( 'id' ) )
                            ->withInput( Input::all() );
                    }
                }
            }
        } else {
            if( $request->input('customer') and $request->input( 'switch' ) ){
                AlertContainer::push( 'You need to select a switch port !', Alert::DANGER );
                return Redirect::to( 'patch-panel-port/edit/'.$request->input( 'id' ) )
                    ->withInput( Input::all() );
            }
            $ppp->setSwitchPort( null );
        }

        if( $ppp->getState() != $request->input( 'state' ) ){
            $ppp->setState( $request->input( 'state' ) );
            $ppp->setLastStateChange( new \DateTime );
        }

        $ppp->setNotes( ( $request->input( 'notes' ) == '' ? null : $request->input( 'notes' ) ) );

        $ppp->setPrivateNotes( ( $request->input( 'private_notes' ) == '' ? null : $request->input( 'private_notes' ) )) ;

        $ppp->setColoCircuitRef( $request->input( 'colo_circuit_ref') );
        $ppp->setTicketRef( $request->input( 'ticket_ref' ) );

        $ppp->setCustomer( ( $request->input( 'customer' ) ) ? D2EM::getRepository( Customer::class )->find( $request->input( 'customer' ) ) : null );

        if( $request->input( 'customer' ) and $request->input( 'assigned_at' ) == ''){
            $ppp->setAssignedAt( new \DateTime );
        } else {
            if( $request->input( 'allocated' ) ){
                $ppp->setAssignedAt( new \DateTime );
            } else {
                $ppp->setAssignedAt( ( $request->input( 'assigned_at' ) == '' ? null : new \DateTime( $request->input( 'assigned_at' ) ) ) );
            }

        }

        if( $request->input( 'state' ) == PatchPanelPort::STATE_CONNECTED and $request->input( 'connected_at' ) == '' ) {
            $ppp->setConnectedAt( new \DateTime );
        } else {
            $ppp->setConnectedAt( ( $request->input( 'connected_at' ) == '' ? null : new \DateTime( $request->input( 'connected_at' ) ) ) );
        }

        if( $request->input( 'state' ) == PatchPanelPort::STATE_AWAITING_CEASE and $request->input( 'ceased_requested_at' ) == '' ) {
            $ppp->setCeaseRequestedAt( new \DateTime );
        } else {
            $ppp->setCeaseRequestedAt( ( $request->input( 'ceased_requested_at' ) == '' ? null : new \DateTime( $request->input( 'ceased_requested_at' ) ) ) );
        }

        if( $request->input( 'state' ) == PatchPanelPort::STATE_CEASED and $request->input( 'ceased_at' ) == '' ) {
            $ppp->setCeasedAt( new \DateTime );
        } else {
            $ppp->setCeasedAt( ( $request->input( 'ceased_at' ) == '' ? null : new \DateTime( $request->input( 'ceased_at' ) ) ) );
        }

        $ppp->setInternalUse( $request->input( 'internal_use' ) );
        $ppp->setChargeable($request->input( 'chargeable' ) );
        $ppp->setOwnedBy( $request->input( 'owned_by' ) );

        D2EM::persist( $ppp );

        if( $request->input( 'duplex' ) ) {
            if( $ppp->hasSlavePort() ) {
                $isNewSlavePort = false;
                $partnerPort = $ppp->getDuplexSlavePort();
            } else {
                $isNewSlavePort = true;
                $partnerPort = D2EM::getRepository( PatchPanelPort::class )->find( $request->input( 'partner_port' ) );
            }

            $duplexPort = $ppp->setDuplexPort( $partnerPort, $isNewSlavePort );

            if( $isNewSlavePort ) {
                $ppp->addDuplexSlavePort( $duplexPort );
            }
        }
        D2EM::flush();

        // create an history and reset the patch panel port
        if( $ppp->getState() == PatchPanelPort::STATE_CEASED ) {
            $ppp->createHistory();
        }

        // set physical interface status if available
        if( $request->input( 'allocated' ) ) {
            if( $request->input( 'pi_status' ) ) {
                $physicalInterface = $ppp->getSwitchPort()->getPhysicalInterface();
                switch ( $request->input( 'pi_status' ) ) {
                    case PhysicalInterface::STATUS_CONNECTED :
                        $piStatus = PhysicalInterface::STATUS_QUARANTINE;
                        break;
                    case PhysicalInterface::STATUS_XCONNECT :
                        $piStatus = PhysicalInterface::STATUS_XCONNECT;
                        break;
                }
                $physicalInterface->setStatus( $piStatus );
                D2EM::persist( $ppp );
                D2EM::flush();
            }
        }

        return Redirect::to( 'patch-panel-port/list/patch-panel/'.$ppp->getPatchPanel()->getId() );

    }

    /**
     * Display the patch panel port informations
     * and the patch panel for history the that patch panel port if exist
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  int $id ID of the patch panel
     * @return  view
     */
    public function view( int $id = null )
    {
        $ppp = false;

        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            abort(404);
        }

        if( !Auth::user()->isSuperUser() ) {
            if( $ppp->getCustomerId() != Auth::user()->getCustomer()->getId() ) {
                abort(404);
            }
        }

        $listHistory[] = $ppp;

        // get the patch panel port histories
        foreach ( $ppp->getPatchPanelPortHistoryMaster() as $history ){
            $listHistory[] = $history;
        }

        return view( 'patch-panel-port/view' )->with([
            'ppp'                       => $ppp,
            'listHistory'               => $listHistory,
            'isSuperUser'               => Auth::user()->isSuperUser()
        ]);
    }

    /**
     * change the status of a patch panel port and set the date value related to the status
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @param int $id
     * @param int $status
     * @return RedirectResponse
     */
    public function changeStatus( int $id, int $status ): RedirectResponse {
        $error = array( 'type' => '', 'message' => '' );
        $message = '';
        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            abort(404);
        }

        if( array_key_exists( $status, PatchPanelPort::$STATES ) ){
            switch ( $status ) {
                case PatchPanelPort::STATE_CONNECTED :
                    $ppp->setState( PatchPanelPort::STATE_CONNECTED );
                    $ppp->setConnectedAt( new \DateTime );
                    break;
                case PatchPanelPort::STATE_AWAITING_CEASE :
                    $ppp->setState( PatchPanelPort::STATE_AWAITING_CEASE );
                    $ppp->setCeaseRequestedAt( new \DateTime );
                    break;
                case PatchPanelPort::STATE_CEASED :
                    $ppp->setState( PatchPanelPort::STATE_CEASED );
                    $ppp->setCeasedAt( new \DateTime );
                    break;
                default:
                    $ppp->setState( $status );
                    break;
            }

            $ppp->setLastStateChange( new \DateTime );
            D2EM::flush();

            if( $status == PatchPanelPort::STATE_CEASED ){
                $ppp->createHistory();
                $message = ' - An history has been generated after ceased.';
            }
            AlertContainer::push( 'The patch panel port has been set to '.$ppp->resolveStates().$message, Alert::SUCCESS );
        }
        else{
            AlertContainer::push( 'An error occurred !', Alert::DANGER );
        }

        return redirect( '/patch-panel-port/list/patch-panel/'.$ppp->getPatchPanel()->getId() );
    }

    /**
     * Allow to download a file
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $id id of the Patch panel port file
     * @return  file
     */
    public function downloadFile( int $id ){
        $pppFile = false;
        if($id != null) {
            if ( ! ( $pppFile = D2EM::getRepository( PatchPanelPortFile::class )->find( $id ) ) ) {
                abort(404);
            }
        }
        /* @var PatchPanelPortFile $pppFile */
        $path = $pppFile->getPath();

        return response()->file( storage_path().'/files/'.$path, ['Content-Type' => $pppFile->getType()] );
    }

    /**
     * Display and fill the form to send an email to the customer
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @param  int $id patch panel port id
     * @param  int $type Email type to send
     * @return  view
     */
    public function email( int $id, int $type ): View {

        if ( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
            abort(404, 'Patch panel port not found');
        }

        if( !( $emailClass = D2EM::getRepository( PatchPanelPort::class )->resolveEmailClass( $type ) ) ) {
            abort(404, 'Email type not found');
        }

        /** @var Email $mailable */
        $mailable = new $emailClass( $ppp );

        Former::populate([
            'email_to'                  => implode( ',', $mailable->getRecipients() ),
            'email_subject'             => $mailable->getSubject(),
            'email_text'                => dd( $mailable->getBody() )
        ]);

        return view( 'patch-panel-port/emailForm' )->with([
            'ppp'                           => $ppp,
            'email_type'                    => $type
        ]);
    }

    /**
     * Send an email to the customer (connected, ceased, info, loa PDF)
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $request instance of the current HTTP request
     * @return  view
     */
    public function sendEmail( EmailPatchPanelPort $request ) {
        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $request->input( 'patch_panel_port_id' ) ) ) ) {
            Log::notice( 'Unknown patch panel port when editing patch panel' );
            abort(404);
        }

        $email_to = $request->input( 'email_to' );
        $email_from = env( 'IDENTITY_SUPPORT_EMAIL' );
        $email_cc = $request->input( 'email_cc');
        $email_bcc = $request->input( 'email_bcc');
        $email_subject = $request->input( 'email_subject' );
        $email_text = $request->input( 'email_text' );

        $hasLoaPDF = ( $request->input( 'loa' ) ? true : false );

        if( $request->input( 'email_type') == PatchPanelPort::EMAIL_LOA ){
            $hasLoaPDF = true;
        }

        $attachFiles = ( $request->input( 'email_type' ) == PatchPanelPort::EMAIL_CEASE or $request->input( 'email_type' ) == PatchPanelPort::EMAIL_INFO ) ? true : false;

        Mail::send('patch-panel-port/email', ['email_text' => $email_text], function ( $message ) use ( $hasLoaPDF,$ppp,$attachFiles,$email_to,$email_cc,$email_bcc )
        {
            $message->from( env( 'IDENTITY_SUPPORT_EMAIL' ) );
            $message->to( explode( ',', $email_to ) );
            if( $email_cc ){
                $message->cc( explode( ',', $email_cc ) );
            }

            if( $email_bcc ){
                $message->bcc( explode( ',', $email_bcc ) );
            }

            if( $attachFiles ){
                foreach( $ppp->getPatchPanelPortPublicFiles() as $file ){
                    $path = PatchPanelPortFile::getPathPPPFile( $file->getStorageLocation() );
                    $message->attach( storage_path().'/files/'.$path,[
                        'as'            => $file->getName(),
                        'mime'          => $file->getType()
                    ]);
                }
            }

            if( $hasLoaPDF ){
                $loaPDFPath = $ppp->createLoaPDF( false );
                $message->attach( $loaPDFPath,[
                    'as'                => $loaPDFPath,
                    'mime'              => 'application/pdf'
                ]);
            }
        });

        return Redirect::to( 'patch-panel-port/list/patch-panel/'.$ppp->getPatchPanel()->getId() );

    }


    /**
     * Allow to download the Letter of Agency - LoA
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $id int the patch panel port
     * @return  JSON customer object
     */
    public function loaPDF( int $id )
    {
        $ppp = false;
        if( $id != null ) {
            if ( ! ( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
                abort(404);
            }
        }

        /** @var PatchPanelPort $ppp */
        if( !Auth::user()->isSuperUser() ){
            if( $ppp->getCustomerId() != Auth::user()->getCustomer()->getId() ){
                abort(404);
            }
        }

        if( $ppp->isStateAwaitingXConnect() or $ppp->isStateConnected() ){
            return $ppp->createLoaPDF( true );
        } else {
            abort(404);
        }
    }


    /**
     * Allow to access to the Loa with the patch panel port ID and the LoA code
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $id int the patch panel port
     * @params  $loaCode string LoA Code
     * @return  JSON customer object
     */
    public function verifyLoa( int $id, string $loaCode )
    {
        if( $id != null ) {
            if ( ! ( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $id ) ) ) {
                abort(404);
            }
        }
        /** @var PatchPanelPort $ppp */
        if( $ppp->getLoaCode() == $loaCode ){
            if( $ppp->isStateAwaitingXConnect() or $ppp->isStateConnected() ){
                return $ppp->createLoaPDF(true);
            }
        } else {
            abort(404);
        }
    }

}