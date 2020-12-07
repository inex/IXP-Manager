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
use Illuminate\Auth\SessionGuard as BaseGuard;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use IXP\Exceptions\GeneralException;

use IXP\Models\{
    User,
    UserRememberToken
};

use PragmaRX\Google2FALaravel\Support\Authenticator as GoogleAuthenticator;

/**
 * Class SessionGuard
 *
 * A small set of functions we need to override from Laravel's SessionGuard to allow for IXP Manager's
 * user session management functionality.
 *
 * @see        https://docs.ixpmanager.org/dev/authentication/
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @package    IXP\Services\Auth
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class SessionGuard extends BaseGuard
{
    /**
     * @var UserRememberToken;
     */
    protected $userRememberToken;

    /**
     * Get the currently authenticated user.
     *
     * Overrides Laravel's default version which is a single shared token so we
     * can have a per-browser / device token.
     *
     * We need to override so we can /immediately/ log out users if the current user session
     * was deleted (by the user) from another session.
     *
     * @return User|AuthenticatableContract|void|null
     */
    public function user()
    {
        if( $this->loggedOut ){
            return;
        }

        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null( $this->user ) ) {
            return $this->user;
        }

        $id = $this->session->get( $this->getName() );
        $recaller = $this->recaller();

        // First we will try to load the user using the identifier in the session if
        // one exists. Otherwise we will check for a "remember me" cookie in this
        // request, and if one exists, attempt to retrieve the user using that.
        if (! is_null( $id ) && $this->user = $this->provider->retrieveById( $id ) ) {

            // User has local session - make sure it hasn't been invalidated if a remember me cookie exists.
            // This is the bit we added to allow a user to invalidate other sessions via the UI.
            if( $recaller ) {
                $urt = UserRememberToken::whereToken( $recaller->token() )->first();

                if( !$urt || $urt->expired() ) {
                    $this->logout();
                    return null;
                }
            }

            $this->fireAuthenticatedEvent( $this->user );
        }

        // If the user is null, but we decrypt a "recaller" cookie we can attempt to
        // pull the user data on that cookie which serves as a remember cookie on
        // the application. Once we have a user we can return it to the caller.
        if( is_null( $this->user ) && ! is_null( $recaller ) ) {
            $this->user = $this->userFromRecaller( $recaller );

            if( $this->user ) {
                $this->updateSession( $this->user->getAuthIdentifier() );

                // Get the UserRememberToken and, if 2fa has been completed, don't redo it:
                if( $this->user->user2FA && $this->user->user2FA->enabled ) {
                    $urt = UserRememberToken::whereToken( $recaller->token() )->first();

                    if( $urt && $urt->is_2fa_complete ) {
                        $authenticator = new GoogleAuthenticator( $this->request );
                        $authenticator->login();
                    }
                }

                $this->fireLoginEvent( $this->user, true);
            }
        }
        return $this->user;
    }


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
     * @param AuthenticatableContract $user
     *
     * @return void
     */
    protected function ensureRememberTokenIsSet( AuthenticatableContract $user ): void
    {
        if( !$this->userRememberToken ) {
            // The addRememberToken() creates a new UserRememberToken for the user
            $this->userRememberToken = $this->provider->addRememberToken($user);
            $this->provider->purgeExpiredRememberTokens( $user );
        }
    }

    /**
     * Queue the recaller cookie into the cookie jar
     *
     * We need to override this function as the parent version calls `$user->getRememberToken()`
     * which, in Laravel, is a single token. In our case we want a per-browser / session token.
     *
     * @param   AuthenticatableContract $user
     *
     * @return  void
     *
     * @throws GeneralException
     */
    protected function queueRecallerCookie( AuthenticatableContract $user ): void
    {
        // we shouldn't have called this function unless a UserRememberToken has been created
        // (or so barryo's understanding as of 20200127). So we'll throw an exception if that happens
        // and fix this then.
        if( !$this->userRememberToken ) {
            throw new  GeneralException( 'UserRememberToken not already created in queueRecallerCookie() ??' );
        }

        $this->getCookieJar()->queue($this->createRecaller(
            $user->getAuthIdentifier() . '|' . $this->userRememberToken->token . '|' . $user->getAuthPassword()
        ));
    }

    /**
     * Refresh the "remember me" token for the user.
     *
     * We need to override this function and use it to delete the user's remember token
     * as our system does not have a single token (where cycling the single token would
     * be sufficient).
     *
     * @param AuthenticatableContract $user
     *
     * @return void
     */
    protected function cycleRememberToken( AuthenticatableContract $user ): void
    {
        if( $this->recaller() && $this->recaller()->token() ) {
            foreach( $this->user()->userRememberTokens as $urt ) {
                if( $urt->token === $this->recaller()->token() ) {
                    $urt->delete();
                    break;
                }
            }
        }
    }
}