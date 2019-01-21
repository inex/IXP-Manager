<?php

namespace IXP\Events\Layer2Address;

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
    User            as UserEntity,
    VlanInterface   as VlanInterfaceEntity
};

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

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
     * @var UserEntity
     */
    public $user;

    /**
     * @var string
     */
    public $customer;

    /**
     * @var VlanInterfaceEntity
     */
    public $vli;

    /**
     * Create a new event instance.
     *
     * @param string                $oldmac
     * @param VlanInterfaceEntity   $vli
     * @param UserEntity            $u
     *
     * @return void
     */
    public function __construct( string $oldmac, VlanInterfaceEntity $vli, UserEntity $u )
    {
        $this->action   = "delete";
        $this->mac      = $oldmac;
        $this->user     = $u;
        $this->customer = $u->getCustomer()->getFormattedName();
        $this->vli      = $vli;
    }
}
