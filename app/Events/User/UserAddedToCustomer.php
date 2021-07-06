<?php

namespace IXP\Events\User;

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

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use IXP\Models\CustomerToUser;

/**
 * UserAddedToCustomer Event
 * Event to trigger the (re)sending of a user welcome email
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Events\User
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserAddedToCustomer
{
    use Dispatchable, SerializesModels;

    /**
     * @var CustomerToUser
     */
    public $c2u;

    /**
     * Create a new event instance.
     *
     * @param CustomerToUser    $c2u
     */
    public function __construct(  CustomerToUser $c2u )
    {
        $this->c2u     = $c2u;
    }
}