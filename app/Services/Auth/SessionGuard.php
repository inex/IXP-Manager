<?php

namespace IXP\Services\Auth;

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


use D2EM, Str;

use Entities\UserRememberToken;

use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\UserProvider;

use Symfony\Component\HttpFoundation\{
    Cookie,
    Request
};

use Illuminate\Support\Facades\Session as SessionFacade;

use Illuminate\Auth\SessionGuard as BaseGuard;
use Illuminate\Auth\Events\Logout as LogoutEvent;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use IXP\Exceptions\GeneralException;

class SessionGuard extends BaseGuard
{
    /**
     * @var UserRememberToken;
     */
    protected $userRememberToken;

    /**
     * Create a "remember me" token for the user.
     *
     * This function is called from the parent SessionGuard's login() method as follows:
     *
     * ```
     * // If the user should be permanently "remembered" by the application we will
     * // queue a permanent cookie that contains the encrypted copy of the user
     * // identifier. We will then decrypt this later to retrieve the users.
     * if ($remember) {
     *     $this->ensureRememberTokenIsSet($user);
     *     $this->queueRecallerCookie($user);
     * }
     * ```
     *
     * Overrides Laravel's default version which is a single shared token so we
     * can have a per-browser / device token.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    protected function ensureRememberTokenIsSet(AuthenticatableContract $user)
    {
        if (!$this->userRememberToken) {
            // The addRememberToken() creates a new UserRememberToken for the user
            $this->userRememberToken = $this->provider->addRememberToken( $user, config('auth.guards.web.expire') );
            $this->provider->purgeRememberTokens( $user, true );
        }
    }


    /**
     * Queue the recaller cookie into the cookie jar
     *
     * We need to override this function as the parent version calls `$user->getRememberToken()`
     * which, in Laravel, is a single token. In our case we want a per-browser / session token.
     *
     * @param AuthenticatableContract $user
     * @return void
     * @throws GeneralException
     */
    protected function queueRecallerCookie(AuthenticatableContract $user)
    {
        // we shouldn't have called this function unless a UserRememberToken has been created
        // (or so barryo's understanding as of 20200127). So we'll throw an exception if that happens
        // and fix this then.
        if( !$this->userRememberToken ) {
            throw new  GeneralException( 'UserRememberToken not already created in queueRecallerCookie() ??' );
        }

        $this->getCookieJar()->queue($this->createRecaller(
            $user->getAuthIdentifier().'|'.$this->userRememberToken->getToken().'|'.$user->getAuthPassword()
        ));
    }


    /**
     * Refresh the "remember me" token for the user.
     *
     * We need to override this function and use it to delete the user's remember token
     * as our system does not have a single token (where cycling the single token would
     * be sufficient).
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    protected function cycleRememberToken(AuthenticatableContract $user)
    {
        if( $this->recaller() && $this->recaller()->token() ) {
            foreach( $this->user()->getUserRememberTokens() as $urt ) {
                if( $urt->getToken() === $this->recaller()->token() ) {
                    $user->removeRememberTokens( $urt );
                    D2EM::remove($urt);
                    D2EM::flush();
                    break;
                }
            }
        }
    }


    /**
     * Invalidate other sessions for the current user.
     *
     * The application must be using the AuthenticateSession middleware.
     *
     * @param  string  $password
     * @param  string  $attribute
     * @return bool|null
     */
    public function logoutOtherDevices($password, $attribute = 'password')
    {
        if (! $this->user()) {
            return;
        }

        $this->provider->purgeRememberTokens($this->user());

        return parent::logoutOtherDevices($password, $attribute);
    }

}