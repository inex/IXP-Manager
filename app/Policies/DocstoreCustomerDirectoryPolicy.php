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

use Entities\User as UserEntity;

use IXP\Models\{
    Customer,
    DocstoreCustomerDirectory,
    PatchPanelPortFile,
    PatchPanelPortHistoryFile
};

use Illuminate\Auth\Access\HandlesAuthorization;

class DocstoreCustomerDirectoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can access the list
     *
     * @param UserEntity    $user
     *
     * @return mixed
     */
    public function listCustomers( UserEntity $user )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can access the list
     *
     * @param UserEntity    $user
     * @param Customer      $cust
     *
     * @return mixed
     */
    public function listPatchPanelPortFiles( UserEntity $user, Customer $cust )
    {
        return ( $user->isSuperUser() || $user->getCustomer()->getId() == $cust->id )
            && PatchPanelPortFile::getForCustomer( $cust, $user->isSuperUser() )->isNotEmpty();
    }

    /**
     * Determine whether the user can access the list
     *
     * @param UserEntity    $user
     * @param Customer      $cust
     *
     * @return mixed
     */
    public function listPatchPanelPortFilesHistory( UserEntity $user, Customer $cust )
    {
        return ( $user->isSuperUser() || $user->getCustomer()->getId() == $cust->id )
            && PatchPanelPortHistoryFile::getForCustomer( $cust, $user->isSuperUser() )->isNotEmpty();
    }

    /**
     * Determine whether the user can create docstore directories.
     *
     * @param UserEntity    $user
     * @param Customer      $cust
     *
     * @return mixed
     */
    public function list( UserEntity $user, Customer $cust  )
    {
        return $user->isSuperUser() || ( $user->getPrivs() >= UserEntity::AUTH_CUSTUSER && $user->getCustomer()->getId() === $cust->id ) ;
    }

    /**
     * Determine whether the user can create docstore directories.
     *
     * @param UserEntity    $user
     * @param Customer      $cust
     * @return mixed
     */
    public function create( UserEntity $user, Customer $cust )
    {
        return $user->isSuperUser() && $cust->exists;
    }

    /**
     * Determine whether the user can update the docstore directory.
     *
     * @param   UserEntity                  $user
     * @param   Customer                    $cust
     * @param   DocstoreCustomerDirectory   $dir
     *
     * @return mixed
     */
    public function update( UserEntity $user, DocstoreCustomerDirectory $dir )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can delete the docstore directory.
     *
     * @param   UserEntity                  $user
     * @param   DocstoreCustomerDirectory   $dir
     *
     * @return mixed
     */
    public function delete( UserEntity $user, DocstoreCustomerDirectory $dir )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can delete the docstore directory.
     *
     * @param   UserEntity  $user
     * @param   Customer    $cust
     *
     * @return mixed
     */
    public function deleteForCustomer( UserEntity $user, Customer $cust )
    {
        return $user->isSuperUser() && $cust->docstoreCustomerFiles()->get()->isNotEmpty();
    }
}
