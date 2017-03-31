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

use Entities\Cabinet;
use Entities\Customer;
use Entities\Location;
use Entities\PatchPanel;
use Entities\PatchPanelPort;
use Entities\PatchPanelPortFile;
use Entities\PhysicalInterface;
use Entities\Switcher;
use Entities\SwitchPort;
use Entities\User;


use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

use IXP\Exceptions\Mailable as MailableException;
use IXP\Http\Controllers\Controller;
use IXP\Http\Requests\EmailPatchPanelPort as EmailPatchPanelPortRequest;
use IXP\Http\Requests\StorePatchPanelPort;
use IXP\Mail\PatchPanelPort\Email;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

use Auth, D2EM, Former, Input, Log, Mail, Redirect;

use Repositories\PatchPanelPort as PatchPanelPortRepository;

/**
 * PatchPanelPort Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PatchPanelPortController extends Controller
{
    /**
     * @var PatchPanelPort
     */
    private $ppp = null;

    /**
     * Get the PPP object.
     *
     * Our middleware ensures that a PPP is loaded when an 'id' parameter exists in the route.
     * It jams it into the request.
     *
     * @param int $id Just IDE glue so we can pass the ID from action functions without the IDE complaining about unused parameters
     * @return PatchPanelPort
     */
    private function getPPP( /** @noinspection PhpUnusedParameterInspection */ int $id = null ): PatchPanelPort {

        if( $this->ppp === null ) {
            $this->ppp = request()->get('ppp');
            assert( $this->ppp instanceof PatchPanelPort );
        }
        return $this->ppp;
    }

    /**
     * Display all the patch panel ports (optionally limited to a specific patch panel)
     *
     * @param   int $ppid Show all ports for a patch panel
     * @return  View
     */
    public function index( int $ppid = null ): View {

        if( $ppid !== null && !( $pp = D2EM::getRepository( PatchPanel::class )->find( $ppid ) ) ) {
            abort(404);
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'patch-panel-port/index' )->with([
            'patchPanelPorts'               => D2EM::getRepository( PatchPanelPort::class )->getAllPatchPanelPort( $ppid ),
            'pp'                            => $pp ?? false,
        ]);
    }

    /**
     * Display all the patch panel ports based on a search
     *
     * @param   Request $request
     * @return  View
     */
    public function advancedIndex( Request $request ): View {

        $location  = is_numeric( $request->get('location') ) ? intval( $request->get('location') ) : 0;
        $cabinet   = is_numeric( $request->get('cabinet' ) ) ? intval( $request->get('cabinet' ) ) : 0;
        $cabletype = is_numeric( $request->get('type'    ) ) ? intval( $request->get('type'    ) ) : 0;

        $summary = "Filtered for: ";
        $summary .= $location ? D2EM::getRepository(Location::class)->find($location)->getName() : 'All locations';
        $summary .= ', ' . ( $cabinet ? D2EM::getRepository(Cabinet::class)->find($cabinet)->getName() : 'all cabinets' );
        $summary .= ', ' . ( $cabletype ? PatchPanel::$CABLE_TYPES[$cabletype] : 'all cable types' ) . '.';

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'patch-panel-port/index' )->with([
            'patchPanelPorts'               => D2EM::getRepository( PatchPanelPort::class )->advancedSearch(
                                                    $location, $cabinet, $cabletype ),
            'pp'                            => $pp ?? false,
            'summary'                       => $summary,
        ]);
    }

    /**
     * Display the form to edit a patch panel port
     *
     * @param  int    $id        Patch panel port that need to be edited
     * @param  string $formType  Which type of form to show
     * @return View
     */
    public function edit( int $id, string $formType = null ): View {
        /** @var PatchPanelPort $ppp */
        if( !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find($id) ) ) {
            abort(404);
        }

        // if this is a slave port in a duplex port, swap for the master:
        if( $ppp->getDuplexMasterPort() != null ){
            $ppp = $ppp->getDuplexMasterPort();
        }

        switch ( $formType )  {
            case 'allocate' :
                $allocating = true;
                $prewired   = false;
                $states     = PatchPanelPort::$ALLOCATE_STATES;
                break;

            case 'prewired' :
                $allocating = false;
                $prewired   = true;
                $states     = [ PatchPanelPort::STATE_PREWIRED => PatchPanelPort::$STATES[PatchPanelPort::STATE_PREWIRED] ];
                break;

            default :
                $allocating = false;
                $prewired   = false;
                $states     = PatchPanelPort::$STATES;
                break;
        }

        // If we're allocating this port, set the chargable flag to the patch panel's default:
        $chargeable = ( $allocating and $ppp->isStateAvailable()) ? $ppp->getPatchPanel()->getChargeable() : $ppp->getChargeable();

        if( $ppp->getSwitchPort() ) {
            // FIXME: Queries and logic could be improved.
            /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
            $switchPorts = D2EM::getRepository(Switcher::class)->getAllPorts( $ppp->getSwitchPort()->getSwitcher()->getId(), null, $ppp->getSwitchPort()->getId() );
        }

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

        // display the duplex port if set or the list of all duplex ports available
        // FIXME: We should allow editing this - see https://github.com/inex/IXP-Manager/issues/307
        if( $ppp->hasSlavePort() ) {
            $partnerPorts = [ $ppp->getDuplexSlavePortId() => $ppp->getDuplexSlavePortName() ];
        } else {
            /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
            $partnerPorts = D2EM::getRepository( PatchPanelPort::class )->getPatchPanelPortAvailableForDuplex( $ppp->getPatchPanel()->getId(), $ppp->getId() );
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'patch-panel-port/edit' )->with([
            'states'            => $states,
            'customers'         => D2EM::getRepository( Customer::class )->getNames( false ),
            'switches'          => D2EM::getRepository( Switcher::class )->getNamesByLocation( true, Switcher::TYPE_SWITCH,$ppp->getPatchPanel()->getCabinet()->getLocation()->getId() ),
            'switchPorts'       => $switchPorts ?? [],
            'chargeables'       => PatchPanelPort::$CHARGEABLES,
            'ownedBy'           => PatchPanelPort::$OWNED_BY,
            'ppp'               => $ppp,
            'partnerPorts'      => $partnerPorts,
            'hasDuplex'         => $ppp->hasSlavePort(),
            'user'              => Auth::user(),
            'allocating'        => $allocating,
            'prewired'          => $prewired
        ]);
    }

    /**
     * Display the form to edit a patch panel port
     *
     * @param   int $id patch panel port that need to be edited
     * @return  View
     */
    public function editToAllocate( int $id ): View {
        return $this->edit( $id, 'allocate' );
    }

    /**
     * Display the form to edit a patch panel port
     *
     * @param  int $id patch panel port that need to be edited
     * @return  View
     */
    public function editToPrewired( int $id ): View {
        return $this->edit( $id, 'prewired' );
    }

    /**
     * Add or edit a patch panel port (set all the data needed)
     *
     * @param   StorePatchPanelPort $request instance of the current HTTP request
     * @return  RedirectResponse
     */
    public function store( StorePatchPanelPort $request ): RedirectResponse {

        if( !$request->input( 'id' ) || !( $ppp = D2EM::getRepository( PatchPanelPort::class )->find( $request->input( 'id' ) ) ) ) {
            abort(404, 'Unknown patch panel port');
        }

        if( $request->input( 'switch_port' ) ) {
            if( !( $sp = D2EM::getRepository( SwitchPort::class )->find( $request->input( 'switch_port' ) ) ) ) {
                abort(404, 'Unknown switch port' );
            }

            if( $sp->getId() != $ppp->getSwitchPortId() ){
                // check if the switch port is available
                /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
                if( D2EM::getRepository( PatchPanelPort::class )->isSwitchPortAvailable( $sp->getId() ) ){
                    $ppp->setSwitchPort($sp);
                } else {
                    AlertContainer::push( 'The switch port selected is already used by an other patch panel port.', Alert::DANGER );
                    return Redirect::to( 'patch-panel-port/edit/'.$request->input( 'id' ) )
                        ->withInput( Input::all() );
                }
            }

            if( $request->input( 'customer' ) ) {
                // check if the switch port can be linked to the customer
                /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
                $custId = D2EM::getRepository( SwitchPort::class )->getCustomerForASwitchPort( $sp->getId() );

                if( $custId != null ) {
                    if( $custId != $request->input( 'customer' ) ) {
                        AlertContainer::push( 'The selected customer does not seem to have a relationship with the switch port', Alert::DANGER );
                        return Redirect::to( 'patch-panel-port/edit/'.$request->input( 'id' ) )
                            ->withInput( Input::all() );
                    }
                }
            }
        } else {
            if( $request->input('customer') and $request->input( 'switch' ) ) {
                AlertContainer::push( 'You need to select a switch port when a switch is selected', Alert::DANGER );
                return Redirect::to( 'patch-panel-port/edit/'.$request->input( 'id' ) )
                    ->withInput( Input::all() );
            }
            $ppp->setSwitchPort( null );
        }

        if( $ppp->getState() != $request->input( 'state' ) ) {
            $ppp->setState( $request->input( 'state' ) );
            $ppp->setLastStateChange( new \DateTime );
        }

        $ppp->setNotes( ( clean( $request->input( 'notes', '' ) ) ) );

        $ppp->setPrivateNotes( clean( $request->input( 'private_notes', '' )  ) );

        $ppp->setColoCircuitRef( $request->input( 'colo_circuit_ref', '') );
        $ppp->setTicketRef( $request->input( 'ticket_ref', '' ) );

        $ppp->setCustomer( ( $request->input( 'customer' ) ) ? D2EM::getRepository( Customer::class )->find( $request->input( 'customer' ) ) : null );

        if( $request->input( 'customer' ) and $request->input( 'assigned_at' ) == '' ) {
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

        $ppp->setInternalUse( ( $request->input( 'internal_use' ) ) ? $request->input( 'internal_use' ) : false );
        $ppp->setChargeable( ( $request->input( 'chargeable' ) ) ? $request->input( 'chargeable' ) : false );
        $ppp->setOwnedBy( ( $request->input( 'owned_by' ) ) ? $request->input( 'owned_by' ) : false );


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

        // create a history and reset the patch panel port
        if( $ppp->getState() == PatchPanelPort::STATE_CEASED ) {
            /** @noinspection PhpUndefinedMethodInspection - we need to get dynamic getRepository() factory working */
            if( D2EM::getRepository(PatchPanelPort::class)->archive( $ppp ) ) {
                $ppp->resetPatchPanelPort();
            }
        }

        // set physical interface status if available
        if( $request->input( 'allocated' ) ) {
            if( $request->input( 'pi_status' ) && $request->input( 'pi_status' ) > 0 && $ppp->getSwitchPort()->getPhysicalInterface() ) {
                $ppp->getSwitchPort()->getPhysicalInterface()->setStatus( $request->input( 'pi_status' ) );
            }
        }

        D2EM::flush();

        return Redirect::to( 'patch-panel-port/list/patch-panel/'.$ppp->getPatchPanel()->getId() );

    }

    /**
     * Display the patch panel port and the patch panel for history (if any).
     *
     * @param  int $id ID of the patch panel
     * @return View
     */
    public function view( int $id ): View {

        $listHistory[] = $this->getPPP($id);

        if( !Auth::user()->isSuperUser() ) {
            if( !$this->getPPP()->getCustomer() || $this->getPPP()->getCustomer()->getId() != Auth::user()->getCustomer()->getId() ) {
                abort(404);
            }
        } else {
            // only load history if we're a super user
            // get the patch panel port histories
            foreach ( $this->getPPP()->getPatchPanelPortHistoryMaster() as $history ){
                $listHistory[] = $history;
            }
        }

        return view( 'patch-panel-port/view' )->with([
            'ppp'                       => $this->getPPP(),
            'listHistory'               => $listHistory,
        ]);
    }

    /**
     * Change the status of a patch panel port and set the date value related to the status
     *
     * @param int $id
     * @param int $status
     * @return RedirectResponse
     */
    public function changeStatus( int $id, int $status ): RedirectResponse {


        switch ( $status ) {
            case PatchPanelPort::STATE_AVAILABLE:
                if( $this->getPPP( $id )->isStatePrewired() ) {
                    // we get here via 'Unset Prewired'
                    if( $this->getPPP()->isDuplexPort() ) {
                        $this->getPPP()->getDuplexSlavePort()->setState( PatchPanelPort::STATE_AVAILABLE )->setDuplexMasterPort(null);
                    }
                    $this->getPPP()->setSwitchPort( null );
                }
                $this->getPPP()->setState( PatchPanelPort::STATE_AVAILABLE );
                break;

            case PatchPanelPort::STATE_CONNECTED :
                $this->getPPP()->setState( PatchPanelPort::STATE_CONNECTED );
                $this->getPPP()->setConnectedAt( new \DateTime );
                break;

            case PatchPanelPort::STATE_AWAITING_CEASE :
                $this->getPPP()->setState( PatchPanelPort::STATE_AWAITING_CEASE );
                $this->getPPP()->setCeaseRequestedAt( new \DateTime );
                break;

            case PatchPanelPort::STATE_CEASED :
                $this->getPPP()->setState( PatchPanelPort::STATE_CEASED );
                $this->getPPP()->setCeasedAt( new \DateTime );
                break;

            default:
                $this->getPPP()->setState( $status );
                break;
        }

        $this->getPPP()->setLastStateChange( new \DateTime );

        if( $status == PatchPanelPort::STATE_CEASED ){
            if( $this->getPPP()->getSwitchPort() ) {
                AlertContainer::push( 'The patch panel port has been set to available again. Consider '
                      . 'setting it as  prewired if the cable is still in place. It was connected to '
                      . $this->getPPP()->getSwitchPort()->getSwitcher()->getName() . ' :: '
                      . $this->getPPP()->getSwitchPort()->getName(),
                  Alert::SUCCESS );
            }

            // create a history and reset the patch panel port
            if( $this->getPPP()->getState() == PatchPanelPort::STATE_CEASED ) {
                /** @noinspection PhpUndefinedMethodInspection - we need to get dynamic getRepository() factory working */
                if( D2EM::getRepository(PatchPanelPort::class)->archive( $this->getPPP() ) ) {
                    $this->getPPP()->resetPatchPanelPort();
                }
            }
        }

        D2EM::flush();

        AlertContainer::push( 'The patch panel port has been set to: ' . $this->getPPP()->resolveStates(), Alert::SUCCESS );
        return redirect( '/patch-panel-port/list/patch-panel/'.$this->getPPP()->getPatchPanel()->getId() );
    }

    /**
     * Download files
     *
     * @param   int $pppfid ID of the Patch panel port file
     * @return  Response
     */
    public function downloadFile( int $pppfid ) {

        /** @var PatchPanelPortFile $pppf */
        if( !($pppf = D2EM::getRepository(PatchPanelPortFile::class)->find($pppfid)) ) {
            abort(404 );
        }

        /** @var User $u */
        $u = Auth::user();
        if( !$u->isSuperUser() ) {
            if( !$pppf->getPatchPanelPort()->getCustomer()
                    || $pppf->getPatchPanelPort()->getCustomer()->getId() != $u->getCustomer()->getId()
                    || $pppf->getIsPrivate() ) {
                Log::alert($u->getUsername() . ' tried to access a PPP file with ID:' . $pppf->getId() . ' but does not have permission');
                abort(401);
            }
        }

        /** @noinspection PhpUndefinedMethodInspection  - Laravel's file() is not in the base contract for response() */
        return response()->file( storage_path() . '/files/' . $pppf->getPath(), [ 'Content-Type' => $pppf->getType() ] );
    }

    /**
     * Setup / validation for composing and sending emails
     *
     * @param  int $type Email type to send
     * @param  Email $mailable
     * @return  Email
     */
    private function setupEmailRoutes( int $type, Email $mailable = null ): Email {

        /** @var PatchPanelPortRepository $pppRepository */
        $pppRepository = D2EM::getRepository( PatchPanelPort::class );
        if( !( $emailClass = $pppRepository->resolveEmailClass( $type ) ) ) {
            abort(404, 'Email type not found');
        }

        if( !$mailable ) {
            $mailable = new $emailClass($this->getPPP());
        }

        Former::populate([
            'email_to'       => implode( ',', $mailable->getRecipientEmails('to') ),
            'email_subject'  => $mailable->getSubject(),
            'email_bcc'      => implode( ',', $mailable->getRecipientEmails('bcc') )
        ]);

        return $mailable;
    }

    /**
     * Display and fill the form to send an email to the customer
     *
     * @param  int $id patch panel port id
     * @param  int $type Email type to send
     * @return  view
     */
    public function email( int $id, int $type ): View {

        $mailable = $this->setupEmailRoutes( $type );

        return view( 'patch-panel-port/email-form' )->with([
            'ppp'                           => $this->getPPP($id),
            'emailType'                     => $type,
            'mailable'                      => $mailable
        ]);
    }

    /**
     * Send an email to the customer (connected, ceased, info, loa PDF)
     *
     * @param EmailPatchPanelPortRequest $request
     * @param int $id   patch panel port id
     * @param int  $type Email type to send
     *
     * @return RedirectResponse|View
     */
    public function sendEmail( EmailPatchPanelPortRequest $request, int $id, int $type ) {

        $mailable = $this->setupEmailRoutes( $type );

        $mailable->prepareFromRequest( $request );
        $mailable->prepareBody( $request );

        try {
            $mailable->checkIfSendable();
        } catch( MailableException $e ) {
            AlertContainer::push( $e->getMessage(), Alert::DANGER );

            return view( 'patch-panel-port/email-form' )->with([
                'ppp'                           => $this->getPPP($id),
                'emailType'                     => $type,
                'mailable'                      => $mailable
            ]);
        }

        if( $type == PatchPanelPort::EMAIL_LOA || $request->input( 'loa' ) ) {
            /** @var \Barryvdh\DomPDF\PDF $pdf */
            list($pdf, $pdfname) = $this->createLoAPDF( $this->getPPP() );
            $mailable->attachData( $pdf->output(), $pdfname, [
                'mime'    => 'application/pdf'
            ]);
        }

        // should we also attach public files?
        if( in_array( $type, [ PatchPanelPort::EMAIL_CEASE, PatchPanelPort::EMAIL_INFO ] ) ) {
            foreach( $this->getPPP()->getPatchPanelPortPublicFiles() as $pppf ) {
                /** @var PatchPanelPortFile $pppf */
                $mailable->attach( storage_path() . '/files/' . $pppf->getPath(), [
                    'as'            => $pppf->getName(),
                    'mime'          => $pppf->getType()
                ]);
            }
        }

        Mail::send( $mailable );

        AlertContainer::push( "Email sent.", Alert::SUCCESS );

        return Redirect::to( 'patch-panel-port/list/patch-panel/' . $this->getPPP()->getPatchPanel()->getId() );
    }

    /**
     * Generate the LoA PDF
     *
     * @param \Entities\PatchPanelPort $ppp
     * @return array To be unpacked with list( $pdf, $pdfname )
     */
    private function createLoAPDF( PatchPanelPort $ppp ): array {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView( 'patch-panel-port/loa', ['ppp' => $ppp] );
        $pdfName = sprintf( "LoA-%s-%s.pdf", $ppp->getCircuitReference(), date( 'Y-m-d' ) );
        return [ $pdf, $pdfName ];
    }

    /**
     * Bootstrap LoA request
     *
     */
    private function setupLoA() {
        if( !($this->getPPP()->isStateAwaitingXConnect() || $this->getPPP()->isStateConnected()) ) {
            abort(404);
        }

        /** @var User $u */
        $u = Auth::user();
        if( !$u->isSuperUser() ) {
            if( !$this->getPPP()->getCustomer() || $this->getPPP()->getCustomer()->getId() != $u->getCustomer()->getId() ) {
                Log::alert($u->getUsername() . ' tried to create a PPP LoA for PPP:' . $this->getPPP()->getId() . ' but does not have permission');
                abort(401);
            }
        }
    }

    /**
     * Download a Letter of Authority file - LoA
     *
     * @param   int $id int the patch panel port
     * @return  Response
     */
    public function downloadLoA( int $id ): Response {
        $this->setupLoA();
        list($pdf, $pdfname) = $this->createLoAPDF($this->getPPP($id));
        return $pdf->download($pdfname);
    }

    /**
     * View a Letter of Authority file - LoA
     *
     * @param   int $id int the patch panel port
     * @return  Response
     */
    public function viewLoA( int $id ): Response {
        $this->setupLoA();
        list($pdf, $pdfname) = $this->createLoAPDF($this->getPPP($id));
        return $pdf->stream($pdfname);
    }


    /**
     * Allow to access to the Loa with the patch panel port ID and the LoA code
     *
     * @param  int    $id      The patch panel port
     * @param  string $loaCode LoA Code
     * @return  View
     */
    public function verifyLoa( int $id, string $loaCode ): View {

        /** @var PatchPanelPort $ppp */
        $ppp = D2EM::getRepository(PatchPanelPort::class)->find($id);

        if( !$ppp ) {
            Log::alert( "Failed PPP LoA verification for non-existent port {$id} from {$_SERVER['REMOTE_ADDR']}" );
        } else if( $ppp->getLoaCode() != $loaCode ) {
            Log::alert( "Failed PPP LoA verification for port {$id} from {$_SERVER['REMOTE_ADDR']} - invalid LoA code presented" );
        } else if( !$ppp->isStateAwaitingXConnect() ) {
            Log::alert( "PPP LoA verification denied for port {$id} from {$_SERVER['REMOTE_ADDR']} - port status is not AwaitingXConnect" );
        }

        return view( 'patch-panel-port/verify-loa' )->with([
            'ppp'     => D2EM::getRepository(PatchPanelPort::class)->find($id),
            'loaCode' => $loaCode
        ]);
    }

}