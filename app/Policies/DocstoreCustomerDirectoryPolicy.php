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
    Customer,
    DocstoreCustomerDirectory,
    User
};

/**
 * DocstoreCustomerDirectoryPolicy
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Policies
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DocstoreCustomerDirectoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the list
     *
     * @param User    $user
     *
     * @return mixed
     */
    public function listCustomers( User $user )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can access the list
     *
     * @param User          $user
     * @param Customer      $cust
     *
     * @return mixed
     */
    public function listPatchPanelPortFiles( User $user, Customer $cust ): bool
    {
        return ( $user->isSuperUser() || $user->custid === $cust->id )
            && $cust->patchPanelPorts()->with( 'patchPanelPortFiles' )
                ->has($user->isSuperUser() ? 'patchPanelPortFiles' : 'patchPanelPortFilesPublic' )->get()
                ->pluck( 'patchPanelPortFiles' )->isNotEmpty();
    }

    /**
     * Determine whether the user can access the list
     *
     * @param User          $user
     * @param Customer      $cust
     *
     * @return mixed
     */
    public function listPatchPanelPortFilesHistory( User $user, Customer $cust ): bool
    {
        return ( $user->isSuperUser() || $user->custid === $cust->id )
            && $cust->patchPanelPortHistories()
                ->with( 'patchPanelPortHistoryFiles' )->has( 'patchPanelPortHistoryFiles' )
                ->get()->pluck( 'patchPanelPortHistoryFiles' )->isNotEmpty();
    }

    /**
     * Determine whether the user can create docstore directories.
     *
     * @param User          $user
     * @param Customer      $cust
     *
     * @return mixed
     */
    public function list( User $user, Customer $cust  ): bool
    {
        return $user->isSuperUser() || ( $user->privs() >= User::AUTH_CUSTUSER && $user->custid === $cust->id ) ;
    }

    /**
     * Determine whether the user can create docstore directories.
     *
     * @param User          $user
     * @param Customer      $cust
     *
     * @return mixed
     */
    public function create( User $user, Customer $cust ): bool
    {
        return $user->isSuperUser() && $cust->exists;
    }

    /**
     * Determine whether the user can update the docstore directory.
     *
     * @param   User                        $user
     * @param   DocstoreCustomerDirectory   $dir
     *
     * @return mixed
     */
    public function update( User $user, DocstoreCustomerDirectory $dir )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can delete the docstore directory.
     *
     * @param   User                        $user
     * @param   DocstoreCustomerDirectory   $dir
     *
     * @return mixed
     */
    public function delete( User $user, DocstoreCustomerDirectory $dir )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can delete the docstore directory.
     *
     * @param   User        $user
     * @param   Customer    $cust
     *
     * @return mixed
     */
    public function deleteForCustomer( User $user, Customer $cust ): bool
    {
        return $user->isSuperUser() && $cust->docstoreCustomerFiles()->get()->isNotEmpty();
    }
}