<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Policies;

use IXP\Models\{
    Customer,
    User,
    RouteServerFilter
};

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * RouteServerFilterPolicy
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
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
     * @param $ability
     *
     * @return null|true
     */
    public function before( User $user, $ability ): bool|null
    {
        if( $user->isSuperUser() ) {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can access the list of customers with filters
     */
    public function checkListCustomers( User $user ): bool
    {
        return $user->isSuperUser();
    }

    /**
     * Authorise a request by $user to update the provided RouteServerFilter
     */
    public function update( User $user, RouteServerFilter $rsf ): bool
    {
        return $this->authorizeCustomerAccess( $user, $rsf->customer );
    }

    /**
     * Authorise a request by $user to view the provided RouteServerFilter
     */
    public function view( User $user, RouteServerFilter $rsf ): bool
    {
        return $this->authorizeCustomerAccess( $user, $rsf->customer, true );
    }

    /**
     * Authorise a request by $user to toggle enable on the provided RouteServerFilter
     */
    public function toggleEnable( User $user, RouteServerFilter $rsf ): bool
    {
        return $this->authorizeCustomerAccess( $user, $rsf->customer );
    }

    /**
     * Authorise a request by $user to move the position of the provided RouteServerFilter
     */
    public function changeOrder( User $user, RouteServerFilter $rsf ): bool
    {
        return $this->authorizeCustomerAccess( $user, $rsf->customer );
    }

    /**
     * Authorise a request by $user to delete the provided RouteServerFilter
     */
    public function delete( User $user, RouteServerFilter $rsf ): bool
    {
        return $this->authorizeCustomerAccess( $user, $rsf->customer );
    }

    /**
     * Authorise a request from $user to view $customer's route server filters.
     */
    public function listRsFilters( User $user, Customer $customer ): bool
    {
        return $this->authorizeCustomerAccess( $user, $customer, true );
    }

    /**
     * Authorise a request from $user to revert $customer's uncommitted route server filters.
     */
    public function revertRsFilters( User $user, Customer $customer ): bool
    {
        return $this->authorizeCustomerAccess( $user, $customer );
    }

    /**
     * Authorise a request from $user to commit $customer's staged route server filters.
     */
    public function commitRsFilters( User $user, Customer $customer ): bool
    {
        return $this->authorizeCustomerAccess( $user, $customer );
    }

    /**
     * Authorise a request from $user to create a new RsFilter for $customer.
     */
    public function createRsFilter( User $user, Customer $customer ): bool
    {
        return $this->authorizeCustomerAccess($user, $customer);
    }

    private function authorizeCustomerAccess(User $user, Customer $customer, bool $allowCustUser = false): bool
    {
        // there exists a before method, with the same check, but add it here just in case it ever gets removed.
        if ($user->isSuperUser()) {
            return true;
        }
        if ( $customer->id !== $user->custid ) {
            return false;
        }

        if ( $user->isCustAdmin() || ( $allowCustUser && $user->isCustUser() ) ) {
            return $customer->routeServerClient();
        }

        return false;
    }

}