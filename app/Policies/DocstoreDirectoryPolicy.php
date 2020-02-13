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

use IXP\Models\DocstoreDirectory;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocstoreDirectoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any docstore directories.
     *
     * @param  UserEntity  $user
     * @return mixed
     */
    public function viewAny(UserEntity $user)
    {
        //
    }

    /**
     * Determine whether the user can view the docstore directory.
     *
     * @param  UserEntity  $user
     * @param DocstoreDirectory $docstoreDirectory
     * @return mixed
     */
    public function view(?UserEntity $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }

    /**
     * Determine whether the user can create docstore directories.
     *
     * @param  UserEntity  $user
     * @return mixed
     */
    public function create(UserEntity $user)
    {
        return $user->isSuperUser();
    }

    /**
     * Determine whether the user can update the docstore directory.
     *
     * @param  UserEntity  $user
     * @param DocstoreDirectory $docstoreDirectory
     *
     * @return mixed
     */
    public function update( UserEntity $user, DocstoreDirectory $docstoreDirectory )
    {
        return $user->isSuperUser() && $docstoreDirectory->exists;
    }

    /**
     * Determine whether the user can delete the docstore directory.
     *
     * @param  UserEntity  $user
     * @param DocstoreDirectory $dir
     *
     * @return mixed
     */
    public function delete( UserEntity $user, DocstoreDirectory $dir )
    {
        return $user->isSuperUser() && $dir->exists && !$dir->subDirectories()->exists() && !$dir->files()->exists();
    }

    /**
     * Determine whether the user can restore the docstore directory.
     *
     * @param  UserEntity  $user
     * @param DocstoreDirectory $docstoreDirectory
     * @return mixed
     */
    public function restore(UserEntity $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the docstore directory.
     *
     * @param  UserEntity  $user
     * @param DocstoreDirectory $docstoreDirectory
     * @return mixed
     */
    public function forceDelete(UserEntity $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }
}
