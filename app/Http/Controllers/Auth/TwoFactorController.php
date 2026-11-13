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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use IXP\Events\Auth\TwoFactorLocked;
use IXP\Http\Requests\Auth\VerifyTwoFactorToken;
use IXP\Models\User;
use IXP\Models\User2FA;
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
        if( $this->shouldntRequest2fa( $user, $authenticator ) ) {
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
            Log::warning( 'Two factor attempt refused for user [' . $user->username . '] from IP [' . ixp_get_client_ip() . '] - two factor authentication is locked' );
            return $this->sendLockoutResponse();
        }

        // Perform the 2FA token check.
        if( !$authenticator->isAuthenticated() ) {
            $this->incrementFailedAttempts( $user );

            if( $this->isLockedOut( $user ) ) {
                return $this->sendLockoutResponse();
            }

            AlertContainer::push( "The one time password entered was wrong.", Alert::DANGER );
            return redirect()->back();
        }

        $this->resetFailedAttempts( $user );

        return redirect()->intended('');
    }

    private function shouldntRequest2fa( User $user, Google2FAAuthenticator $authenticator ): bool
    {
        return !$user->user2FA || !$user->user2FA->isEnabled() || $authenticator->canPassWithoutOtp();
    }

    private function isLockedOut( User $user ): bool
    {
        return $user->user2FA?->fail_count >= config('auth.lockout.two_factor_attempts');
    }

    private function incrementFailedAttempts(User $user): void
    {
        $user2fa = $user->user2FA;
        DB::transaction( function () use ($user2fa) {
            // effectively a refresh() on $user2fa, but takes values from the locking read.
            $locked = User2FA::whereId( $user2fa->id )->lockForUpdate()->firstOrFail();
            $user2fa->setRawAttributes( $locked->getAttributes(), true );
            $user2fa->fail_count++;
            $user2fa->save();
        } );

        if ($user2fa->fail_count === config('auth.lockout.two_factor_attempts')) {
            event( new TwoFactorLocked( $user ) );
        }
    }

    /**
     * Reset 2FA lockout state
     */
    private function resetFailedAttempts( User $user ): void
    {
        $user->user2FA->resetLockoutState();
    }

    /**
     * Log the user out (also cycling any remember me token) and push them back to the login form.
     */
    private function sendLockoutResponse(): RedirectResponse
    {
        Auth::logout();

        AlertContainer::push(
            "Too many failed two factor authentication attempts. You have been logged out. See your email for more info",
            Alert::DANGER
        );

        return redirect()->route( 'login@showForm' );
    }
}