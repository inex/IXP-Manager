<?php

namespace IXP\Events\Auth;

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

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use Entities\{
    User    as UserEntity
};

class ForgotPassword{

    use Dispatchable, SerializesModels;


    /**
     * @var token
     */
    public $token;

    /**
     * @var UserEntity user
     */
    public $user;


    /**
     * Create a new event instance.
     *
     * @param string $token
     * @param UserEntity $user
     */
    public function __construct( string $token, UserEntity $user )
    {
        $this->token        = $token;
        $this->user         = $user;

    }
}
