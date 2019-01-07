<?php

namespace IXP\Events\Customer;

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



use Entities\{
    CompanyBillingDetail as CompanyBillingDetailEntity
};

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;


class BillingDetailsChanged
{
    use Dispatchable, SerializesModels;

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
     * Create a new event instance.
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

}
