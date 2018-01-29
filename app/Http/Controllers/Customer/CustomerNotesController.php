<?php

namespace IXP\Http\Controllers\Customer;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, D2EM , DateTime, Exception, Mail, Redirect, Former;

use Intervention\Image\ImageManagerStatic as Image;

use Illuminate\View\View;
use IXP\Http\Controllers\Controller;
use Illuminate\Http\{
    RedirectResponse,
    JsonResponse,
    Request
};


use Entities\{
    Customer as CustomerEntity,
    CustomerNote as CustomerNoteEntity,
    User as UserEntity
};

use IXP\Mail\Customer\Email as EmailCustomer;


use IXP\Http\Requests\{
    StoreCustomer
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Customer Notes Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Interfaces
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerNotesController extends Controller {


    /**
     * Add/Edit note for a customer
     *
     * @param Request $request instance of the current HTTP request
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function add( Request $request ) : JsonResponse{
        $isEdit = false;
        $old = false;

        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) ) ){
            abort( 404);
        }

        if( $request->input( 'noteid' ) ) {
            $isEdit = true;
            $n = D2EM::getRepository( CustomerNoteEntity::class )->find( $request->input( 'noteid' ) );
            $old = clone( $n );
        } else {
            $n = new CustomerNoteEntity();
        }

        if( $c && $n ) {
            $n->setTitle(   $request->input( 'title' ) );
            $n->setNote(    $request->input( 'note' ) );
            $n->setPrivate( $request->input( 'public' ) == 'makePublic' ? false : true );
            $n->setUpdated( new \DateTime );

            if( !$isEdit ) {
                $n->setCreated( $n->getUpdated() );
                $n->setCustomer( $c );
                D2EM::persist( $n );
            }

            // update the user's notes last read so he won't be told his own is new
            Auth::getUser()->setPreference( "customer-notes.{$request->input( 'custid' )}.last_read", time() );

            D2EM::flush();

            if( !$old || $old->getTitle() != $n->getTitle() || $old->getNote() != $n->getNote() || $old->getPrivate() != $n->getPrivate() ){
                $this->sendNotifications( $old , $n );
            }

            $r[ 'error' ] = false;
            $r[ 'noteid' ] = $n->getId();
        } else {
            $r['error'] = "Invalid customer / note specified.";
        }

        return response()->json( [ 'rep' => $r ] );
    }


    public function notifyToggleByCust( int $custid = null ){

        return  $this->notifyToggle( $custid, null );
    }

    public function notifyToggleByNote( int $id = null ){
        return  $this->notifyToggle( null, $id );
    }

    private function notifyToggle( int $custid = null, int $noteId = null ) : JsonResponse{

        if( $custid ) {
            $id   = $custid;
            $name = sprintf( "customer-notes.%d.notify", $id );
            $value = 'all';
        } else if( $noteId ) {
            $id = $noteId;
            $name = sprintf( "customer-notes.watching.%d", $id );
            $value = 1;
        }


        // Toggles customer notes notification preference
        if( isset( $id ) && is_numeric( $id ) ) {
            if( !Auth::getUser()->getPreference( $name ) ){
                Auth::getUser()->setPreference( $name, $value );
            } else {
                Auth::getUser()->deletePreference( $name );
            }
            D2EM::flush();
        }

        return response()->json( true );
    }

    /**
     * Get a customer note
     *
     * @param int $id ID of the customer note
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function get( int $id = null ){
        $r = [ 'error' => true ];

        if( $note = D2EM::getRepository( CustomerNoteEntity::class )->find( $id ) ) {
            if( Auth::getUser()->getPrivs() != UserEntity::AUTH_SUPERUSER && ( $note->getCustomer() != Auth::getUser()->getCustomer() || $note->getPrivate() ) ) {
                abort( 403 );
            } else {
                $r = $note->toArray();
                $r['created'] = $r['created']->format( 'Y-m-d H:i' );
                $r['error'] = false;
            }
        }

        return response()->json( $r );
    }

    /**
     * Delete a customer note
     *
     * Send A notification email
     *
     * @param int $id ID of the customer note
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function delete( int $id = null ) : JsonResponse{
        $error = true;
dd($id);
        if( $note = D2EM::getRepository( CustomerNoteEntity::class )->find( $id ) ) {
            $old = clone( $note );
            D2EM::remove( $note );
            D2EM::flush();
            $this->sendNotifications( $old, false );
            $error = false;
        }

        return response()->json( ['error' => $error ] );
    }


    public function ping( int $id = null ) : JsonResponse {
        if( Auth::getUser()->getPrivs() == UserEntity::AUTH_SUPERUSER ){
            $custid = $id;
        } else {
            $custid = Auth::getUser()->getCustomer()->getId();
        }

        // update the last read for this user / customer combination
        if( is_numeric( $custid ) ) {
            Auth::getUser()->setPreference( "customer-notes.{$custid}.last_read", time() );
            D2EM::flush();
        }

        return response()->json( true );
    }

    /**
     * Send email notification, We can work out the action as follows:
     *
     *  * old == false, new != false: ADD
     *  * old != false, new == false: DELETE
     *  * old != false, new != false: EDIT
     *
     * @param string $old Old Note
     * @param string $new New note
     *
     * @throws Exception
     */
    private function sendNotifications( $old = null , $new = null ){
        // get admin users
        $users = D2EM::getRepository( UserEntity::class )->findBy( [ 'privs' => UserEntity::AUTH_SUPERUSER ] );


        if( $old ){
            $c = $cust = $old->getCustomer();
        } else if( $new ) {
            $c = $cust = $new->getCustomer();
        } else {
            throw new Exception( "Customer note is missing." );
        }


        $mailable = new EmailCustomer( $cust );
        $mailable->subject(  '[IXP Notes] [' . $c->getName() . '] ' . ( $old ? $old->getTitle() : $new->getTitle() ) );
        $mailable->from( config('identity.email'), config('identity.name') );
        $mailable->view( "customer/emails/notification" )->with( ['old' => $old, 'new' => $new , 'cust' => $c, 'user' => Auth::getUser() ] );


        foreach( $users as $user ) {
            if( !$user->getPreference( "customer-notes.notify" ) ) {
                if( !$user->getPreference( "customer-notes.{$cust->getId()}.notify" ) ) {
                    if( !$old ) // adding
                        continue;

                    if( !$user->getPreference( "customer-notes.watching.{$old->getId()}" ) )
                        continue;
                }
            }
            else if( $user->getPreference( "customer-notes.notify" ) == "none" )
                continue;

            try {
                //$mailable->to( $user->getContact()->getEmail(), $user->getContact()->getName() );
                $mailable->to( 'yann@islandbridgenetworks.ie', 'yann' );
                Mail::send( $mailable ) ;

            } catch( Exception $e ) {
                AlertContainer::push( $e->getMessage(), Alert::DANGER );
            }
        }
    }


    public function readAll() : RedirectResponse{
        $lastReads = Auth::getUser()->getAssocPreference( 'customer-notes' )[0];
        foreach( $lastReads as $id => $data ) {
            if( is_numeric( $id ) )
                Auth::getUser()->deletePreference( "customer-notes.$id.last_read" );
        }

        Auth::getUser()->setPreference( 'customer-notes.read_upto', time() );
        D2EM::flush();

        return Redirect::to( '/customer/unread-notes' );
    }
}

