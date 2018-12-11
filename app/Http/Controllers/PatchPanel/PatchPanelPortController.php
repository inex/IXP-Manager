<?php

namespace IXP\Http\Controllers\PatchPanel;

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

use Entities\{
    Cabinet                     as CabinetEntity,
    Customer                    as CustomerEntity,
    Location                    as LocationEntity,
    PatchPanel                  as PatchPanelEntity,
    PatchPanelPort              as PatchPanelPortEntity,
    PatchPanelPortFile          as PatchPanelPortFileEntity,
    PatchPanelPortHistoryFile   as PatchPanelPortHistoryFileEntity,
    Switcher                    as SwitcherEntity,
    SwitchPort                  as SwitchPortEntity,
    User                        as UserEntity,
    PhysicalInterface           as PhysicalInterfaceEntity
};

use Illuminate\Http\{
    RedirectResponse,
    Request,
    Response,
    JsonResponse
};

use Illuminate\View\View;

use IXP\Exceptions\Mailable as MailableException;
use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\{
    EmailPatchPanelPort as EmailPatchPanelPortRequest,
    StorePatchPanelPort as StorePatchPanelPortRequest,
    MovePatchPanelPort as MovePatchPanelPortRequest
};

use IXP\Mail\PatchPanelPort\Email;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Auth, D2EM, Former, Input, Log, Mail, Redirect, Storage;

