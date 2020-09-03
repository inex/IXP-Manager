<?php

namespace IXP\Listeners\Auth;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, D2EM, Log;
use Entities\UserLoginHistory as UserLoginHistoryEntity;
use Illuminate\Auth\Events\Login as LoginEvent;

class LoginSuccessful {

    /**
     * Handle a login event.
     *
     *
     * @param  LoginEvent  $e
     * @return void
     */
    public function handle( LoginEvent $e )
    {
        Log::notice( 'Login successful for user "' .$e->user->getUsername(). '" from IP ' . ixp_get_client_ip() . '.' );

        if( !session()->exists( "switched_user_from" ) && $e->user->getCurrentCustomerToUser() ) {
            if( Auth::viaRemember() ) {
                $e->user->getCurrentCustomerToUser()->setLastLoginVia( 'RememberMe' );
            } else {
                $e->user->getCurrentCustomerToUser()->setLastLoginVia( 'Login' );
            }

            $e->user->getCurrentCustomerToUser()->setLastLoginAt( now() );
            $e->user->getCurrentCustomerToUser()->setLastLoginFrom( ixp_get_client_ip() );

            if( config( "ixp_fe.login_history.enabled" ) ) {
                $log = new UserLoginHistoryEntity;
                $log->setAt( now() );
                $log->setVia( Auth::viaRemember() ? 'RememberMe' : 'Login' );
                $log->setIp( ixp_get_client_ip() );
                $log->setCustomerToUser( $e->user->getCurrentCustomerToUser() );
                D2EM::persist( $log );
            }
        }

        D2EM::flush();
    }
}
