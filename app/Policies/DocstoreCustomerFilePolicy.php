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
    DocstoreCustomerFile,
    User
};

/**
 * DocstoreCustomerFilePolicy
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Policies
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class DocstoreCustomerFilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can download a docstore customer file.
     *
     * @param   User                    $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function download( User $user, DocstoreCustomerFile $file ): bool
    {
        return $user->isSuperUser() || ( $file->min_privs <= $user->privs() && request()->user()->custid === $file->customer->id );
    }

    /**
     * Determine whether the user can view the docstore customer file.
     *
     * @param User                  $user
     * @param DocstoreCustomerFile  $file
     *
     * @return mixed
     */
    public function view( User $user, DocstoreCustomerFile $file ): bool
    {
        return $user->isSuperUser() || ( $file->min_privs <= $user->privs() && request()->user()->custid === $file->customer->id );
    }

    /**
     * Determine whether the user can create docstore customer files.
     *
     * @param  User  $user
     *
     * @return mixed
     */
    public function create( User $user ): bool
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can get info on the docstore customer file.
     *
     * @param   User                    $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function info( User $user, DocstoreCustomerFile $file ): bool
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can update the docstore customer file.
     *
     * @param   User                    $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function update( User $user, DocstoreCustomerFile $file ): bool
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can delete the docstore customer file.
     *
     * @param   User                    $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function delete( User $user, DocstoreCustomerFile $file ): bool
    {
        return $user->isSuperUser() && $file->exists;
    }
}