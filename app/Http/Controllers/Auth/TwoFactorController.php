<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Http\Controllers\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use IXP\Http\Requests\Auth\VerifyTwoFactorToken;
use IXP\Models\User;
use IXP\Services\Auth\Google2FAAuthenticator;
use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

class TwoFactorController
{
    public function showForm( Request $request ): View|RedirectResponse
    {
        $user = Auth::user();
        $authenticator = new Google2FAAuthenticator( $request );

        // If the user has no 2FA setup, or has already completed a 2FA challenge, redirect away.
        if( $this->shouldntRequest2fa( $user, $authenticator) ) {
            return redirect('');
        }

        return view('user.2fa.login-form');
    }

    public function verifyToken( VerifyTwoFactorToken $request ): View|RedirectResponse
    {
        $user = Auth::user();
        $authenticator = new Google2FAAuthenticator( $request );

        // If user has no 2FA setup, or has already completed a 2FA challenge, redirect away.
        if( $this->shouldntRequest2fa( $user, $authenticator ) ) {
            return redirect('');
        }

        // A 2FA challenge is required. If this user is locked out from too many failed
        // attempts, do not even evaluate the OTP - log them out and send them to login.
        if( $this->isLockedOut( $user ) ) {
            return $this->lockoutResponse( $user );
        }

        // Perform the 2FA token check.
        if( !$authenticator->isAuthenticated() ) {
            RateLimiter::hit( $this->failedAttemptsKey( $user ), $this->lockoutMinutes() * 60 );

            if( $this->isLockedOut( $user ) ) {
                return $this->lockoutResponse( $user );
            }

            AlertContainer::push("The one time password entered was wrong.", Alert::DANGER);
            return redirect()->back();
        }

        RateLimiter::clear( $this->failedAttemptsKey( $user ) );

        if( $intendedUrl = session()->pull( "url.intended.2fa" ) ) {
            return redirect()->to( $intendedUrl );
        }
        return redirect( '' );
    }

    private function shouldntRequest2fa( User $user, Google2FAAuthenticator $authenticator ): bool
    {
        return !$user->user2FA || !$user->user2FA->isEnabled() || $authenticator->canPassWithoutOtp();
    }

    /**
     * Cache key for counting failed OTP attempts for a user.
     *
     * Keyed by user (not session / IP) so an attacker holding the password cannot
     * reset the counter by logging in again or rotating IP addresses.
     */
    protected function failedAttemptsKey( User $user ): string
    {
        return '2fa:failed-otp:user:' . $user->id;
    }

    protected function maxAttempts(): int
    {
        return max( 1, config( 'google2fa.ixpm_max_failed_attempts' ) );
    }

    protected function lockoutMinutes(): int
    {
        return max( 1, config( 'google2fa.ixpm_lockout_minutes' ) );
    }

    protected function isLockedOut( User $user ): bool
    {
        return RateLimiter::tooManyAttempts( $this->failedAttemptsKey( $user ), $this->maxAttempts() );
    }

    /**
     * Log the user out (also cycling any remember me token) and push them back to the login form.
     */
    protected function lockoutResponse( User $user ): RedirectResponse
    {
        $minutes = (int)ceil( RateLimiter::availableIn( $this->failedAttemptsKey( $user ) ) / 60 );

        Log::warning( sprintf(
            '2FA: user %s (id %d) locked out after %d failed one time password attempts (from %s)',
            $user->username, $user->id, $this->maxAttempts(), ixp_get_client_ip()
        ) );

        Auth::logout();

        AlertContainer::push(
            "Too many failed two factor authentication attempts. You have been logged out - "
            . "please try again in " . max( 1, $minutes ) . " minute(s).",
            Alert::DANGER
        );

        return redirect()->route( 'login@showForm' );
    }
}