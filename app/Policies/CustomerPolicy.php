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

use Illuminate\Auth\Access\HandlesAuthorization;

use IXP\Models\{
    User,
    Customer
};

/**
 * CustomerPolicy
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Policies
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class CustomerPolicy
{
    use HandlesAuthorization;


    /**
     * Superadmins can do anything
     *
     * @param User  $user
     * @param       $ability
     *
     * @return bool|null
     */
    public function before( User $user, $ability ): ?bool
    {
        if( $user->isSuperUser() ) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can view the customer.
     *
     * @param  User         $user
     * @param  Customer     $customer
     *
     * @return mixed
     */
    public function view( User $user, Customer $customer ): bool
    {
        return $user->custid === $customer->id;
    }

    /**
     * Determine whether the user can create customers.
     *
     * @param  User  $user
     *
     * @return mixed
     */
    public function create( User $user )
    {
        //
    }

    /**
     * Determine whether the user can update the customer.
     *
     * @param  User         $user
     * @param  Customer     $customer
     *
     * @return mixed
     */
    public function update(User $user, Customer $customer)
    {
        //
    }

    /**
     * Determine whether the user can delete the customer.
     *
     * @param  User         $user
     * @param  Customer     $customer
     * @return mixed
     */
    public function delete( User $user, Customer $customer )
    {
        //
    }

    /**
     * Determine whether the user can restore the customer.
     *
     * @param  User         $user
     * @param  Customer     $customer
     * @return mixed
     */
    public function restore( User $user, Customer $customer )
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the customer.
     *
     * @param  User         $user
     * @param  Customer     $customer
     *
     * @return mixed
     */
    public function forceDelete( User $user, Customer $customer )
    {
        //
    }
}