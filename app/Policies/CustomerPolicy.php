<?php

namespace IXP\Policies;

use Entities\User;
use IXP\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;


    /**
     * Superadmins can do anything
     *
     * @param User $user
     * @param $ability
     * @return bool
     */
    public function before( User $user, $ability)
    {
        if( $user->isSuperUser() ) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the customer.
     *
     * @param  \Entities\User  $user
     * @param  Customer  $customer
     * @return mixed
     */
    public function view(User $user, Customer $customer)
    {
        //
        return $user->getCustomer()->getId() === $customer->id;
    }

    /**
     * Determine whether the user can create customers.
     *
     * @param  \Entities\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the customer.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Customer  $customer
     * @return mixed
     */
    public function update(User $user, Customer $customer)
    {
        //
    }

    /**
     * Determine whether the user can delete the customer.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Customer  $customer
     * @return mixed
     */
    public function delete(User $user, Customer $customer)
    {
        //
    }

    /**
     * Determine whether the user can restore the customer.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Customer  $customer
     * @return mixed
     */
    public function restore(User $user, Customer $customer)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the customer.
     *
     * @param  \Entities\User  $user
     * @param  \IXP\Customer  $customer
     * @return mixed
     */
    public function forceDelete(User $user, Customer $customer)
    {
        //
    }
}
