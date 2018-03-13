<?php

namespace IXP\Mail\Customer\Note;

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



use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Entities\{
    CustomerNote    as CustomerNoteEntity,
    Customer        as CustomerEntity,
    User            as UserEntity
};

/**
 * Mailable for customer note changed
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin      <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Changed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Old/original customer not
     * @var CustomerNoteEntity
     */
    public $ocn;

    /**
     * New customer note
     * @var CustomerNoteEntity
     */
    public $cn;

    /**
     * Customer
     * @var CustomerEntity
     */
    public $cust;

    /**
     * User
     * @var UserEntity
     */
    public $user;

    /**
     * type of action
     * @var string
     */
    public $type;

    /**
     * Create a new message instance.
     *
     * @param CustomerNoteEntity|null   $ocn
     * @param CustomerNoteEntity|null   $cn
     * @param CustomerEntity            $cust
     * @param UserEntity                $user
     * @param string                    $type
     * @return void
     */
    public function __construct( $ocn,  $cn, CustomerEntity $cust, UserEntity $user, string $type )
    {
        $this->ocn      = $ocn;
        $this->cn       = $cn;
        $this->cust     = $cust;
        $this->user     = $user;
        $this->type     = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $cust = $this->cn ? $this->cn->getCustomer() : $this->ocn->getCustomer();
        return $this->markdown( 'customer.emails.note-changed' )
            ->subject( env('IDENTITY_NAME') . " :: IXP Notes " . $cust->getFormattedName() );
    }
}
