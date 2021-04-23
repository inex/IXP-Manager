<?php

namespace IXP\Mail\Customer\Note;

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

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use IXP\Events\Customer\Note\Changed as CustomerNoteChangedEvent;

/**
 * Mailable for customer note changed
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin      <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Changed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     *
     * @var CustomerNoteChangedEvent
     */
    public $event;

    /**
     * Create a new message instance.
     *
     * @param CustomerNoteChangedEvent $e
     *
     * @return void
     */
    public function __construct( CustomerNoteChangedEvent $e )
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
        $cust = $this->event->note() ? $this->event->note()->customer : $this->event->oldNote()->customer;
        return $this->markdown( 'customer.emails.note-changed' )
            ->subject( env('IDENTITY_NAME') . " :: Customer Notes :: " . $cust->getFormattedName() );
    }
}