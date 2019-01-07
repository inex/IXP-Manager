<?php

namespace IXP\Mail\Customer;

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

use Entities\CompanyBillingDetail as CompanyBillingDetailEntity;

/**
 * Mailable for billing details changed
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Customer
 * @package    IXP\Mail\Customer
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class BillingDetailsChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Old/original details
     * @var CompanyBillingDetailEntity
     */
    public $ocbd;

    /**
     * New details
     * @var CompanyBillingDetailEntity
     */
    public $cbd;

    /**
     * Create a new message instance.
     *
     * @param CompanyBillingDetailEntity     $ocbd
     * @param CompanyBillingDetailEntity     $cbd
     * @return void
    */
    public function __construct( CompanyBillingDetailEntity $ocbd, CompanyBillingDetailEntity $cbd )
    {
        $this->ocbd = $ocbd;
        $this->cbd  = $cbd;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown( 'customer.emails.billing-details-changed' )
            ->subject( env('IDENTITY_NAME') . " :: Updated Billing Details for " . $this->cbd->getCustomer()->getFormattedName() );
    }
}
