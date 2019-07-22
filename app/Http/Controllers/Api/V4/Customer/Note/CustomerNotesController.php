<?php

namespace IXP\Http\Controllers\Api\V4\Customer\Note;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM;

use IXP\Exceptions\GeneralException;

use IXP\Http\Controllers\Controller;

use Illuminate\Http\{
    JsonResponse,
    Request
};

use Entities\{
    Customer as CustomerEntity,
    CustomerNote as CustomerNoteEntity
};

use IXP\Events\Customer\Note\{
    Added       as CustomerNoteAddedEvent,
    Deleted     as CustomerNoteDeletedEvent,
    Edited      as CustomerNoteUpdatedEvent
};

/**
 * Customer Note API v4 Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customers
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerNotesController extends Controller
{
    /**
     * Add/Edit note for a customer
     *
     * @param Request $request instance of the current HTTP request
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function add( Request $request ): JsonResponse
    {

        /** @var CustomerEntity $c */
        if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) ) ) {
            abort( 404, 'Customer not found.' );
        }

        /** @var CustomerNoteEntity $old */
        if( $request->input( 'noteid' ) ) {
            if( !( $n = D2EM::getRepository( CustomerNoteEntity::class )->find( $request->input( 'noteid' ) ) ) ) {
                abort( 404, 'Note not found.' );
            }
            $old = clone( $n );
        } else {
            $n = new CustomerNoteEntity();
            $old = null;
        }

        $n->setTitle(   $request->input( 'title' ) );
        $n->setNote(    $request->input( 'note' ) );
        $n->setPrivate( $request->input( 'public' ) == 'makePublic' ? false : true );
        $n->setUpdated( new \DateTime );

        if( $old === null ) {
            // new note:
            $n->setCreated( $n->getUpdated() );
            $n->setCustomer( $c );
            D2EM::persist( $n );
        }

        // update the user's notes last read so he won't be told his own is new
        Auth::getUser()->setPreference( "customer-notes.{$c->getId()}.last_read", time() );

        D2EM::flush();

        if( $old === null ) {
            event( new CustomerNoteAddedEvent( null, $n, Auth::getUser() ) );
        } else if( $old->getTitle() != $n->getTitle() || $old->getNote() != $n->getNote() ) {
            event( new CustomerNoteUpdatedEvent ( $old, $n, Auth::getUser()  ) );
        }

        return response()->json( [ 'noteid' => $n->getId() ] );
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
    public function get( int $id = null )
    {
        if( !( $n = D2EM::getRepository( CustomerNoteEntity::class )->find( $id ) ) ) {
            abort( 404, 'Note not found.' );
        }

        // these if's could be joined with '&&' but are separated for readability:
        if( !Auth::getUser()->isSuperUser() ) {
            if( $n->getCustomer() != Auth::getUser()->getCustomer() || $n->getPrivate() ) {
                abort( 403, 'Insufficient Permissions.' );
            }
        }

        $nArray = $n->toArray();
        /** @noinspection PhpUndefinedMethodInspection */
        $nArray['created'] = $nArray['created']->format( 'Y-m-d H:i' );
        /** @noinspection PhpUndefinedMethodInspection */
        $nArray['updated'] = $nArray['updated'] ? $nArray['updated']->format( 'Y-m-d H:i' ) : null;

        return response()->json( [ 'note' => $nArray ] );
    }

    /**
     * Delete a customer note
     *
     * @param int $id ID of the customer note
     * @return JsonResponse
     *
     * @throws
     */
    public function delete( int $id = null ) : JsonResponse
    {
        if( !( $n = D2EM::getRepository( CustomerNoteEntity::class )->find( $id ) ) ) {
            abort( 404, 'Note not found.' );
        }

        /** @var CustomerNoteEntity $on */
        $on = clone( $n );
        D2EM::remove( $n );
        D2EM::flush();
        event( new CustomerNoteDeletedEvent ( null , $on, Auth::getUser() ) );

        return response()->json( [ 'noteid' => $on->getId() ] );
    }


    /**
     * Update the last read for this user
     *
     * @param int|null $id
     * @return JsonResponse
     * @throws
     */
    public function ping( int $id = null ): JsonResponse
    {

        if( Auth::getUser()->isSuperUser() ) {
            if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ) {
                abort( 404, 'Customer not found.' );
            }
        } else {
            $c = Auth::getUser()->getCustomer();
        }

        // update the last read for this user / customer combination
        Auth::getUser()->setPreference( "customer-notes.{$c->getId()}.last_read", time() );
        D2EM::flush();

        return response()->json( true );
    }


    /**
     * @param int $id  Customer ID
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function notifyToggleCustomer( int $id ): JsonResponse
    {
        return  $this->notifyToggle( $id, null );
    }

    /**
     * @param int $id Note ID
     *
     * @return JsonResponse
     *
     * @throws
     */
    public function notifyToggleNote( int $id ): JsonResponse
    {
        return  $this->notifyToggle( null, $id );
    }

    /**
     *
     * @param int|null $custid
     * @param int|null $noteId
     *
     * @return JsonResponse
     *
     * @throws
     */
    private function notifyToggle( int $custid = null, int $noteId = null ): JsonResponse
    {
        if( $custid ) {
            $id   = $custid;
            $name = sprintf( "customer-notes.%d.notify", $id );
            $value = 'all';
        } else if( $noteId ) {
            $id = $noteId;
            $name = sprintf( "customer-notes.watching.%d", $id );
            $value = 1;
        } else {
            throw new GeneralException('Coding error');
        }

        // Toggles customer notes notification preference
        if( isset( $id ) && is_numeric( $id ) ) {
            if( !Auth::getUser()->getPreference( $name ) ) {
                Auth::getUser()->setPreference( $name, $value );
            } else {
                Auth::getUser()->deletePreference( $name );
            }
            D2EM::flush();
        }

        return response()->json( true );
    }
}