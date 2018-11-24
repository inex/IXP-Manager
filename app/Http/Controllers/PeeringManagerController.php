<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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
    BGPSessionData              as BGPSessionDataEntity,
    Customer                    as CustomerEntity,
    PeeringManager              as PeeringManagerEntity,
    Vlan                        as VlanEntity
};

use Illuminate\Http\{
    RedirectResponse,
    JsonResponse
};

use Illuminate\View\View;

use Illuminate\Http\{
    Request
};

use IXP\Http\Requests\{
    PeeringManagerRequest
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Auth, D2EM, DateTime, Former, Mail, Redirect;

use IXP\Mail\PeeringManager\RequestPeeringManager;

/**
 * PeeringManagerController Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringManagerController extends Controller
{

    /**
     * Display dashboard
     *
     * @return  View|RedirectResponse
     *
     * @throws
     */
    public function index() {

        if( config( 'ixp_fe.frontend.disabled.peering-manager', false ) ) {
            AlertContainer::push( 'The peering manager has been disabled.', Alert::DANGER );
            return Redirect::to('');
        }

        if( !Auth::getUser()->isCustUser() ) {
            AlertContainer::push( 'Only standard customer users can access the peering manager.', Alert::DANGER );
            return Redirect::to('');
        }

        $c      = Auth::getUser()->getCustomer();

        $vlans  = D2EM::getRepository( VlanEntity::class )->getPeeringManagerVLANs();

        $protos = [ 4, 6 ];

        $peers  = D2EM::getRepository( CustomerEntity::class )->getPeeringManagerArrayByType( $c, $vlans, $protos );

        /** @noinspection PhpUndefinedMethodInspection - need to sort D2EM::getRepository factory inspection */
        return view( 'peering-manager/index' )->with([
            'bilat'                         => $peers[ "bilat" ],
            'vlans'                         => $vlans,
            'protos'                        => $protos,
            'peers'                         => $peers[ "peers" ],
            'me'                            => $peers[ "me" ],
            'c'                             => $c,
            'custs'                         => $peers[ "custs" ],
            'potential'                     => $peers[ "potential" ],
            'potential_bilat'               => $peers[ "potential_bilat" ],
            'peered'                        => $peers[ "peered" ],
            'rejected'                      => $peers[ "rejected" ],
        ]);
    }


    /**
     * Display the form to send email
     *
     * @param  Request    $request        instance of the current HTTP request
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function formEmailFrag( Request $request ) {

        $success = true;
        $pp = $peer = $peeringManager = null;


        /** @var CustomerEntity $peer */
        if( !( $peer = D2EM::getRepository( CustomerEntity::class )->find( $request->input("peerid") ) ) ){
            $success = false;
        } else {

            if( $request->input( "form" ) == "email" ) {

                // potential peerings
                $pp = [];
                $count = 0;
                $cust = Auth::getUser()->getCustomer();

                foreach( $cust->getVirtualInterfaces() as $myvis ) {
                    foreach( $myvis->getVlanInterfaces() as $vli ) {
                        // does b member have one (or more than one)?
                        foreach( $peer->getVirtualInterfaces() as $pvis ) {
                            foreach( $pvis->getVlanInterfaces() as $pvli ) {
                                if( $vli->getVlan()->getId() == $pvli->getVlan()->getId() ) {
                                    $pp[ $count ][ 'my' ] = $vli;
                                    $pp[ $count ][ 'your' ] = $pvli;
                                    $count++;
                                }
                            }
                        }
                    }
                }

                //$f->getElement( 'message' )->setValue( $this->view->render( 'peering-manager/peering-request-message.phtml' ) );

                Former::populate( [
                    'to'             => $peer->getPeeringemail(),
                    'cc'             => $cust->getPeeringemail(),
                    'bcc'            => Auth::getUser()->getEmail(),
                    'subject'        => "[" . config( "identity.orgname" ) . "] Peering Request from " . $cust->getName() . " (ASN" . $cust->getAutsys() . ")",
                ] );

            } else {
                $peeringManager = $this->loadPeeringManager( Auth::getUser()->getCustomer(), $peer );
            }

        }

        $returnHTML = view('peering-manager/form-email')->with([
                'peer'                  => $peer,
                'pp'                    => $pp,
                'peeringManager'        => $peeringManager,
                'form'                  => $request->input("form") ?? "email",
            ])->render();


        return response()->json( ['success' => $success, 'htmlFrag' => $returnHTML ] );
    }


    /**
     * @param PeeringManagerRequest $r
     * @return \Illuminate\Http\JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function sendPeeringEmail( PeeringManagerRequest $r ){

        /** @var CustomerEntity $peer */
        if( !( $peer = D2EM::getRepository( CustomerEntity::class )->find( $r->input( 'peerid' ) ) ) ){
            abort( 404);
        }

        $mailable = new RequestPeeringManager( $peer, $r );


        $marksent = $r->input( "input-marksent" );
        $sendtome = $r->input( "input-sendtome" );

        try {
            if( !$marksent ){
                $mailable->checkIfSendable( $sendtome );
            }

        } catch( \Exception $e ) {
            return response()->json( [ 'error' => true, "message" => $e->getMessage() ] );
        }


        if( !$marksent ) {
            Mail::send( $mailable );
        }

        if( !$sendtome ) {

            // get this customer/peer peering manager table entry
            $pm = $this->loadPeeringManager( Auth::getUser()->getCustomer(), $peer );

            if( !( config( "ixp.peering_manager.testmode" ) ) || config( "ixp.peering_manager.testdate" ) ) {
                $pm->setEmailLastSent(          new DateTime() );
                $pm->setEmailsSent(  $pm->getEmailsSent() + 1 );
                $pm->setUpdated(                new DateTime() );
            }

            if( !( config( "ixp.peering_manager.testmode" ) ) || config( "ixp.peering_manager.testnote" ) ) {
                $pm->setNotes(
                    '### ' . date( 'Y-m-d' ) . " - " .Auth::getUser()->getUsername() . "\n\nPeering request " . ( $r->input( "marksent" ) ? 'marked ' : '' ) . "sent\n\n" . $pm->getNotes()
                );
            }

            D2EM::flush();
        }


        if( $sendtome ){
            $message = "Peering request sample sent to your own email address (" . Auth::getUser()->getEmail() . ").";
        } else if( $marksent ) {
            $message = "Peering request marked as sent in your Peering Manager.";
        } else {
            $message = "Peering request sent to ". $peer->getName() . " Peering Team.";
        }


        return response()->json( [ 'error' => false, "message" => $message ] );

    }

    /**
     * Set a note for a dedicated peering manager
     *
     * @param Request $r
     *
     * @return PeeringManagerEntity
     *
     * @throws
     */
    public function peeringNotes( Request $r ){

        /** @var CustomerEntity $peer */
        if( !( $peer = D2EM::getRepository( CustomerEntity::class )->find( $r->input( 'peerid' ) ) ) ){
            return response()->json( [ 'error' => true, "message" => "Peering manager unknown" ] );
        }

        $pm = $this->loadPeeringManager( Auth::getUser()->getCustomer(), $peer );

        $pm->setUpdated( new DateTime() );

        if( trim( stripslashes( $r->input( 'notes' ) ) ) ) {
            $pm->setNotes( trim( stripslashes( $r->input( 'notes' ) ) ) );
        }


        D2EM::flush();

        return response()->json( [ 'error' => false, "message" => "Peering notes updated for " . $peer->getName() ] );

    }


    /**
     * Mark the peering manager as "peered" or "rejected"
     *
     * @param integer   $custid
     * @param string    $status
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function markPeering( $custid, $status ){

        /** @var CustomerEntity $peer */
        if( !( $peer = D2EM::getRepository( CustomerEntity::class )->find( $custid ) ) ){
            abort(404);
        }

        $pm = $this->loadPeeringManager( Auth::getUser()->getCustomer(), $peer );

        if( $status == "peered" ) {
            $pm->setPeered( $pm->getPeered() ? false : true );
            if( $pm->getPeered() && $pm->getRejected() ){
                $pm->setRejected( false );
            }
        } else{
            $pm->setRejected( $pm->getRejected() ? false : true );
            if( $pm->getPeered() && $pm->getRejected() ){
                $pm->setPeered( false );
            }

        }

        D2EM::flush();

        if( $status == "peered" ) {
            AlertContainer::push( "Peered flag " . ( $pm->getPeered() ? 'set' : 'cleared' ) . " for " . $peer->getName() . "." , Alert::SUCCESS );
        } else {
            AlertContainer::push( "Ignored / rejected flag " . ( $pm->getRejected() ? 'set' : 'cleared' ) . " for "  . $peer->getName() . "." , Alert::SUCCESS );
        }


        return Redirect::to( route( "peering-manager@index"  ) );
    }


    /**
     * Utility function to load a PeeringManager entity and initialise one if not found
     *
     * @param CustomerEntity $cust
     * @param CustomerEntity $peer
     *
     * @return PeeringManagerEntity
     *
     * @throws
     */
    private function loadPeeringManager( CustomerEntity $cust, CustomerEntity $peer ){

        /** @var $pm PeeringManagerEntity */
        if( !( $pm = D2EM::getRepository( PeeringManagerEntity::class )->findOneBy( [ 'Customer' => $cust, 'Peer' => $peer ] ) ) ) {
            $pm = new PeeringManagerEntity;
            D2EM::persist( $pm );

            $pm->setCustomer(   $cust );
            $pm->setPeer(       $peer );
            $pm->setCreated(    new \DateTime );
            $pm->setNotes(      '' );
            $pm->setPeered(     false );
            $pm->setRejected(   false );

            D2EM::flush();
        }

        return $pm;
    }



}