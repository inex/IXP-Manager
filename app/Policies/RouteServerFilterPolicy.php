<?php

namespace IXP\Policies;

use IXP\Models\User;
use Route;
use Entities\{
    User as UserEntity
};
use IXP\Models\Customer;
use IXP\Models\RouteServerFilter;
use Illuminate\Auth\Access\HandlesAuthorization;

class RouteServerFilterPolicy
{
    use HandlesAuthorization;

    /**
     * Super admins can do anything
     *
     * @param UserEntity $user
     * @param $ability
     *
     * @return bool
     *
     * @throws
     */
    public function before( UserEntity $user, $ability)
    {
        if( !$user->isSuperUser() ) {
            $minAuth = User::AUTH_CUSTADMIN;

            if( in_array( explode('@', Route::getCurrentRoute()->getActionName() )[1], [ "view", "list" ] ) ){
                $minAuth = User::AUTH_CUSTUSER;
            }

            if( $user->getPrivs() < $minAuth ) {
                return false;
            }
        }
    }

    /**
     * Determine whether the user can access to that route
     *
     * @param UserEntity    $user
     * @param Customer      $cust
     *
     * @return mixed
     *
     * @throws
     */
    public function checkCustObject( UserEntity $user, Customer $cust )
    {
        if( !$user->isSuperUser() && $cust->id !== $user->getCustomer()->getId() ){
            return false;
        }
        return $cust->isRouteServerClient();
    }

    /**
     * Determine whether the user can access to that route
     *
     * @param  UserEntity  $user
     * @param  RouteServerFilter  $rsf
     * @return mixed
     */
    public function checkRsfObject( UserEntity $user, RouteServerFilter $rsf )
    {
        if( !$user->isSuperUser() && $rsf->customer_id !== $user->getCustomer()->getId() ){
            return false;
        }

        return $rsf->customer->isRouteServerClient();
    }

}
