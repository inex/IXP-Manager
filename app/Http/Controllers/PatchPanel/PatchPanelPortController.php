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
Use DateTime;

use Entities\Customer;
use Entities\PatchPanel;
use Entities\PatchPanelPort;
use Entities\PatchPanelPortFile;
use Entities\PatchPanelPortHistory;
use Entities\PhysicalInterface;
use Entities\Switcher;
use Entities\SwitchPort;

use Former\Facades\Former;

use Illuminate\Http\JsonResponse;
use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\EmailPatchPanelPort;
use IXP\Http\Requests\StorePatchPanelPort;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Http\RedirectResponse;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use Illuminate\View\View;

use Auth;


use Mail;
use IXP\Mail\PatchPanelPort as PatchPanelPortMail;

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

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
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @parama  int $id allow to display all the port for a patch panel => if null display all ports for all patch panel
     * @return  view
     */
    public function index(int $id = null): View{
        $patchPanel = false;
        if($id != null) {
            if (!($patchPanel = D2EM::getRepository(PatchPanel::class)->find($id))) {
                abort(404);
            }
        }

        return view('patch-panel-port/index')->with([
            'patchPanelPorts'               => D2EM::getRepository(PatchPanelPort::class)->getAllPatchPanelPort($id),
            'patchPanel'                    => $patchPanel,
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
        $patchPanelPort = false;

        if( !( $patchPanelPort = D2EM::getRepository( PatchPanelPort::class )->find($id) ) ) {
            abort(404);
        }

        // display master port informations
        if($patchPanelPort->getDuplexMasterPort() != null){
            $patchPanelPort = $patchPanelPort->getDuplexMasterPort();
        }

        $hasDuplex = $patchPanelPort->hasSlavePort();

        // fill the form with patch panel port data
        Former::populate([
            'number'                => $patchPanelPort->getNumber(),
            'patch_panel'           => $patchPanelPort->getPatchPanel()->getName(),
            'colo_circuit_ref'      => $patchPanelPort->getColoCircuitRef(),
            'ticket_ref'            => $patchPanelPort->getTicketRef(),
            'switch'                => $patchPanelPort->getSwitchId(),
            'switch_port'           => $patchPanelPort->getSwitchPortId(),
            'customer'              => $patchPanelPort->getCustomerId(),
            'partner_port'          => $patchPanelPort->getDuplexSlavePortId(),
            'state'                 => $patchPanelPort->getState(),
            'notes'                 => $patchPanelPort->getNotes(),
            'private_notes'         => $patchPanelPort->getPrivateNotes(),
            'assigned_at'           => $patchPanelPort->getAssignedAtFormated(),
            'connected_at'          => $patchPanelPort->getConnectedAtFormated(),
            'ceased_requested_at'   => $patchPanelPort->getCeaseRequestedAtFormated(),
            'ceased_at'             => $patchPanelPort->getCeasedAtFormated(),
            'last_state_change_at'  => $patchPanelPort->getLastStateChangeFormated(),
            'chargeable'            => $patchPanelPort->getChargeableDefaultNo(),
            'owned_by'              => $patchPanelPort->getOwnedBy()
        ]);

        // display the duplex port if set or the list of all duplex port available
        if($hasDuplex) {
            $partnerPorts = [ $patchPanelPort->getDuplexSlavePortId() => $patchPanelPort->getDuplexSlavePortName() ];
        } else {
            $partnerPorts = D2EM::getRepository(PatchPanelPort::class)->getPatchPanelPortAvailableForDuplex($patchPanelPort->getPatchPanel()->getId(), $patchPanelPort->getId());
        }

        return view('patch-panel-port/edit')->with([
            'states'            => ($allocating) ? PatchPanelPort::$ALLOCATE_STATES : PatchPanelPort::$STATES,
            'piStatus'          => PhysicalInterface::$PPP_STATES,
            'customers'         => D2EM::getRepository(Customer::class)->getNames(true),
            'switches'          => D2EM::getRepository(Switcher::class)->getNamesByLocation(true, Switcher::TYPE_SWITCH,$patchPanelPort->getPatchPanel()->getCabinet()->getLocation()->getId()),
            'switchPorts'       => D2EM::getRepository(Switcher::class)->getAllPortForASwitch($patchPanelPort->getSwitchId(),null, $patchPanelPort->getSwitchPortId()),
            'chargeables'       => PatchPanelPort::$CHARGEABLES,
            'ownedBy'           => PatchPanelPort::$OWNED_BY,
            'patchPanelPort'    => $patchPanelPort,
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
    public function store(StorePatchPanelPort $request){
        if( $request->input( 'id', false ) ) {
            // get the existing patch panel object for that ID
            if( !( $patchPanelPort = D2EM::getRepository( PatchPanelPort::class )->find( $request->input( 'id' ) ) ) ) {
                Log::notice( 'Unknown patch panel port when editing patch panel' );
                abort(404);
            }
        } else {
            $patchPanelPort = new PatchPanelPort();
        }

        if($request->input('switch_port')){
            if( !( $switchPort = D2EM::getRepository( SwitchPort::class )->find( $request->input( 'switch_port' ) ) ) ) {
                Log::notice( 'Unknown switch port when adding patch panel' );
                abort(404);
            }

            if($switchPort->getId() != $patchPanelPort->getSwitchPortId()){
                // check if the switch port is available
                if(D2EM::getRepository(PatchPanelPort::class)->isSwitchPortAvailable($switchPort->getId())){
                    $patchPanelPort->setSwitchPort($switchPort);
                } else {
                    AlertContainer::push( 'The switch port selected is already used by an other patch panel Port !', Alert::DANGER );
                    return Redirect::to('patch-panel-port/edit/'.$request->input( 'id' ))
                        ->withInput(Input::all());
                }
            }

            if($request->input('customer')){
                // check if the switch port can be link to the customer
                $custId = D2EM::getRepository(SwitchPort::class)->getCustomerForASwitchPort($switchPort->getId());

                if($custId != null){
                    if($custId != $request->input('customer')){
                        AlertContainer::push( 'Customer not allowed for this switch port !', Alert::DANGER );
                        return Redirect::to('patch-panel-port/edit/'.$request->input( 'id' ))
                            ->withInput(Input::all());
                    }
                }
            }
        } else {
            if($request->input('customer') and $request->input('switch')){
                AlertContainer::push( 'You need to select a switch port !', Alert::DANGER );
                return Redirect::to('patch-panel-port/edit/'.$request->input( 'id' ))
                    ->withInput(Input::all());
            }
            $patchPanelPort->setSwitchPort(null);
        }

        if($patchPanelPort->getState() != $request->input('state')){
            $patchPanelPort->setState($request->input('state'));
            $patchPanelPort->setLastStateChange(new \DateTime(date('Y-m-d')));
        }

        $patchPanelPort->setNotes(($request->input('notes') == '' ? null : $request->input('notes')));

        $patchPanelPort->setPrivateNotes(($request->input('private_notes') == '' ? null : $request->input('private_notes')));

        $patchPanelPort->setColoCircuitRef($request->input('colo_circuit_ref'));
        $patchPanelPort->setTicketRef($request->input('ticket_ref'));

        $patchPanelPort->setCustomer(($request->input('customer')) ? D2EM::getRepository(Customer::class)->find($request->input('customer')) : null);

        if($request->input('customer') and $request->input('assigned_at') == ''){
            $patchPanelPort->setAssignedAt(new \DateTime(date('Y-m-d')));
        } else {
            if($request->input('allocated')){
                $patchPanelPort->setAssignedAt(new \DateTime(date('Y-m-d')));
            } else {
                $patchPanelPort->setAssignedAt(($request->input('assigned_at') == '' ? null : new \DateTime($request->input('assigned_at'))));
            }

        }

        if($request->input('state') == PatchPanelPort::STATE_CONNECTED and $request->input('connected_at') == ''){
            $patchPanelPort->setConnectedAt(new \DateTime(date('Y-m-d')));
        } else {
            $patchPanelPort->setConnectedAt(($request->input('connected_at') == '' ? null : new \DateTime($request->input('connected_at'))));
        }

        if($request->input('state') == PatchPanelPort::STATE_AWAITING_CEASE and $request->input('ceased_requested_at') == ''){
            $patchPanelPort->setCeaseRequestedAt(new \DateTime(date('Y-m-d')));
        } else {
            $patchPanelPort->setCeaseRequestedAt(($request->input('ceased_requested_at') == '' ? null : new \DateTime($request->input('ceased_requested_at'))));
        }

        if($request->input('state') == PatchPanelPort::STATE_CEASED and $request->input('ceased_at') == ''){
            $patchPanelPort->setCeasedAt(new \DateTime(date('Y-m-d')));
        } else {
            $patchPanelPort->setCeasedAt(($request->input('ceased_at') == '' ? null : new \DateTime($request->input('ceased_at'))));
        }

        $patchPanelPort->setInternalUse($request->input('internal_use'));
        $patchPanelPort->setChargeable($request->input('chargeable'));
        $patchPanelPort->setOwnedBy($request->input('owned_by'));

        D2EM::persist($patchPanelPort);

        if($request->input('duplex')){
            if($patchPanelPort->hasSlavePort()){
                $isNewSlavePort = false;
                $partnerPort = $patchPanelPort->getDuplexSlavePort();
            } else {
                $isNewSlavePort = true;
                $partnerPort = D2EM::getRepository(PatchPanelPort::class)->find($request->input('partner_port'));
            }

            $duplexPort = $patchPanelPort->setDuplexPort($partnerPort,$isNewSlavePort);

            if($isNewSlavePort){
                $patchPanelPort->addDuplexSlavePort($duplexPort);
            }
        }
        D2EM::flush();

        // create an history and reset the patch panel port
        if($patchPanelPort->getState() == PatchPanelPort::STATE_CEASED){
            $patchPanelPort->createHistory();
        }

        // set physical interface status if available
        if($request->input('allocated')){
            if($request->input('pi_status')){
                $physicalInterface = $patchPanelPort->getSwitchPort()->getPhysicalInterface();
                switch ($request->input('pi_status')) {
                    case PhysicalInterface::STATUS_CONNECTED :
                        $piStatus = PhysicalInterface::STATUS_QUARANTINE;
                        break;
                    case PhysicalInterface::STATUS_XCONNECT :
                        $piStatus = PhysicalInterface::STATUS_XCONNECT;
                        break;
                }
                $physicalInterface->setStatus($piStatus);
                D2EM::persist($patchPanelPort);
                D2EM::flush();
            }
        }

        return Redirect::to('patch-panel-port/list/patch-panel/'.$patchPanelPort->getPatchPanel()->getId());

    }

    /**
     * Display the patch panel port informations
     * and the patch panel for history the that patch panel port if exist
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  int $id ID of the patch panel
     * @return  view
     */
    public function view(int $id = null)
    {
        $patchPanelPort = false;

        if( !($patchPanelPort = D2EM::getRepository(PatchPanelPort::class)->find($id))){
            abort(404);
        }

        if(!Auth::user()->isSuperUser()){
            if($patchPanelPort->getCustomerId() != Auth::user()->getCustomer()->getId()){
                abort(404);
            }
        }

        $listHistory[] = $patchPanelPort;

        // get the patch panel port histories
        foreach ($patchPanelPort->getPatchPanelPortHistoryMaster() as $history){
            $listHistory[] = $history;
        }

        return view('patch-panel-port/view')->with([
            'patchPanelPort'    => $patchPanelPort,
            'listHistory'       => $listHistory,
            'isSuperUser'       => Auth::user()->isSuperUser()]);
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
        $error = array('type' => '', 'message' => '');
        $message = '';
        if( !( $patchPanelPort = D2EM::getRepository( PatchPanelPort::class )->find($id) ) ) {
            abort(404);
        }

        if(array_key_exists($status,PatchPanelPort::$STATES)){
            switch ($status) {
                case PatchPanelPort::STATE_CONNECTED :
                    $patchPanelPort->setState(PatchPanelPort::STATE_CONNECTED);
                    $patchPanelPort->setConnectedAt(new \DateTime(date('Y-m-d')));
                    break;
                case PatchPanelPort::STATE_AWAITING_CEASE :
                    $patchPanelPort->setState(PatchPanelPort::STATE_AWAITING_CEASE);
                    $patchPanelPort->setCeaseRequestedAt(new \DateTime(date('Y-m-d')));
                    break;
                case PatchPanelPort::STATE_CEASED :
                    $patchPanelPort->setState(PatchPanelPort::STATE_CEASED);
                    $patchPanelPort->setCeasedAt(new \DateTime(date('Y-m-d')));
                    break;
            }

            $patchPanelPort->setLastStateChange(new \DateTime(date('Y-m-d')));
            D2EM::persist($patchPanelPort);
            D2EM::flush();

            if($status == PatchPanelPort::STATE_CEASED){
                $patchPanelPort->createHistory();
                $message = ' - An history has been generated after ceased.';
            }
            AlertContainer::push( 'The patch panel port has been set to '.$patchPanelPort->resolveStates().$message, Alert::SUCCESS );
        }
        else{
            AlertContainer::push( 'An error occurred !', Alert::DANGER );
        }

        return redirect( '/patch-panel-port/list/patch-panel/'.$patchPanelPort->getPatchPanel()->getId() );
    }


    /**
     * Allow to download a file
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $id id of the Patch panel port file
     * @return  file
     */
    public function downloadFile(int $id)
    {
        $pppFile = false;
        if($id != null) {
            if (!($pppFile = D2EM::getRepository(PatchPanelPortFile::class)->find($id))) {
                abort(404);
            }
        }
        $path = PatchPanelPortFile::getPathPPPFile($pppFile->getStorageLocation());

        return response()->file(storage_path().'/files/'.$path, ['Content-Type' => $pppFile->getType()]);
    }

    /**
     * Display and fill the form to send an email to the customer
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @parama  int $id patch panel port id
     * @return  view
     */
    public function email(int $id = null, $type = null): View{
        $patchPanelPort = false;
        if($id != null and $type != null) {
            if (!($patchPanelPort = D2EM::getRepository(PatchPanelPort::class)->find($id))) {
                abort(404);
            }

            $customer = $patchPanelPort->getCustomer();
            $usersEmail = $customer->getUsersEmail();
            $email_to = implode(',',$usersEmail);
            $peeringEmail = $customer->getPeeringemail();

            switch ($type){
                case PatchPanelPort::EMAIL_CONNECT:
                    $subject = "Cross connect to ".env('IDENTITY_ORGNAME')." [".$patchPanelPort->getColoCircuitRef()." / ".$patchPanelPort->getName()."]";
                    $emailText = "Hi,\n\n";
                    $emailText .= "** ACTION REQUIRED - PLEASE SEE BELOW **\n\n";

                    $emailText .= "We have allocated the following cross connect demarcation point for your connection to ".env( 'IDENTITY_ORGNAME' ).". Please order a ".$patchPanelPort->getPatchPanel()->getCableType()." cross connect where our demarcation point is:\n\n";

                    $emailText .= "Patch panel: ".$patchPanelPort->getPatchPanel()->getName()."\n";
                    $emailText .= "Port: ".$patchPanelPort->getName()."\n\n";

                    if($patchPanelPort->getSwitchPort()){
                        $emailText .= "This request is in relation the following connection: \n";
                        $emailText .= "Switch Port: ".$patchPanelPort->getSwitchName().'::'.$patchPanelPort->getSwitchPortName()."\n\n";
                    }
                    $emailText .= "If you have any queries about this, please reply to this email.\n\n";
                    break;
                case PatchPanelPort::EMAIL_CEASE:
                    $subject = "Cease Cross connect to ".env('IDENTITY_ORGNAME')." [".$patchPanelPort->getColoCircuitRef()." / ".$patchPanelPort->getName()."]";

                    $emailText = "Hi,\n\n";
                    $emailText .= "** ACTION REQUIRED - PLEASE SEE BELOW **\n\n";
                    $emailText .= "You have a cross connect to ".env( 'IDENTITY_ORGNAME' )." which our records indicate is no longer required.\n\n";
                    $emailText .= "Please contact the co-location facility and request that they cease the following cross connect:\n\n";
                    $emailText .= "Colo Reference: ".$patchPanelPort->getColoCircuitRef()."\n";
                    $emailText .= "Patch panel: ".$patchPanelPort->getPatchPanel()->getName()."\n";
                    $emailText .= "Port: ".$patchPanelPort->getName()."\n";
                    $emailText .= "Connected on: ".$patchPanelPort->getConnectedAtFormated()."\n\n";

                    if($patchPanelPort->hasPublicFiles()){
                        $emailText .= "We have attached documentation which we have on file regarding this connection which may help process this request.\n\n";
                    }

                    if($patchPanelPort->getNotes()){
                        $emailText .= "We have also recorded the following notes which may also be of use:\n";
                        $emailText .= $patchPanelPort->getNotes()."\n\n";
                    }

                    $emailText .= "> add with leading '>' so it appears quoted\n\n";
                    $emailText .= "If you have any queries about this, please reply to this email.\n\n";
                    break;
                case PatchPanelPort::EMAIL_INFO:
                    $subject = "Cross connect details for ".env('IDENTITY_ORGNAME')." [".$patchPanelPort->getColoCircuitRef()." / ".$patchPanelPort->getName()."]";

                    $emailText = "Hi,\n\n";
                    $emailText .= "You or someone in your organisation requested details on the following cross connect to ".env( 'IDENTITY_ORGNAME' ).".\n\n";
                    $emailText .= "Colo Reference: ".$patchPanelPort->getColoCircuitRef()."\n";
                    $emailText .= "Patch panel: ".$patchPanelPort->getPatchPanel()->getName()."\n";
                    $emailText .= "Port: ".$patchPanelPort->getName()."\n";
                    $emailText .= "State: ".$patchPanelPort->resolveStates()."\n";

                    if($patchPanelPort->getCeaseRequestedAt()){
                        $emailText .= "Cease requested: ".$patchPanelPort->getCeaseRequestedAtFormated()."\n";
                    }

                    $emailText .= "Connected on: ".$patchPanelPort->getConnectedAtFormated()."\n\n";

                    if($patchPanelPort->hasPublicFiles()){
                        $emailText .= "We have attached documentation which we have on file regarding this connection.\n\n";
                    }

                    if($patchPanelPort->getNotes()){
                        $emailText .= "We have also recorded the following notes:\n\n";
                        $emailText .= $patchPanelPort->getNotes()."\n\n";
                    }

                    $emailText .= "> add with leading '>' so it appears quoted\n\n";
                    $emailText .= "If you have any queries about this, please reply to this email.\n\n";
                    break;
                case PatchPanelPort::EMAIL_LOA:
                    $subject = "Cross connect Letter of Agency details for ".env('IDENTITY_ORGNAME')." [".$patchPanelPort->getColoCircuitRef()." / ".$patchPanelPort->getName()."]";

                    $emailText = "Hi,\n\n";
                    $emailText .= "You or someone in your organisation requested details on the following cross connect to ".env( 'IDENTITY_ORGNAME' ).".\n\n";
                    $emailText .= "Colo Reference: ".$patchPanelPort->getColoCircuitRef()."\n";
                    $emailText .= "Patch panel: ".$patchPanelPort->getPatchPanel()->getName()."\n";
                    $emailText .= "Port: ".$patchPanelPort->getName()."\n";
                    $emailText .= "State: ".$patchPanelPort->resolveStates()."\n\n";
                    $emailText .= "We have attached the Letter of Agency in PDF format.\n\n";
                    $emailText .= "> add with leading '>' so it appears quoted\n\n";
                    $emailText .= "If you have any queries about this, please reply to this email.\n\n";
                    break;
            }

            $emailText .= env('IDENTITY_NAME')."\n";
            $emailText .= env('IDENTITY_EMAIL');

            Former::populate([
                'email_to'                  => $email_to.','.$peeringEmail,
                'email_subject'             => $subject,
                'email_text'                => $emailText
            ]);
        }

        return view('patch-panel-port/emailForm')->with([
            'patchPanelPort'            => $patchPanelPort,
            'email_type'                => $type
        ]);
    }

    /**
     * Send an email to the customer (connected, ceased, info, loa PDF)
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     * @params  $request instance of the current HTTP request
     * @return  view
     */
    public function sendEmail(EmailPatchPanelPort $request) {
        if( !( $patchPanelPort = D2EM::getRepository( PatchPanelPort::class )->find( $request->input( 'patch_panel_port_id' ) ) ) ) {
            Log::notice( 'Unknown patch panel port when editing patch panel' );
            abort(404);
        }

        $email_to = $request->input( 'email_to' );
        $email_from = env('IDENTITY_SUPPORT_EMAIL');
        $email_cc = $request->input( 'email_cc');
        $email_bcc = $request->input( 'email_bcc');
        $email_subject = $request->input( 'email_subject' );
        $email_text = $request->input( 'email_text' );

        $hasLoaPDF = ($request->input( 'loa' )?true:false);

        if($request->input( 'email_type') == PatchPanelPort::EMAIL_LOA){
            $hasLoaPDF = true;
        }

        $attachFiles = ($request->input( 'email_type' ) == PatchPanelPort::EMAIL_CEASE or $request->input( 'email_type' ) == PatchPanelPort::EMAIL_INFO) ? true : false;

        Mail::send('patch-panel-port/email', ['email_text' => $email_text], function ($message) use ($hasLoaPDF,$patchPanelPort,$attachFiles,$email_to,$email_cc,$email_bcc)
        {
            $message->from(env('IDENTITY_SUPPORT_EMAIL'));
            $message->to(explode( ',', $email_to ));
            if($email_cc){
                $message->cc(explode( ',', $email_cc ));
            }

            if($email_bcc){
                $message->bcc(explode( ',', $email_bcc ));
            }

            if($attachFiles){
                foreach($patchPanelPort->getPatchPanelPortPublicFiles() as $file){
                    $path = PatchPanelPortFile::getPathPPPFile($file->getStorageLocation());
                    $message->attach(storage_path().'/files/'.$path,[
                        'as'    => $file->getName(),
                        'mime'  => $file->getType()
                    ]);
                }
            }

            if($hasLoaPDF){
                $loaPDFPath = $patchPanelPort->createLoaPDF(false);
                $message->attach($loaPDFPath,[
                    'as'    => $loaPDFPath,
                    'mime'  => 'application/pdf'
                ]);
            }
        });

        return Redirect::to('patch-panel-port/list/patch-panel/'.$patchPanelPort->getPatchPanel()->getId());

    }


    /**
     * Allow to download the Letter of Agency - LoA
     *
     * @author  Yann Robin <yann@islandbridgenetworks.ie>
     *
     * @params  $id int the patch panel port
     * @return  JSON customer object
     */
    public function sendLoadPDF(int $id)
    {
        $ppp = false;
        if($id != null) {
            if (!($ppp = D2EM::getRepository(PatchPanelPort::class)->find($id))) {
                abort(404);
            }
        }

        if(!Auth::user()->isSuperUser()){
            if($ppp->getCustomerId() != Auth::user()->getCustomer()->getId()){
                abort(404);
            }
        }

        if($ppp->getState() == PatchPanelPort::STATE_AWAITING_XCONNECT or $ppp->getState() == PatchPanelPort::STATE_CONNECTED){
            return $ppp->createLoaPDF(true);
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
    public function verifyLoa(int $id, string $loaCode)
    {
        if($id != null) {
            if (!($ppp = D2EM::getRepository(PatchPanelPort::class)->find($id))) {
                abort(404);
            }
        }

        if($ppp->getLoaCode() == $loaCode){
            if($ppp->getState() == PatchPanelPort::STATE_AWAITING_XCONNECT or $ppp->getState() == PatchPanelPort::STATE_CONNECTED){
                return $ppp->createLoaPDF(true);
            }
        } else {
            abort(404);
        }
    }

}