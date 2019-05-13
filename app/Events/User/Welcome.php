<?php

namespace IXP\Events\User;

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

use Entities\{
    Customer        as CustomerEntity,
    User            as UserEntity
};

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use IXP\Models\Customer;

/**
 * Event to trigger the (re)sending of a user welcome email
 *
 * @package IXP\Events\User
 */
class Welcome
{
    use Dispatchable, SerializesModels;

    /**
     * @var CustomerEntity
     */
    public $customer;

    /**
     * @var UserEntity
     */
    public $user;

    /**
     * @var boolean
     */
    public $resend;

    /**
     * @var boolean
     */
    public $existing;

    /**
     * Create a new event instance.
     *
     * @param UserEntity    $u
     * @param bool          $resend     Do you need to resend the email ?
     * @param bool          $existing   Does the user already exist ?
     */
    public function __construct(  ?CustomerEntity $c, UserEntity $u, bool $resend = false, bool $existing = false )
    {
        $this->customer = $c;
        $this->user     = $u;
        $this->resend   = $resend;
        $this->existing = $existing;
    }
}
