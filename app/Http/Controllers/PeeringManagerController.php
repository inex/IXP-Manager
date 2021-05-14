<?php

namespace IXP\Http\Controllers;

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

use Auth, Former, Mail, Redirect;

use IXP\Models\{
    Aggregators\CustomerAggregator,
    Customer,
    PeeringManager,
    User,
    Vlan
};

use Exception;
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

use IXP\Mail\PeeringManager\RequestPeeringManager;

/**
 * PeeringManagerController Controller
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
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
    public function index(): View|RedirectResponse
    {
        if( config( 'ixp_fe.frontend.disabled.peering-manager', false ) ) {
            AlertContainer::push( 'The peering manager has been disabled.', Alert::DANGER );
            return Redirect::to('');
        }

        $protos = [ 4, 6 ];
        $c      = Customer::find( Auth::getUser()->custid );
        $vlans  = Vlan::peeringManager()->orderBy( 'number' )->get();
        $peers  = CustomerAggregator::getPeeringManagerArrayByType( $c , $vlans, $protos ) ?? [];

        if( !count( $peers ) ) {
            AlertContainer::push( 'No peers have been found for the peering manager. Please see <a href="'
                . 'https://github.com/inex/IXP-Manager/wiki/Peering-Manager">these instructions</a>'
                . ' / ensure your database is populating with peering information.', Alert::DANGER );
            return redirect( '' );
        }

        return view( 'peering-manager/index' )->with([
            'bilat'             => $peers[ "bilat" ],
            'vlans'             => $vlans,
            'protos'            => $protos,
            'peers'             => $peers[ "peers" ],
            'me'                => $peers[ "me" ],
            'c'                 => $c,
            'custs'             => $peers[ "custs" ],
            'potential'         => $peers[ "potential" ],
            'potential_bilat'   => $peers[ "potential_bilat" ],
            'peered'            => $peers[ "peered" ],
            'rejected'          => $peers[ "rejected" ],
        ]);
    }

    /**
     * Display the form to send email
     *
     * @param  Request    $r        instance of the current HTTP request
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function formEmailFrag( Request $r ): JsonResponse
    {
        $success = true;
        $pp = $peer = $peeringManager = false;

        if( !( $peer = Customer::find( $r->peerid ) ) ){
            $success = false;
        } elseif( $r->form === "email" ) {
            // potential peerings
            $pp     = [];
            $count  = 0;
            $cust   = Customer::find( Auth::getUser()->custid );

            foreach( $cust->virtualInterfaces as $myvis ) {
                foreach( $myvis->vlanInterfaces as $vli ) {
                    // does b member have one (or more than one)?
                    foreach( $peer->virtualInterfaces as $pvis ) {
                        foreach( $pvis->vlanInterfaces as $pvli ) {
                            if( $vli->vlan->id === $pvli->vlan->id ) {
                                $pp[ $count ][ 'my' ] = $vli;
                                $pp[ $count ][ 'your' ] = $pvli;
                                $count++;
                            }
                        }
                    }
                }
            }

            Former::populate( [
                'to'             => $peer->peeringemail,
                'cc'             => $cust->peeringemail,
                'bcc'            => User::find( Auth::id() )->email,
                'subject'        => "[" . config( "identity.orgname" ) . "] Peering Request from " . $cust->name . " (ASN" . $cust->autsys . ")",
            ] );
        } else {
            $peeringManager = $this->loadPeeringManager( Customer::find( Auth::getUser()->custid ), $peer );
        }

        $returnHTML = view('peering-manager/form-email')->with([
                'peer'                  => $peer,
                'pp'                    => $pp,
                'peeringManager'        => $peeringManager,
                'form'                  => $r->form ?? "email",
            ])->render();

        return response()->json( ['success' => $success, 'htmlFrag' => $returnHTML ] );
    }

    /**
     * @param PeeringManagerRequest $r
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function sendPeeringEmail( PeeringManagerRequest $r ) : JsonResponse
    {
        $peer       = Customer::findOrFail( $r->peerid );
        $mailable   = new RequestPeeringManager( $peer, $r );
        $marksent   = $r->marksent;
        $sendtome   = $r->sendtome;
        $user       = User::find( Auth::id() );
        $cust       = Customer::find( Auth::getUser()->custid );

        try {
            if( !$marksent ){
                $mailable->checkIfSendable( $sendtome );
            }

        } catch( Exception $e ) {
            return response()->json( [ 'error' => true, "message" => $e->getMessage() ] );
        }

        if( !$marksent ) {
            Mail::send( $mailable );
        }

        if( !$sendtome ) {
            // get this customer/peer peering manager table entry
            $pm = $this->loadPeeringManager( $cust , $peer );

            if( !( config( "ixp.peering_manager.testmode" ) ) || config( "ixp.peering_manager.testdate" ) ) {
                $pm->email_last_sent = now();
                ++$pm->emails_sent;
            }

            if( !( config( "ixp.peering_manager.testmode" ) ) || config( "ixp.peering_manager.testnote" ) ) {
                $pm->notes = '### ' . date( 'Y-m-d' ) . " - " . $user->username . "\n\nPeering request " . ( $r->marksent ? 'marked ' : '' ) . "sent\n\n" . $pm->notes;
            }

            $pm->save();
        }

        if( $sendtome ){
            $message = "Peering request sample sent to your own email address (" . $user->email . ").";
        } else if( $marksent ) {
            $message = "Peering request marked as sent in your Peering Manager.";
        } else {
            $message = "Peering request sent to ". $peer->name . " Peering Team.";
        }

        return response()->json( [ 'error' => false, "message" => $message ] );
    }

    /**
     * Set a note for a dedicated peering manager
     *
     * @param Request $r
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function peeringNotes( Request $r ): JsonResponse
    {
        $peer   = Customer::findOrFail( $r->peerid );
        $cust   = Customer::find( Auth::getUser()->custid );
        $pm     = $this->loadPeeringManager( $cust , $peer );

        if( trim( stripslashes( $r->notes ) ) ) {
            $pm->notes = trim( stripslashes( $r->notes ) );
            $pm->save();
        }
        return response()->json( [ 'error' => false, "message" => "Peering notes updated for " . $peer->name ] );
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
    public function markPeering( int $custid, string $status ): RedirectResponse
    {
        $peer   = Customer::findOrFail( $custid );
        $pm     = $this->loadPeeringManager( Customer::find( Auth::getUser()->custid ), $peer );

        if( $status === "peered" ) {
            $pm->peered = !$pm->peered;
            if( $pm->peered && $pm->rejected ){
                $pm->rejected = false;
            }
        } else{
            $pm->rejected = !$pm->rejected;
            if( $pm->peered && $pm->rejected ){
                $pm->peered = false;
            }
        }

        $pm->save();

        if( $status === "peered" ) {
            AlertContainer::push( "Peered flag " . ( $pm->peered ? 'set' : 'cleared' ) . " for " . $peer->name . "." , Alert::SUCCESS );
        } else {
            AlertContainer::push( "Ignored / rejected flag " . ( $pm->rejected ? 'set' : 'cleared' ) . " for "  . $peer->name . "." , Alert::SUCCESS );
        }

        return Redirect::to( route( "peering-manager@index"  ) );
    }

    /**
     * Utility function to load a PeeringManager entity and initialise one if not found
     *
     * @param Customer $cust
     * @param Customer $peer
     *
     * @return PeeringManager
     *
     * @throws
     */
    private function loadPeeringManager( Customer $cust, Customer $peer ): PeeringManager
    {
        $pm = PeeringManager::where( 'custid' , $cust->id )->where( 'peerid' , $peer->id )->first();

        if( !$pm ){
            $pm = PeeringManager::create(
                [
                    'note'      => '',
                    'peered'    => false,
                    'rejected'  => false,
                ]
            );

            $pm->custid =   $cust->id;
            $pm->peerid =   $peer->id;
            $pm->save();
            return $pm;
        }

        return $pm;
    }
}