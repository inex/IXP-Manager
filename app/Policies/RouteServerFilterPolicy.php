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

use Route;

use IXP\Models\{
    User,
    Customer,
    RouteServerFilter
};

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * RouteServerFilterPolicy
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Policies
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class RouteServerFilterPolicy
{
    use HandlesAuthorization;

    /**
     * Super admins can do anything
     *
     * @param User  $user
     * @param       $ability
     *
     * @return bool
     *
     * @throws
     */
    public function before( User $user, $ability): bool
    {
        $privs = $user->privs();
        if( $privs !== User::AUTH_SUPERUSER ) {
            $minAuth = User::AUTH_CUSTADMIN;

            if( in_array( explode('@', Route::getCurrentRoute()->getActionName() )[1], [ "view", "list" ] ) ){
                $minAuth = User::AUTH_CUSTUSER;
            }

            if( $privs < $minAuth ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine whether the user can access to that route
     *
     * @param User          $user
     * @param Customer      $cust
     *
     * @return bool
     *
     * @throws
     */
    public function checkCustObject( User $user, Customer $cust ): bool
    {
        if( $cust->id !== $user->custid && !$user->isSuperUser() ){
            return false;
        }
        return $cust->routeServerClient();
    }

    /**
     * Determine whether the user can access to that route
     *
     * @param  User                 $user
     * @param  RouteServerFilter    $rsf
     *
     * @return bool
     *
     * @throws
     */
    public function checkRsfObject( User $user, RouteServerFilter $rsf ): bool
    {
        if(  $rsf->customer_id !== $user->custid  && !$user->isSuperUser() ){
            return false;
        }
        return $rsf->customer->routeServerClient();
    }
}