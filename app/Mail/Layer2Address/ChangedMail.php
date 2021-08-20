<?php

namespace IXP\Mail\Layer2Address;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use IXP\Events\Layer2Address\{
    Added   as Layer2AddressAddedEvent,
    Deleted as Layer2AddressDeletedEvent
};

/**
 * Mailable for Layer2Address
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yanm Robin       <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Layer2AddressAddedEvent|Layer2AddressDeletedEvent
     */
    public $event;

    /**
     * Create a new message instance.
     *
     * @param Layer2AddressAddedEvent|Layer2AddressDeletedEvent $e
     */
    public function __construct(  $e )
    {
        $this->event = $e;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown( 'layer2-address.emails.changed' )
            ->subject( env('IDENTITY_NAME') . " :: Layer2 / MAC Address Changed for " . $this->event->customer );
    }
}