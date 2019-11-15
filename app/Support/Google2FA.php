<?php

namespace IXP\Support;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM, Str;

use Illuminate\Auth\Recaller;
use Entities\{
    OtpRememberTokens   as OtpRememberTokensEntity,
    User                as UserEntity
};

use PragmaRX\Google2FALaravel\Events\LoggedOut;
use PragmaRX\Google2FALaravel\Google2FA as Google2FABase;
use PragmaRX\Google2FALaravel\Support\Constants;

use Illuminate\Auth\SessionGuard;


class Google2FA extends Google2FABase
{

    /**
     * Set current auth as valid.
     */
    public function login()
    {
        $this->sessionPut(Constants::SESSION_AUTH_PASSED, true);

        $this->updateCurrentAuthTime();

        if( request()->input( "remember_me" ) ){
            D2EM::getRepository( OtpRememberTokensEntity::class )->createRememberToken( request()->user(), $token = $this->getNewToken() );
            $this->createRememberCookie( $token );
            D2EM::getRepository( OtpRememberTokensEntity::class )->purgeRememberTokens( request()->user()->getAuthIdentifier(), true );
        }
    }

    private function getNewToken(){
        return Str::random(60);
    }

    /**
     * OTP logout.
     */
    public function logout()
    {
        $user = $this->getUser();

        $this->sessionForget();

        if ( $recaller = $this->recaller() ) {
            Auth::getCookieJar()->queue( Auth::getCookieJar()->forget( self::getCookieName() ) );

            D2EM::getRepository( OtpRememberTokensEntity::class )->deleteRememberToken( $recaller->id(), $recaller->token() );
        }

        event(new LoggedOut($user));
    }

    /**
     * Verify if there is a remember me cookie existing
     *
     * if yes we replace the remember token with a new one and generate a new cookie
     *
     * @return bool
     */
    protected function verifyRememberMeCookie()
    {
        if ( $recaller = $this->recaller() ) {
            if( !$recaller->valid() ){
                return false;
            }

            if( $user = D2EM::getRepository( UserEntity::class )->retrieveByOtcToken( $recaller->id(), $recaller->token() ) ) {
                D2EM::getRepository( OtpRememberTokensEntity::class )->replaceRememberToken( $user->getAuthIdentifier(), $recaller->token(), $newToken = $this->getNewToken(), config( "google2fa.remember_me_expire" ) );
                $this->createRememberCookie( $newToken );
                return true;
            }

        }
        return false;
    }


    /**
     * Create the Otc Remember me cookie
     *
     * @param string $token
     */
    private function createRememberCookie( string $token )
    {
        $user = Auth::user();

        $cookie = Auth::getCookieJar()->make( self::getCookieName(), $user->getAuthIdentifier().'|'.$token.'|'.$user->getAuthPassword() , config( "google2fa.remember_me_expire" ) );

        Auth::getCookieJar()->queue( $cookie );
    }

    /**
     * Get the remember me cookie name
     *
     * @return string
     */
    public static function getCookieName(){
        return 'otp_remember_'.sha1( SessionGuard::class );
    }

    /**
     * Get recalled object depending on the cookie name
     *
     * @return bool|Recaller
     */
    private function recaller()
    {
        if ( $recaller = request()->cookies->get( $this->getCookieName() ) ) {
            return new Recaller( $recaller );
        }

        return false;
    }
}