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

use PragmaRX\Google2FALaravel\{
    Events\EmptyOneTimePasswordReceived,
    Events\LoginFailed,
    Events\LoginSucceeded,
    Exceptions\InvalidOneTimePassword,
    Exceptions\InvalidSecretKey,
    Support\ErrorBag,
    Support\Input,
    Support\Response,
    Support\Session
};

use Illuminate\Http\Request as IlluminateRequest;

class Google2FAAuthenticator extends Google2FA
{
    use ErrorBag, Input, Response, Session;

    /**
     * The current password.
     *
     * @var
     */
    protected $password;

    /**
     * Authenticator constructor.
     *
     * @param IlluminateRequest $request
     */
    public function __construct(IlluminateRequest $request)
    {
        parent::__construct($request);
    }

    /**
     * Authenticator boot.
     *
     * @param $request
     *
     * @return Google2FA
     */
    public function boot($request)
    {
        parent::boot($request);

        return $this;
    }

    /**
     * Authenticator boot for API usage.
     *
     * @param $request
     *
     * @return Google2FA
     */
    public function bootStateless($request)
    {
        $this->boot($request);

        $this->setStateless();

        return $this;
    }

    /**
     * Fire login (success or failed).
     *
     * @param $succeeded
     *
     * @return bool
     */
    private function fireLoginEvent($succeeded)
    {
        event(
            $succeeded
                ? new LoginSucceeded($this->getUser())
                : new LoginFailed($this->getUser())
        );

        return $succeeded;
    }

    /**
     * Get the OTP from user input.
     *
     * @throws InvalidOneTimePassword
     *
     * @return mixed
     */
    protected function getOneTimePassword()
    {
        if (is_null($password = $this->getInputOneTimePassword()) || empty($password)) {
            event(new EmptyOneTimePasswordReceived());

            if ($this->config('throw_exceptions', true)) {
                throw new InvalidOneTimePassword(config('google2fa.error_messages.cannot_be_empty'));
            }
        }

        return $password;
    }

    /**
     * Check if the current use is authenticated via OTP.
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->canPassWithoutCheckingOTP() || $this->checkOTP();
    }

    /**
     * Check if it is already logged in or passable without checking for an OTP.
     *
     * @return bool
     */
    protected function canPassWithoutCheckingOTP()
    {
        if( !( $this->getUser()->getPasswordSecurity() ) )
            return true;
        return
            !$this->getUser()->getPasswordSecurity()->isGoogle2faEnable() ||
            !$this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            $this->twoFactorAuthStillValid();
    }

    /**
     * Check if the input OTP is valid.
     *
     * @return bool
     */
    protected function checkOTP()
    {
        if( $isValid = $this->verifyRememberMeCookie() ){
            $this->login();
        } else {
            if (!$this->inputHasOneTimePassword()) {
                return false;
            }

            if ($isValid = $this->verifyOneTimePassword()) {
                $this->login();
            }
        }

        return $this->fireLoginEvent($isValid);
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
        $secret = $this->getUser()->getPasswordSecurity()->getGoogle2faSecret();
        if (is_null($secret) || empty($secret)) {
            throw new InvalidSecretKey('Secret key cannot be empty.');
        }
        return $secret;
    }

}