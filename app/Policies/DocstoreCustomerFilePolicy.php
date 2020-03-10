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

use IXP\Models\DocstoreCustomerFile;

use Illuminate\Auth\Access\HandlesAuthorization;

class DocstoreCustomerFilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can download a docstore customer file.
     *
     * @param   UserEntity              $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function download( ?UserEntity $user, DocstoreCustomerFile $file )
    {
        return $file->min_privs <= ( $user ? $user->getPrivs() : UserEntity::AUTH_PUBLIC );
    }

    /**
     * Determine whether the user can view the docstore customer file.
     *
     * @param   UserEntity              $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function view( ?UserEntity $user, DocstoreCustomerFile $file )
    {
        return $file->min_privs <= ( $user ? $user->getPrivs() : UserEntity::AUTH_PUBLIC );
    }

    /**
     * Determine whether the user can create docstore customer files.
     *
     * @param  UserEntity  $user
     *
     * @return mixed
     */
    public function create( UserEntity $user )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can get info on the docstore customer file.
     *
     * @param   UserEntity              $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function info( UserEntity $user, DocstoreCustomerFile $file )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can update the docstore customer file.
     *
     * @param   UserEntity              $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function update( UserEntity $user, DocstoreCustomerFile $file )
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can delete the docstore customer file.
     *
     * @param   UserEntity              $user
     * @param   DocstoreCustomerFile    $file
     *
     * @return mixed
     */
    public function delete( UserEntity $user, DocstoreCustomerFile $file )
    {
        return $user->isSuperUser();
    }
}