use GrahamCampbell\Flysystem\FlysystemManager;

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
     * @var PatchPanelPortEntity
     */
    private $ppp = null;

    /**
     * Get the PPP object.
     *
     * Our middleware ensures that a PPP is loaded when an 'id' parameter exists in the route.
     * It jams it into the request.
     *
     * @param int $id Just IDE glue so we can pass the ID from action functions without the IDE complaining about unused parameters
     * @return PatchPanelPortEntity
     */
    private function getPPP( /** @noinspection PhpUnusedParameterInspection */ int $id = null ): PatchPanelPortEntity {
        if( $this->ppp === null ) {
            $this->ppp = request()->get('ppp');
            assert( $this->ppp instanceof PatchPanelPortEntity );
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

        if( $ppid !== null && !( $pp = D2EM::getRepository( PatchPanelEntity::class )->find( $ppid ) ) ) {
            abort(404);
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'patch-panel-port/index' )->with([
            'patchPanelPorts'               => D2EM::getRepository( PatchPanelPortEntity::class )->getAllPatchPanelPort( $ppid ),
            'pp'                            => $pp ?? false,
        ]);
    }

    /**
     * Display all the patch panel ports based on a search
     *
     * @param   Request $request
     *
     * @return  View
     */
    public function advancedIndex( Request $request ): View {

        $location           = is_numeric( $request->get('location') )       ? intval( $request->get('location') )       : 0;
        $cabinet            = is_numeric( $request->get('cabinet' ) )       ? intval( $request->get('cabinet' ) )       : 0;
        $cabletype          = is_numeric( $request->get('type'    ) )       ? intval( $request->get('type'    ) )       : 0;
        $availableForUse    = $request->get('available')                    ? true                                           : false;

        $summary = "Filtered for: ";
        $summary .= $location ? D2EM::getRepository(LocationEntity::class)->find($location)->getName() : 'all locations';
        $summary .= ', ' . ( $cabinet ? D2EM::getRepository(CabinetEntity::class)->find($cabinet)->getName() : 'all cabinets' );
        $summary .= ', ' . ( $cabletype ? PatchPanelEntity::$CABLE_TYPES[$cabletype] : 'all cable types' );
        $summary .= ( $availableForUse ? ', available for use.' : '.' );

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'patch-panel-port/index' )->with([
            'patchPanelPorts'               => D2EM::getRepository( PatchPanelPortEntity::class )->advancedSearch( $location, $cabinet, $cabletype, $availableForUse ),
            'pp'                            => $pp ?? false,
            'summary'                       => $summary,
        ]);
    }

    /**
     * Display the form to edit a patch panel port
     *
     * @param  int    $id        Patch panel port that need to be edited
     * @param  string $formType  Which type of form to show
     *
     * @return View
     */
    public function edit( int $id, string $formType = null ): View {
        /** @var PatchPanelPortEntity $ppp */
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find($id) ) ) {
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
                $states     = PatchPanelPortEntity::getAllocatedStatesWithDescription();
                break;

            case 'prewired' :
                $allocating = false;
                $prewired   = true;
                $states     = [ PatchPanelPortEntity::STATE_PREWIRED => PatchPanelPortEntity::$STATES[PatchPanelPortEntity::STATE_PREWIRED] ];
                break;

            default :
                $allocating = false;
                $prewired   = false;
                $states     = PatchPanelPortEntity::$STATES;
                break;
        }

        // If we're allocating this port, set the chargable flag to the patch panel's default:
        $chargeable = ( $allocating and $ppp->isStateAvailable()) ? $ppp->getPatchPanel()->getChargeable() : $ppp->getChargeable();


        if( $ppp->getSwitchPort() ) {
            // FIXME: Queries and logic could be improved.
            /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
            $switchPorts = D2EM::getRepository(SwitcherEntity::class)->getAllPortsNotAssignedToPI( $ppp->getSwitchPort()->getSwitcher()->getId(), [], $ppp->getSwitchPort()->getId() );

            // we add the current switch port in the list to display it
            $switchPorts[ ] = [  "name"     => $ppp->getSwitchPort()->getName(),
                                 "typeid"   => $ppp->getSwitchPort()->getType(),
                                 "type"     => $ppp->getSwitchPort()->resolveType(),
                                 "id"       => $ppp->getSwitchPort()->getId() ];

            array_multisort( array_column( $switchPorts, 'id' ), SORT_ASC, $switchPorts );
        }

        $old = request()->old();

        // fill the form with patch panel port data
        Former::populate([
            'description'               => array_key_exists( 'description',             $old    ) ? $old['description']          : $ppp->getDescription(),
            'number'                    => $ppp->getNumber(),
            'patch_panel'               => $ppp->getPatchPanel()->getName(),
            'cabinet_name'              => $ppp->getPatchPanel()->getCabinet()->getName(),
            'colocation_centre'         => $ppp->getPatchPanel()->getCabinet()->getLocation()->getName(),
            'colo_circuit_ref'          => array_key_exists( 'colo_circuit_ref',        $old    ) ? $old['colo_circuit_ref']        : $ppp->getColoCircuitRef(),
            'colo_billing_ref'          => array_key_exists( 'colo_billing_ref',        $old    ) ? $old['colo_billing_ref']        : $ppp->getColoBillingRef(),
            'ticket_ref'                => array_key_exists( 'ticket_ref',              $old    ) ? $old['ticket_ref']              : $ppp->getTicketRef(),
            'switch'                    => array_key_exists( 'switch',                  $old    ) ? $old['switch']                  : $ppp->getSwitchId(),
            'switch_port'               => array_key_exists( 'switch_port',             $old    ) ? $old['switch_port']             : $ppp->getSwitchPortId(),
            'customer'                  => array_key_exists( 'customer',                $old    ) ? $old['customer']                : $ppp->getCustomerId(),
            'partner_port'              => array_key_exists( 'partner_port',            $old    ) ? $old['partner_port']            : $ppp->getDuplexSlavePortId(),
            'state'                     => array_key_exists( 'state',                   $old    ) ? $old['state']                   : $ppp->getState(),
            'assigned_at'               => array_key_exists( 'assigned_at',             $old    ) ? $old['assigned_at']             : $ppp->getAssignedAtFormated(),
            'connected_at'              => array_key_exists( 'connected_at',            $old    ) ? $old['connected_at']            : $ppp->getConnectedAtFormated(),
            'ceased_requested_at'       => array_key_exists( 'ceased_requested_at',     $old    ) ? $old['ceased_requested_at']     : $ppp->getCeaseRequestedAtFormated(),
            'ceased_at'                 => array_key_exists( 'ceased_at',               $old    ) ? $old['ceased_at']               : $ppp->getCeasedAtFormated(),
            'last_state_change_at'      => array_key_exists( 'last_state_change_at',    $old    ) ? $old['last_state_change_at']    : $ppp->getLastStateChangeFormated(),
            'chargeable'                => array_key_exists( 'chargeable',              $old    ) ? $old['chargeable']              : $chargeable,
            'owned_by'                  => array_key_exists( 'owned_by',                $old    ) ? $old['owned_by']                : $ppp->getOwnedBy()
        ]);

        // display the duplex port if set or the list of all duplex ports available
        // FIXME: We should allow editing this - see https://github.com/inex/IXP-Manager/issues/307
        if( $ppp->hasSlavePort() ) {
            $partnerPorts = [ $ppp->getDuplexSlavePortId() => $ppp->getDuplexSlavePortName() ];
        } else {
            /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
            $partnerPorts = D2EM::getRepository( PatchPanelPortEntity::class )->getAvailablePorts( $ppp->getPatchPanel()->getId(), [$ppp->getId()] );
        }

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'patch-panel-port/edit' )->with([
            'states'                => $states,
            'customers'             => D2EM::getRepository( CustomerEntity::class )->getNames( false ),
            'switches'              => D2EM::getRepository( SwitcherEntity::class )->getNamesByLocation( true, $ppp->getPatchPanel()->getCabinet()->getLocation()->getId() ),
            'switchPorts'           => $switchPorts ?? [],
            'chargeables'           => PatchPanelPortEntity::$CHARGEABLES,
            'ownedBy'               => PatchPanelPortEntity::$OWNED_BY,
            'ppp'                   => $ppp,
            'partnerPorts'          => $partnerPorts,
            'hasDuplex'             => $ppp->hasSlavePort(),
            'user'                  => Auth::user(),
            'allocating'            => $allocating,
            'prewired'              => $prewired,
            'notes'                 => $id ? ( array_key_exists( 'notes',           $old ) ? $old['notes']              : $ppp->getNotes() )        : ( array_key_exists( 'notes',              $old ) ? $old['notes']              : "" ),
            'private_notes'         => $id ? ( array_key_exists( 'private_notes',   $old ) ? $old['private_notes']      : $ppp->getPrivateNotes() ) : ( array_key_exists( 'private_notes',      $old ) ? $old['private_notes']      : "" )
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
     * @param   StorePatchPanelPortRequest $request instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
     */
    public function store( StorePatchPanelPortRequest $request ): RedirectResponse {

        /** @var PatchPanelPortEntity $ppp */
        if( !$request->input( 'id' ) || !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $request->input( 'id' ) ) ) ) {
            abort(404, 'Unknown patch panel port');
        }

        if( $request->input( 'switch_port' ) ) {
            if( !( $sp = D2EM::getRepository( SwitchPortEntity::class )->find( $request->input( 'switch_port' ) ) ) ) {
                abort(404, 'Unknown switch port' );
            }

            if( $sp->getId() != $ppp->getSwitchPortId() ){
                // check if the switch port is available
                /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
                if( D2EM::getRepository( PatchPanelPortEntity::class )->isSwitchPortAvailable( $sp->getId() ) ){
                    $ppp->setSwitchPort($sp);
                } else {
                    AlertContainer::push( 'The switch port selected is already used by an other patch panel port.', Alert::DANGER );
                    return Redirect::back()->withInput( Input::all() );
                }
            }

            if( $request->input( 'customer' ) ) {
                // check if the switch port can be linked to the customer
                /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
                $custId = D2EM::getRepository( SwitchPortEntity::class )->getCustomerForASwitchPort( $sp->getId() );

                if( $custId != null ) {
                    if( $custId != $request->input( 'customer' ) ) {
                        AlertContainer::push( 'The selected customer does not seem to have a relationship with the switch port', Alert::DANGER );
                        return Redirect::back()->withInput( Input::all() );
                    }
                }
            }
        } else {
            if( $request->input('customer') and $request->input( 'switch' ) ) {
                AlertContainer::push( 'You need to select a switch port when a switch is selected', Alert::DANGER );
                return Redirect::back()->withInput( Input::all() );
            }
            $ppp->setSwitchPort( null );
        }

        if( $ppp->getState() != $request->input( 'state' ) ) {
            $ppp->setState( $request->input( 'state' ) );
            $ppp->setLastStateChange( new \DateTime );
        }

        $ppp->setDescription(       clean( $request->input( 'description',  ''  ) ) );
        $ppp->setNotes(             clean( $request->input( 'notes',        ''  ) ) );
        $ppp->setPrivateNotes(      clean( $request->input( 'private_notes',''  ) ) );

        $ppp->setColoCircuitRef(    $request->input( 'colo_circuit_ref',    ''  ) );
        $ppp->setColoBillingRef(    $request->input( 'colo_billing_ref',    ''  ) );
        $ppp->setTicketRef(         $request->input( 'ticket_ref',          ''  ) );

        $ppp->setCustomer( ( $request->input( 'customer' ) ) ? D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'customer' ) ) : null );

        if( $request->input( 'customer' ) and $request->input( 'assigned_at' ) == '' ) {
            $ppp->setAssignedAt( new \DateTime );
        } else {
            if( $request->input( 'allocated' ) ){
                $ppp->setAssignedAt( new \DateTime );
            } else {
                $ppp->setAssignedAt( ( $request->input( 'assigned_at' ) == '' ? null : new \DateTime( $request->input( 'assigned_at' ) ) ) );
            }
        }

        if( $request->input( 'state' ) == PatchPanelPortEntity::STATE_CONNECTED and $request->input( 'connected_at' ) == '' ) {
            $ppp->setConnectedAt( new \DateTime );
        } else {
            $ppp->setConnectedAt( ( $request->input( 'connected_at' ) == '' ? null : new \DateTime( $request->input( 'connected_at' ) ) ) );
        }

        if( $request->input( 'state' ) == PatchPanelPortEntity::STATE_AWAITING_CEASE and $request->input( 'ceased_requested_at' ) == '' ) {
            $ppp->setCeaseRequestedAt( new \DateTime );
        } else {
            $ppp->setCeaseRequestedAt( ( $request->input( 'ceased_requested_at' ) == '' ? null : new \DateTime( $request->input( 'ceased_requested_at' ) ) ) );
        }

        if( $request->input( 'state' ) == PatchPanelPortEntity::STATE_CEASED and $request->input( 'ceased_at' ) == '' ) {
            $ppp->setCeasedAt( new \DateTime );
        } else {
            $ppp->setCeasedAt( ( $request->input( 'ceased_at' ) == '' ? null : new \DateTime( $request->input( 'ceased_at' ) ) ) );
        }

        $ppp->setInternalUse(   ( $request->input( 'internal_use'   ) ) ? $request->input( 'internal_use'   ) : false );
        $ppp->setChargeable(    ( $request->input( 'chargeable'     ) ) ? $request->input( 'chargeable'     ) : false );
        $ppp->setOwnedBy(         ( $request->input( 'owned_by'       ) ) ? $request->input( 'owned_by'       ) : false );


        if( $request->input( 'duplex' ) ) {
            if( !$ppp->hasSlavePort() ) {

                if( $request->input( 'partner_port' ) != null ){
                    /** @var PatchPanelPortEntity $partnerPort */
                    $partnerPort = D2EM::getRepository( PatchPanelPortEntity::class )->find( $request->input( 'partner_port' ) );
                    $ppp->setDuplexPort( $partnerPort );
                } else{
                    AlertContainer::push( 'You need to select a partner port as you checked duplex connection', Alert::DANGER );
                    return Redirect::back()->withInput( Input::all() );
                }

            }
        }

        // create a history and reset the patch panel port
        if( $ppp->getState() == PatchPanelPortEntity::STATE_CEASED ) {
            /** @noinspection PhpUndefinedMethodInspection - we need to get dynamic getRepository() factory working */
            if( D2EM::getRepository(PatchPanelPortEntity::class)->archive( $ppp ) ) {
                $ppp->resetPatchPanelPort();
            }
        }

        // set physical interface status if available
        if( $request->input( 'allocated' ) ) {
            if( $request->input( 'pi_status' ) && $request->input( 'pi_status' ) > 0 && $ppp->getSwitchPort() && $ppp->getSwitchPort()->getPhysicalInterface() ) {
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
            foreach ( $this->getPPP()->getPatchPanelPortHistory() as $history ){
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
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
     */
    public function changeStatus( int $id, int $status ): RedirectResponse {
        switch ( $status ) {
            case PatchPanelPortEntity::STATE_AVAILABLE:
                if( $this->getPPP( $id )->isStatePrewired() ) {
                    // we get here via 'Unset Prewired'
                    if( $this->getPPP()->isDuplexPort() ) {
                        $this->getPPP()->getDuplexSlavePort()->setState( PatchPanelPortEntity::STATE_AVAILABLE )->setDuplexMasterPort(null);
                    }
                    $this->getPPP()->setSwitchPort( null );
                }
                $this->getPPP()->setState( PatchPanelPortEntity::STATE_AVAILABLE );
                break;

            case PatchPanelPortEntity::STATE_CONNECTED :
                $this->getPPP()->setState( PatchPanelPortEntity::STATE_CONNECTED );
                $this->getPPP()->setConnectedAt( new \DateTime );
                break;

            case PatchPanelPortEntity::STATE_AWAITING_CEASE :
                $this->getPPP()->setState( PatchPanelPortEntity::STATE_AWAITING_CEASE );
                $this->getPPP()->setCeaseRequestedAt( new \DateTime );
                break;

            case PatchPanelPortEntity::STATE_CEASED :
                $this->getPPP()->setState( PatchPanelPortEntity::STATE_CEASED );
                $this->getPPP()->setCeasedAt( new \DateTime );
                break;

            default:
                $this->getPPP()->setState( $status );
                break;
        }

        $this->getPPP()->setLastStateChange( new \DateTime );

        if( $status == PatchPanelPortEntity::STATE_CEASED ){
            if( $this->getPPP()->getSwitchPort() ) {
                AlertContainer::push( 'The patch panel port has been set to available again. Consider '
                      . 'setting it as  prewired if the cable is still in place. It was connected to '
                      . $this->getPPP()->getSwitchPort()->getSwitcher()->getName() . ' :: '
                      . $this->getPPP()->getSwitchPort()->getName(),
                  Alert::SUCCESS );
            }

            // create a history and reset the patch panel port
            if( $this->getPPP()->getState() == PatchPanelPortEntity::STATE_CEASED ) {
                /** @noinspection PhpUndefinedMethodInspection - we need to get dynamic getRepository() factory working */
                if( D2EM::getRepository(PatchPanelPortEntity::class)->archive( $this->getPPP() ) ) {
                    $this->getPPP()->resetPatchPanelPort();
                }
            }
        }

        D2EM::flush();

        AlertContainer::push( 'The patch panel port has been set to: ' . $this->getPPP()->resolveStates(), Alert::SUCCESS );

        return redirect::back();
    }

    /**
     * Download files
     *
     * @param   int $pppfid ID of the Patch panel port file
     * @return  Response
     */
    public function downloadFile( int $pppfid ) {
        /** @var PatchPanelPortFileEntity $pppf */
        if( !($pppf = D2EM::getRepository(PatchPanelPortFileEntity::class)->find($pppfid)) ) {
            abort(404 );
        }

        /** @var UserEntity $u */
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
        $pppRepository = D2EM::getRepository( PatchPanelPortEntity::class );
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
            'body'                          => $mailable->getBody()
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

        if( $type == PatchPanelPortEntity::EMAIL_LOA || $request->input( 'loa' ) ) {
            /** @var \Barryvdh\DomPDF\PDF $pdf */
            list($pdf, $pdfname) = $this->createLoAPDF( $this->getPPP() );
            $mailable->attachData( $pdf->output(), $pdfname, [
                'mime'    => 'application/pdf'
            ]);
        }

        // should we also attach public files?
        if( in_array( $type, [ PatchPanelPortEntity::EMAIL_CEASE, PatchPanelPortEntity::EMAIL_INFO ] ) ) {
            foreach( $this->getPPP()->getPatchPanelPortPublicFiles() as $pppf ) {
                /** @var PatchPanelPortFileEntity $pppf */
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
    private function createLoAPDF( PatchPanelPortEntity $ppp ): array {
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
        /** @var UserEntity $u */
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
        /** @var PatchPanelPortEntity $ppp */
        $ppp = D2EM::getRepository(PatchPanelPortEntity::class)->find($id);

        if( !$ppp ) {
            Log::alert( "Failed PPP LoA verification for non-existent port {$id} from {$_SERVER['REMOTE_ADDR']}" );
        } else if( $ppp->getLoaCode() != $loaCode ) {
            Log::alert( "Failed PPP LoA verification for port {$id} from {$_SERVER['REMOTE_ADDR']} - invalid LoA code presented" );
        } else if( !$ppp->isStateAwaitingXConnect() ) {
            Log::alert( "PPP LoA verification denied for port {$id} from {$_SERVER['REMOTE_ADDR']} - port status is not AwaitingXConnect" );
        }

        return view( 'patch-panel-port/verify-loa' )->with([
            'ppp'           => D2EM::getRepository(PatchPanelPortEntity::class)->find($id),
            'loaCode'       => $loaCode
        ]);
    }

    /**
     * Access to the form that allow to move the informations of a port to an other port
     *
     * @param  int    $id      The patch panel port
     * @return  View
     */
    public function moveForm( int $id ): View {
        /** @var PatchPanelPortEntity $ppp */
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find($id) ) ) {
            abort(404);
        }

        return view( 'patch-panel-port/move' )->with([
            'ppp'               => $ppp,
            'ppAvailable'       => D2EM::getRepository( PatchPanelEntity::class )->getByLocationAndCableTypeAsArray( $ppp->getPatchPanel()->getCabinet()->getLocation()->getId(), $ppp->getPatchPanel()->getCableType() ),
        ]);
    }


    /**
     * Move a patch panel port information to an other
     *
     * @param   MovePatchPanelPortRequest $request instance of the current HTTP request
     * @return  RedirectResponse
     *
     * @throws
     */
    public function move( MovePatchPanelPortRequest $request ): RedirectResponse {
        $pppOld     = D2EM::getRepository( PatchPanelPortEntity::class )->find( $request->input( 'id'           ) ) ; /** @var PatchPanelPortEntity $pppOld */
        $pppMaster  = D2EM::getRepository( PatchPanelPortEntity::class )->find( $request->input( 'master-port'  ) ) ; /** @var PatchPanelPortEntity $pppMaster */

        $pppSlave = null;

        /** @var PatchPanelPortEntity $pppSlave */
        if( $pppOld->hasSlavePort() ){
            $pppSlave = D2EM::getRepository( PatchPanelPortEntity::class )->find( $request->input( 'slave-port' ) );
        }

        if( D2EM::getRepository( PatchPanelPortEntity::class )->move( $pppOld, $pppMaster, $pppSlave ) ) {
            AlertContainer::push( 'The patch panel port has been moved.', Alert::SUCCESS );
        } else {
            AlertContainer::push( 'Something went wrong!', Alert::DANGER );
        }

        return Redirect::to( 'patch-panel-port/list/patch-panel/' . $pppMaster->getPatchPanel()->getId() );
    }

    /**
     * Delete a patch panel port file
     *
     * @param  int $fileid patch panel port file ID
     * @return  JsonResponse
     *
     * @throws
     */
    public function deleteFile( int $fileid ){
        /** @var PatchPanelPortFileEntity $pppf */
        if( !( $pppf = D2EM::getRepository( PatchPanelPortFileEntity::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $path = 'files/'.$pppf->getPath();

        if( Storage::exists( $path ) && Storage::delete( $path ) ) {
            $pppf->getPatchPanelPort()->removePatchPanelPortFile( $pppf );
            D2EM::remove( $pppf );
            D2EM::flush();
            return response()->json( ['success'     => true,    'message' => 'File deleted' ] );
        } else {
            return response()->json( [ 'success'    => false,   'message' => 'Error: file could not be deleted' ] );

        }
    }

    /**
     * Delete a patch panel port file history
     *
     * @param  int $fileid patch panel port history file ID
     *
     * @return  JsonResponse
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \LaravelDoctrine\ORM\Facades\ORMInvalidArgumentException
     */
    public function deleteHistoryFile( int $fileid ){
        /** @var PatchPanelPortHistoryFileEntity $ppphf */
        if( !( $ppphf = D2EM::getRepository( PatchPanelPortHistoryFileEntity::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $path = 'files/'.$ppphf->getPath();

        if( Storage::exists( $path ) && Storage::delete( $path ) ) {
            $ppphf->getPatchPanelPortHistory()->removePatchPanelPortHistoryFile( $ppphf );
            D2EM::remove( $ppphf );
            D2EM::flush();
            return response()->json(    ['success'  => true,    'message' => 'File deleted' ] );
        }

        return response()->json(        [ 'success' => false,   'message' => 'Error: file could not be deleted' ] );
    }

    /**
     * Delete a patch panel port
     *
     * If the patch panel port has a duplex port then it will delete both ports.
     * Also deletes associated files and histories.
     *
     * @param  int $id ID of the patch panel port to delete
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function delete( int $id ) {
        /** @var PatchPanelPortEntity $ppp */
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        D2EM::getRepository( PatchPanelPortEntity::class )->delete( $ppp );

        AlertContainer::push( 'The patch Panel port has been successfully deleted.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }

    /**
     * Remove the linked port from the master and reset it as available.
     *
     * @param  int $id ID of the patch panel **master** port from which to split the slave
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function split( int $id ){
        /** @var PatchPanelPortEntity $ppp */
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
            abort( 404 );
        }

        if( !$ppp->hasSlavePort() ) {
            return response()->json( ['success' => false, 'message' => 'This patch panel port does not have any slave port.']) ;
        }

        $slavePort = $ppp->getDuplexSlavePort();

        $ppp->removeDuplexSlavePort( $slavePort );

        $ppp->setPrivateNotes(
            "### " . date('Y-m-d') . " - ". Auth::user()->getUsername() ."\n\nThis port had a slave port: "
            . $slavePort->getPrefix() . $slavePort->getNumber() . " which was split by " . Auth::user()->getUsername()
            . " on " . date('Y-m-d') . ".\n\n"
            . $ppp->getPrivateNotes()
        );

        $slavePort->resetPatchPanelPort();
        $slavePort->setPrivateNotes(
            "### " . date('Y-m-d') . " - ". Auth::user()->getUsername() ."\n\nThis port was a duplex slave port with "
            . $ppp->getPrefix() . $ppp->getNumber() . " and was split by " . Auth::user()->getUsername()
            . " on " . date('Y-m-d') . ".\n\n"
        );

        D2EM::flush();

        AlertContainer::push( 'The patch Panel port has been successfully split.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ] );
    }

    /**
     * Make a patch panel port file private
     *
     * @param  int $fileid patch panel port file ID
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function toggleFilePrivacy( int $fileid ){
        /** @var PatchPanelPortFileEntity $pppFile */
        if( !( $pppFile = D2EM::getRepository( PatchPanelPortFileEntity::class )->find( $fileid ) ) ) {
            abort( 404 );
        }

        $pppFile->setIsPrivate( !$pppFile->getIsPrivate() );
        D2EM::flush();

        return response()->json( [ 'success' => true, 'isPrivate' => $pppFile->getIsPrivate() ] );
    }

    /**
     * Upload a file to a patch panel port
     *
     * @param  int $id patch panel port ID
     * @param  Request $request instance of the current HTTP request
     * @param  FlysystemManager $filesystem instance of the file manager
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function uploadFile( Request $request, FlysystemManager $filesystem, int $id ): JsonResponse {
        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find($id) ) ) {
            abort(404);
        }

        if( !$request->hasFile('upl') ) {
            abort(400);
        }

        $file = $request->file('upl');

        $pppFile = new PatchPanelPortFileEntity;
        $pppFile->setPatchPanelPort(    $ppp );
        $pppFile->setStorageLocation(   hash('sha256', $ppp->getId() . '-' . $file->getClientOriginalName() ) );
        $pppFile->setName(              $file->getClientOriginalName() );
        $pppFile->setUploadedAt(        new \DateTime );
        $pppFile->setUploadedBy(        Auth::user()->getUsername() );

        $path = $pppFile->getPath();

        if( $filesystem->has( $path ) ) {
            return response()->json( [ 'success' => false, 'message' => 'File of the same name already exists for this port' ] );
        }

        $stream = fopen( $file->getRealPath(), 'r+' );
        if( $filesystem->writeStream( $path, $stream ) ) {

            $pppFile->setSize(  $filesystem->getSize($path) );
            $pppFile->setType(  $filesystem->getMimetype($path) );
            D2EM::persist(      $pppFile );

            $ppp->addPatchPanelPortFile( $pppFile );
            D2EM::flush();
            $resp = [ 'success' => true, 'message' => 'File uploaded and saved.', 'id' => $pppFile->getId() ];
        } else {
            $resp = [ 'success' => false, 'message' => 'Could not save file ti storage location' ];
        }

        fclose($stream);
        return response()->json($resp);
    }

    /**
     * Set the public and private notes of a patch panel
     *
     * @param   Request     $request    instance of the current HTTP request
     * @param   int         $id         The ID of the patch panel port to query
     *
     * @return  JsonResponse JSON customer object
     *
     * @throws
     */
    public function setNotes( Request $request, int $id ) : JsonResponse {

        if( !( $ppp = D2EM::getRepository( PatchPanelPortEntity::class )->find( $id ) ) ) {
            abort( 404, 'No such patch panel port' );
        }

        if( $request->input('notes', null) !== null ) {
            $ppp->setNotes( clean( $request->input( 'notes' ) ) );
        }

        if( $request->input('private_notes', null) !== null ) {
            $ppp->setPrivateNotes( clean( $request->input( 'private_notes' ) ) );
        }
        D2EM::flush();

        // we may also pass a new state for a physical interface with this request
        // (because we call this function from set connected / set ceased / etc)
        if( $request->input('pi_status') ) {
            if( $ppp->getSwitchPort() && ( $pi = $ppp->getSwitchPort()->getPhysicalInterface() ) ) {
                /** @var PhysicalInterfaceEntity $pi */
                $pi->setStatus( $request->input( 'pi_status' ) );
            }
            D2EM::flush();
        }

        return response()->json( [ 'success' => true ] );
    }
}