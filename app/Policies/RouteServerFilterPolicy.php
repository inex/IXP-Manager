<?php

namespace IXP\Policies;

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


use IXP\Models\User;
use Route;
use Entities\{
    User as UserEntity
};
use IXP\Models\Customer;
use IXP\Models\RouteServerFilter;
use Illuminate\Auth\Access\HandlesAuthorization;

class RouteServerFilterPolicy
{
    use HandlesAuthorization;

    /**
     * Super admins can do anything
     *
     * @param UserEntity $user
     * @param $ability
     *
     * @return bool
     *
     * @throws
     */
    public function before( UserEntity $user, $ability)
    {
        if( !$user->isSuperUser() ) {
            $minAuth = User::AUTH_CUSTADMIN;

            if( in_array( explode('@', Route::getCurrentRoute()->getActionName() )[1], [ "view", "list" ] ) ){
                $minAuth = User::AUTH_CUSTUSER;
            }

            if( $user->getPrivs() < $minAuth ) {
                return false;
            }
        }
    }

    /**
     * Determine whether the user can access to that route
     *
     * @param UserEntity    $user
     * @param Customer      $cust
     *
     * @return mixed
     *
     * @throws
     */
    public function checkCustObject( UserEntity $user, Customer $cust )
    {
        if( !$user->isSuperUser() && $cust->id !== $user->getCustomer()->getId() ){
            return false;
        }

        return $cust->isRouteServerClient();
    }

    /**
     * Determine whether the user can access to that route
     *
     * @param  UserEntity  $user
     * @param  RouteServerFilter  $rsf
     * @return mixed
     */
    public function checkRsfObject( UserEntity $user, RouteServerFilter $rsf )
    {
        if( !$user->isSuperUser() && $rsf->customer_id !== $user->getCustomer()->getId() ){
            return false;
        }

        return $rsf->customer->isRouteServerClient();
    }

}
