<?php

namespace IXP\Mail\Customer;

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
use Illuminate\Contracts\Queue\ShouldQueue;

use Entities\{
    Customer as CustomerEntity
};

/**
 * Mailable for Customer
 *
 * @author     Barry O'Donovan  <barry@islandbridgenetworks.ie>
 * @author     Yanm Robin       <yann@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var CustomerEntity
     */
    public $cust;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string The template to use to create the email
     */
    protected $tmpl;

    /**
     * Create a new message instance.
     *
     * @param CustomerEntity $cust
     */
    public function __construct( CustomerEntity $cust ) {
        $this->cust    = $cust;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this;
    }
}
