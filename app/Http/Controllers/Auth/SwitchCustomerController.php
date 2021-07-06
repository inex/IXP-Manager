<?php

namespace IXP\Http\Controllers\Auth;

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

use Auth;

use Illuminate\Http\RedirectResponse;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Customer,
    CustomerToUser,
    User,
    UserLoginHistory};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * SwitchCustomerController
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Auth
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SwitchCustomerController extends Controller
{
    /**
     * Allow a user to switch of customer
     *
     * @param Customer $cust
     *
     * @return RedirectResponse
     */
    public function switch( Customer $cust ): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::getUser();

        // Check if the selected customer is associated with the current user
         if( !( $c2u = CustomerToUser::where( 'customer_id', $cust->id )->where( 'user_id', $user->id )->first() ) ){
            AlertContainer::push( "You are not allowed to access to this " . config( "ixp_fe.lang.customer.one" ) . ".", Alert::DANGER );
            return redirect()->to( "/" );
        }

         // Check if the selected customer is active
        if( $c2u->customer()->active()->notDeleted()->get()->isEmpty() ){
            AlertContainer::push( "You are not allowed to access to this " . config( "ixp_fe.lang.customer.one" ) . ".", Alert::DANGER );
            return redirect()->to( "/" );
        }

        $c2u->update([
            'last_login_date' => now(),
            'last_login_from' => $this->getIp(),
        ]);

        if( config( "ixp_fe.login_history.enabled" ) ) {
            UserLoginHistory::create( [
                'ip'                    => $this->getIp(),
                'at'                    => now(),
                'customer_to_user_id'   => $c2u->id,
                'via'                   => 'SwitchCustomer'
            ] );
        }

        $user->custid = $cust->id;
        $user->save();

        AlertContainer::push( "You are now logged in for {$cust->name}.", Alert::SUCCESS );
        return redirect()->to( "/" );
    }
}