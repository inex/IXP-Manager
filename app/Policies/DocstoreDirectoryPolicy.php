<?php

namespace IXP\Policies;

use Entities\User;
use IXP\Models\DocstoreDirectory;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocstoreDirectoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any docstore directories.
     *
     * @param  \Entities\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can access files in a docstore directory.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return mixed
     */
    public function canAccess(User $user, DocstoreDirectory $docstoreDirectory)
    {
        return $user->getPrivs() >= $docstoreDirectory->min_privs;
    }

    /**
     * Determine whether the user can view the docstore directory.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return mixed
     */
    public function view(User $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }

    /**
     * Determine whether the user can create docstore directories.
     *
     * @param  \Entities\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the docstore directory.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return mixed
     */
    public function update(User $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }

    /**
     * Determine whether the user can delete the docstore directory.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return mixed
     */
    public function delete(User $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }

    /**
     * Determine whether the user can restore the docstore directory.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return mixed
     */
    public function restore(User $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the docstore directory.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreDirectory  $docstoreDirectory
     * @return mixed
     */
    public function forceDelete(User $user, DocstoreDirectory $docstoreDirectory)
    {
        //
    }
}
