<?php

namespace IXP\Support;

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

use Auth;

use PragmaRX\Google2FALaravel\{
    Exceptions\InvalidSecretKey,
    Support\Authenticator,
    Support\Constants
};

/**
 * https://github.com/antonioribeiro/google2fa
 * Class Google2FAAuthenticator
 * @package IXP\Support
 */
class Google2FAAuthenticator extends Authenticator
{
    /**
     * Check if the user is already logged in or passable without checking for an OTP.
     *
     * @return bool
     */
    protected function canPassWithoutCheckingOTP()
    {
        // Check if we have a remember me cookie token, stored in request because the remember me cookie
        // is updated every time we login and placed in the cookie queue so not accessible yet with the middleware
        // That why we added a value in the request in order to know if we allow the user to avoid the OTP security form
        if( request()->request->has( "ixpm-remember-me-token" ) ){
            $this->login();
            return true;
        }

        if( !( $this->getUser()->getUser2FA() ) )
            return true;
        return
            !$this->getUser()->getUser2FA()->enabled() ||
            !$this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            $this->twoFactorAuthStillValid();
    }
    

    /**
     * Set current auth as valid.
     */
    public function login()
    {
        $this->sessionPut(Constants::SESSION_AUTH_PASSED, true);

        $this->updateCurrentAuthTime();

        if( request()->input( "remember_me" ) ) {
            Auth::guard()->RememberMeViaOTP( Auth::user(), true );
        }
    }

    /**
     * Override the function in order to avoid en error is the code is empty
     *
     * @throws
     *
     * @return mixed
     */
    protected function verifyOneTimePassword()
    {
        return $this->verifyAndStoreOneTimePassword($this->getOneTimePassword() ?? '');
    }

    /**
     * Get the user Google2FA secret.
     *
     * @throws InvalidSecretKey
     *
     * @return mixed
     */
    protected function getGoogle2FASecretKey()
    {
        $secret = $this->getUser()->getUser2FA()->getSecret();
        if (is_null($secret) || empty($secret)) {
            throw new InvalidSecretKey('Secret key cannot be empty.');
        }
        return $secret;
    }

}