<?php

namespace IXP\Listeners\Customer\Note;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use D2EM, Mail;

use IXP\Events\Customer\Note\{
    Added   as CustomerNoteAddedEvent
};

use Entities\{
    User as UserEntity
};


use IXP\Mail\Customer\Note\Changed as CustomerNoteChangedMailable;

class EmailOnChange
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CustomerNoteAddedEvent $e
     * @return void
     */
    public function handle( $e )
    {
        // get admin users
        $users = D2EM::getRepository( UserEntity::class )->findBy( [ 'privs' => UserEntity::AUTH_SUPERUSER ] );

        foreach( $users as $user ) {
            /** @var UserEntity $user */
            if( !$user->getPreference( "customer-notes.notify" ) ) {
                if( !$user->getPreference( "customer-notes.{$e->getCustomer()->getId()}.notify" ) ) {
                    if( !$e->getOldNote() ) // adding
                        continue;

                    if( !$user->getPreference( "customer-notes.watching.{$e->getOldNote()->getId()}" ) )
                        continue;
                }
            }
            else if( $user->getPreference( "customer-notes.notify" ) == "none" )
                continue;


            Mail::to( $user->getContact()->getEmail() )->send( new CustomerNoteChangedMailable( $e->getOldNote(), $e->getNote(), $e->getCustomer(), $user, $e->getType() ) );

        }

    }
}
