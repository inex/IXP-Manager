<?php

namespace IXP\Events\Layer2Address;

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

use IXP\Models\{
    User,
    VlanInterface
};

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Deleted Event
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Layer2Address\Event
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Deleted
{
    use Dispatchable, SerializesModels;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $mac;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $customer;

    /**
     * @var VlanInterface
     */
    public $vli;

    /**
     * Create a new event instance.
     *
     * @param string                $oldmac
     * @param VlanInterface         $vli
     * @param User                  $u
     *
     * @return void
     */
    public function __construct( string $oldmac, VlanInterface $vli, User $u )
    {
        $this->action   = "delete";
        $this->mac      = $oldmac;
        $this->user     = $u;
        $this->customer = $u->customer->getFormattedName();
        $this->vli      = $vli;
    }
}