<?php

namespace IXP\Listeners\Customer\Note;

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
use Mail;

use Illuminate\Events\Dispatcher;

use IXP\Models\{
    CustomerToUser,
    User
};

use IXP\Mail\Customer\Note\Changed as CustomerNoteChangedMailable;

use IXP\Events\Customer\Note\{
    Changed as NoteChangedEvent,
    Created,
    Edited,
    Deleted};

/**
 * EmailOnChange Listener
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Listener\Customer\Note
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class EmailOnChange
{
    /**
     * Handle customer note added
     *
     * @param $event
     *
     * @return void
     */
    public function onCreatedNote( $event ) : void
    {
        $this->handle( $event );
    }

    /**
     * Handle customer note edited
     *
     * @param $event
     *
     * @return void
     */
    public function onEditedNote( $event ): void
    {
        $this->handle( $event );
    }

    /**
     * Handle customer note deleted
     *
     * @param $event
     *
     * @return void
     */
    public function onDeletedNote( $event ): void
    {
        $this->handle( $event );
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     */
    public function subscribe( Dispatcher $events ): void
    {
        $events->listen(
            Created::class,
            'IXP\Listeners\Customer\Note\EmailOnChange@onCreatedNote'
        );

        $events->listen(
            Edited::class,
            'IXP\Listeners\Customer\Note\EmailOnChange@onEditedNote'
        );

        $events->listen(
            Deleted::class,
            'IXP\Listeners\Customer\Note\EmailOnChange@onDeletedNote'
        );
    }


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * Handle the event.
     *
     * @param  NoteChangedEvent $e
     *
     * @return void
     */
    public function handle( $e ): void
    {
        if( config( 'ixp_fe.customer.notes.only_send_to' ) ) {
            $to = [ config( 'ixp_fe.customer.notes.only_send_to' ) ];
        } else {
            // get admin users
            $c2us = CustomerToUser::from( 'customer_to_users AS c2u' )
                ->leftJoin( 'user AS u', 'u.id', 'c2u.user_id' )
                ->where( 'c2u.privs', User::AUTH_SUPERUSER )
                ->where( 'u.disabled', false )
                ->get();
            $to = [];

            foreach( $c2us as $c2u ) {
                $user = $c2u->user;/** @var $user User */
                if( isset( $user->prefs[ 'notes' ][ 'global_notifs' ] ) && $user->prefs[ 'notes' ][ 'global_notifs' ] === 'none' ){
                    continue;
                }

                if( !$user->email || filter_var( $user->email , FILTER_VALIDATE_EMAIL ) === false ) {
                    continue;
                }

                if( !isset( $user->prefs[ 'notes' ][ 'global_notifs' ] ) || $user->prefs[ 'notes' ][ 'global_notifs' ] === 'default' || $user->prefs[ 'notes' ][ 'global_notifs' ] === 'all' ) {
                    $to[] = [ 'name' => $user->username, 'email' => $user->email ];
                    continue;
                }

                // watching a whole customer
                if( isset( $user->prefs[ 'notes' ][ 'customer_watching' ][ $e->customer()->id ] ) ) {
                    $to[] = [ 'name' => $user->username, 'email' => $user->email ];
                    continue;
                }

                // watching a specific note: customer-notes.watching.{note id}
                if( isset( $user->prefs[ 'notes' ][ 'note_watching' ][ $e->eitherNote()->id ] ) ) {
                    $to[] = [ 'name' => $user->username, 'email' => $user->email ];
                    continue;
                }
                // so, skip this user then.
            }
        }

        if( count( $to ) ) {
            Mail::to( $to )->send( new CustomerNoteChangedMailable( $e ) );
        }
    }
}