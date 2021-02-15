<?php

namespace IXP\Policies;

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

use IXP\Models\CustomerToUser;
use IXP\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * User Policy
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Policies
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class UserPolicy
{
    use HandlesAuthorization;

    /**
     *
     * @param User $user
     *
     * @return mixed
     */
    public function any( User $user )
    {
        if( !$user->isCustUser() ){
            return true;
        }
    }

    /**
     * Determine whether the user can update the docstore file.
     *
     * @param   User        $auth
     * @param   User        $user
     *
     * @return mixed
     */
    public function access( User $auth, User $user )
    {
        $privs = $auth->privs();

        if( $privs === User::AUTH_SUPERUSER ){
            return true;
        }

        if( $privs === User::AUTH_CUSTADMIN && CustomerToUser::where( 'customer_id', $auth->custid )->where( 'user_id', $user->id )->exists() ){
            return true;
        }
    }
}