<?php

namespace IXP\Listeners\Layer2Address;

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

use Mail;

use Illuminate\Contracts\Queue\ShouldQueue;

use IXP\Events\Layer2Address\{
    Added   as Layer2AddressAddedEvent,
    Deleted as Layer2AddressDeletedEvent
};

use IXP\Mail\Layer2Address\ChangedMail as Layer2AddressChangedMail;

/**
 * Changed Listener
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Layer2Address\Listeners
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Changed implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(){}

    /**
     * Handle the event.
     *
     * @param  Layer2AddressAddedEvent|Layer2AddressDeletedEvent  $e
     *
     * @return void
     */
    public function handle( $e ): void
    {
        if( !( config( 'ixp_fe.layer2-addresses.email_on_superuser_change' ) || config( 'ixp_fe.layer2-addresses.email_on_customer_change' ) ) ) {
            return;
        }

        if( !config( 'ixp_fe.layer2-addresses.email_on_superuser_change' ) && $e->user->isSuperUser() ) {
            return;
        }

        Mail::to( config( 'ixp_fe.layer2-addresses.email_on_change_dest' ) )->send( new Layer2AddressChangedMail( $e ) );
    }
}