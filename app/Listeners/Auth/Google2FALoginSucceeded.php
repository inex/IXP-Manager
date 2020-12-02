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
use Auth;

use Illuminate\Auth\Recaller;

use IXP\Models\UserRememberToken;

use PragmaRX\Google2FALaravel\Events\LoginSucceeded;

/**
 * Google2FALoginSucceeded Listener
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Listeners\Auth
 * @copyright  Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Google2FALoginSucceeded
{
    /**
     * Handle a Google2FA login event.
     *
     * What we need to do here is, if this is a 'remember me' session, store the fact that the user has
     * authenticated with Google2FA in the database. If we do not record this (and subsequently check it) it
     * is possibly for a user to avoid 2da by deleting the session cookie and forcing a remember me login.
     *
     * @param  LoginSucceeded  $e
     *
     * @return void
     */
    public function handle( LoginSucceeded $e ): void
    {
        if( $r = request()->cookies->get( Auth::getRecallerName() ) ) {
            $recaller = new Recaller( $r );
            $urt = UserRememberToken::where( 'token',  $recaller->token() )->first();

            if( $urt && !$urt->is_2fa_complete ) {
                $urt->update( [ 'is_2fa_complete' => true ] );
            }
        }
    }
}