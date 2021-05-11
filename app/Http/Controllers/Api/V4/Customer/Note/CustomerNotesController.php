<?php

namespace IXP\Http\Controllers\Api\V4\Customer\Note;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth;

use Exception;
use Illuminate\Support\Facades\Log;
use IXP\Exceptions\GeneralException;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Customer,
    CustomerNote
};

use Illuminate\Http\{
    JsonResponse,
    Request
};

use IXP\Events\Customer\Note\{
    Created     as CustomerNoteCreatedEvent,
    Deleted     as CustomerNoteDeletedEvent,
    Edited      as CustomerNoteUpdatedEvent
};

/**
 * Customer Note API v4 Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerNotesController extends Controller
{
    /**
     * Create note for a customer
     *
     * @param Request   $r      instance of the current HTTP request
     * @param Customer  $cust
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function create( Request $r, Customer $cust ): JsonResponse
    {
        $user   = Auth::getUser();
        $cn     = new CustomerNote;

        $cn->title          =   $r->title;
        $cn->note           =   $r->note ;
        $cn->private        =   $r->public ? false : true;
        $cn->customer_id    =   $cust->id;
        $cn->save();

        // update the user's notes last read so he won't be told his own is new
        $prefs = $user->prefs;
        $prefs[ 'notes' ][ 'last_read' ][ $cust->id ] = now()->format( 'Y-m-d H:i:s' );
        $user->prefs = $prefs;
        $user->save();

        event( new CustomerNoteCreatedEvent( null, $cn, $user ) );

        return response()->json( [ 'noteid' => $cn->id ] );
    }

    /**
     * Update note for a customer
     *
     * @param  Request  $r  instance of the current HTTP request
     * @param  CustomerNote  $cn
     *
     * @return JsonResponse
     *
     * @throws GeneralException
     */
    public function update( Request $r, CustomerNote $cn ): JsonResponse
    {
        $user = Auth::getUser();
        $old = clone( $cn );

        $cn->title   =   $r->title;
        $cn->note    =   $r->note ;
        $cn->private =   $r->public ? false : true;
        $cn->save();

        // update the user's notes last read so he won't be told his own is new
        $prefs = $user->prefs;
        $prefs[ 'notes' ][ 'last_read' ][ $cn->customer_id ] = now()->format( 'Y-m-d H:i:s' );
        $user->prefs = $prefs;
        $user->save();

        if( $old->title !== $cn->title || $old->note !== $cn->note ) {
            event( new CustomerNoteUpdatedEvent( $old, $cn, $user  ) );
        }

        return response()->json( [ 'noteid' => $cn->id ] );
    }

    /**
     * Get a customer note
     *
     * @param CustomerNote $cn customer note
     *
     * @return JsonResponse
     */
    public function get( CustomerNote $cn ): JsonResponse
    {
        // these if's could be joined with '&&' but are separated for readability:
        if( !Auth::getUser()->isSuperUser() ) {
            if( $cn->private || $cn->customer_id !== Auth::getUser()->custid ) {
                abort( 403, 'Insufficient Permissions.' );
            }
        }

        $note = $cn->toArray();
        $note[ 'note_parsedown' ] = parsedown( $cn->note );
        $note[ 'created_at' ] = $cn->created_at->format( 'Y-m-d H:i:s' );
        
        return response()->json( [ 'note' => $note ] );
    }

    /**
     * Delete a customer note
     *
     * @param  CustomerNote  $cn  customer note
     *
     * @return JsonResponse
     *
     * @throws GeneralException|Exception
     */
    public function delete( CustomerNote $cn ) : JsonResponse
    {
        $on = clone( $cn );
        $cn->delete();
        event( new CustomerNoteDeletedEvent ( null , $on, Auth::getUser() ) );
        return response()->json( [ 'noteid' => $on->id ] );
    }

    /**
     * Update the last read for this user
     *
     * @param Customer|null $c
     *
     * @return JsonResponse
     */
    public function ping( Customer $c = null ): JsonResponse
    {
        $u = Auth::getUser();
        if( !$u->isSuperUser() ) {
            $c = Auth::getUser()->customer;
        }

        // update the last read for this user / customer combination
        $prefs = $u->prefs;
        $prefs[ "notes" ][ "last_read" ][ $c->id ] = now()->format( "Y-m-d H:i:s" );
        $u->prefs = $prefs;
        $u->save();

        return response()->json( true );
    }

    /**
     * Watch/Unwatch all notes for a customer
     *
     * @param Customer $cust  Customer
     *
     * @return JsonResponse
     */
    public function notifyToggleCustomer( Customer $cust ): JsonResponse
    {
        return  $this->notifyToggle( $cust, null );
    }

    /**
     * Watch/Unwatch a note
     *
     * @param CustomerNote $cn
     *
     * @return JsonResponse
     */
    public function notifyToggleNote( CustomerNote $cn ): JsonResponse
    {
        return  $this->notifyToggle( null, $cn );
    }

    /**
     * Watch/Unwatch a note or All notes for a customer
     *
     * @param Customer|null     $cust
     * @param CustomerNote|null $cn
     *
     * @return JsonResponse
     */
    private function notifyToggle( Customer $cust = null, CustomerNote $cn = null ): JsonResponse
    {
        $user   = Auth::getUser();
        $prefs  = $user->prefs;

        if( $cust ){
            $result = 'Watch All';
            $index  = 'customer_watching';
            $id     = $cust->id;
        } else {
            $result = 'Watch';
            $index  = 'note_watching';
            $id     = $cn->id;
        }

        if( isset( $prefs[ 'notes' ][ $index ][ $id ] ) ){
            // if exist we delete the entry to unwatch the customer
            unset( $prefs[ 'notes' ][ $index ][ $id ] );
        } else {
            // if doesnt exist we create the entry to watch the customer
            $prefs[ 'notes' ][ $index ][ $id ] = now()->format( 'Y-m-d H:i:s' );
            $result = $cust ? 'Unwatch All' : 'Unwatch';
        }

        $user->prefs = $prefs;
        $user->save();

        return response()->json( $result );
    }
}