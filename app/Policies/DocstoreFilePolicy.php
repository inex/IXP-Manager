<?php

namespace IXP\Policies;

use Entities\User;
use IXP\Models\DocstoreFile;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocstoreFilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any docstore files.
     *
     * @param  \Entities\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the docstore file.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreFile  $docstoreFile
     * @return mixed
     */
    public function view(?User $user, DocstoreFile $docstoreFile)
    {
        //
        return $docstoreFile->min_privs <= ( $user ? $user->getPrivs() : User::AUTH_PUBLIC );
    }

    /**
     * Determine whether the user can create docstore files.
     *
     * @param  \Entities\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the docstore file.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreFile  $docstoreFile
     * @return mixed
     */
    public function update(User $user, DocstoreFile $docstoreFile)
    {
        //
    }

    /**
     * Determine whether the user can delete the docstore file.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreFile  $docstoreFile
     * @return mixed
     */
    public function delete(User $user, DocstoreFile $docstoreFile)
    {
        //
    }

    /**
     * Determine whether the user can restore the docstore file.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreFile  $docstoreFile
     * @return mixed
     */
    public function restore(User $user, DocstoreFile $docstoreFile)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the docstore file.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Models\DocstoreFile  $docstoreFile
     * @return mixed
     */
    public function forceDelete(User $user, DocstoreFile $docstoreFile)
    {
        //
    }
}
