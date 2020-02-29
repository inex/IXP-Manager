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

use Auth, D2EM;
use Illuminate\Auth\Recaller;
use PragmaRX\Google2FALaravel\Events\LoginSucceeded;

class Google2FALoginSucceeded {

    /**
     * Handle a Google2FA login event.
     *
     * What we need to do here is, if this is a 'remember me' session, store the fact that the user has
     * authenticated with Google2FA in the database. If we do not record this (and subsequently check it) it
     * is possibly for a user to avoid 2da by deleting the session cookie and forcing a remember me login.
     *
     * @param  LoginSucceeded  $e
     * @return void
     */
    public function handle( LoginSucceeded $e )
    {
        if( $r = request()->cookies->get(Auth::getRecallerName()) ) {

            $recaller = new Recaller($r);

            /** @var \Entities\UserRememberToken $urt */
            $urt = d2r( 'UserRememberToken' )->findOneBy( [ 'token' => $recaller->token() ] );

            if( $urt && !$urt->getIs2faComplete() ) {
                $urt->setIs2faComplete(true);
                D2EM::flush($urt);
            }
        }
    }
}
