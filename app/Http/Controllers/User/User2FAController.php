<?php

namespace IXP\Http\Controllers\User;

/*
 * Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, Hash;

use Illuminate\Auth\Recaller;
use Illuminate\Contracts\View\Factory;

use IXP\Models\{
    User,
    User2FA,
    UserRememberToken
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Exceptions\User2FAException;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use PragmaRX\Google2FALaravel\Support\Authenticator as GoogleAuthenticator;

/**
 * Security Password Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\User
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class User2FAController extends Controller
{
    /**
     * Configure 2FA
     *
     * @return View
     */
    public function configure(): View
    {
        $user = Auth::getUser();

        if( !$user->user2FA ) {
            $this->generateUser2FA( $user );
            // Refresh to get the new User2FA created above
            $user->refresh();
        }

        return view( 'user/2fa/configure' )->with([
            'user'          => $user,
            'qrcode'        => $this->generateQRCode( $user ),
        ]);
    }

    /**
     * Enable 2FA for a user
     *
     * @param  Request  $r
     *
     * @return RedirectResponse
     *
     * @throws User2FAException
     */
    public function enable( Request $r ): RedirectResponse
    {
        if( !$this->checkUserPassword( $r ) || !$this->testOneTimeCode( $r ) ) {
            return redirect( route('2fa@configure' ) );
        }

        $r->user()->user2FA->update( [ 'enabled' => true ] );

        // We also need to mark the current session as 2fa complete:
        if( $recallerName = $r->cookies->get( Auth::getRecallerName() ) ) {
            $recaller = new Recaller( $recallerName );

            if( $urt = UserRememberToken::where( 'token', $recaller->token() )->first() ) {
                $urt->update( [ 'is_2fa_complete' => true ] );
            }
        }

        $this->google2faLogin( $r );
        AlertContainer::push( "2FA successfully enabled.", Alert::SUCCESS );
        return redirect('');
    }

    /**
     * Disable 2FA for a user
     *
     * @param Request $r
     *
     * @return RedirectResponse
     */
    public function disable( Request $r ): RedirectResponse
    {
        if( !$this->checkUserPassword( $r ) ) {
            return redirect( route('2fa@configure' ) );
        }
        $r->user()->user2FA->delete();

        $this->google2faLogin( $r, false );
        AlertContainer::push( "2FA successfully disabled.", Alert::SUCCESS );
        return redirect( route('profile@edit' ) );
    }

    /**
     * Remove 2FA for a user
     *
     * @param Request $r
     *
     * @return RedirectResponse
     */
    public function delete( Request $r, User $user ): RedirectResponse
    {
        $user?->user2FA?->delete();

        AlertContainer::push( "2FA successfully deleted for {$user->username}.", Alert::SUCCESS );
        return redirect( route('user@list' ) );
    }



    /**
     * Create Password Security if needed and generate a QR code
     *
     * @param User $user
     *
     * @return void
     */
    private function generateUser2FA( User $user ): void
    {
        $google2fa = app( 'pragmarx.google2fa' );

        if( !$user->user2FA ) {
            User2FA::create([
                'user_id'   => $user->id,
                'enabled'   => false,
                'secret'    => $google2fa->generateSecretKey( 32 ),
            ]);
        }
    }

    /**
     * Get a QR Code object for the user's 2fa settings
     *
     * @param  User  $user
     *
     * @return mixed
     */
    private function generateQRCode( User $user ): mixed
    {
        $google2fa = app( 'pragmarx.google2fa' );

        return $google2fa->getQRCodeInline(
            config( 'identity.sitename' ),
            $user->email,
            $user->user2FA->secret
        );
    }

    /**
     * Check if the user password is valid
     *
     * @param Request $r
     *
     * @return bool
     */
    private function checkUserPassword( Request $r ): bool
    {
        if( !Hash::check( $r->password, $r->user()->password ) ) {
            AlertContainer::push( 'Incorrect user password - please check your password and try again.', Alert::DANGER );
            return false;
        }

        return true;
    }

    /**
     * Test if the one time code is valid
     *
     * @param Request $r
     *
     * @return bool
     *
     * @throws User2FAException
     */
    private function testOneTimeCode( Request $r ): bool
    {
        if( !$r->user()->user2FA ) {
            throw new User2FAException('Attempt to test OTC but no 2FA record exists');
        }

        $google2fa = app( 'pragmarx.google2fa' );

        if( !$google2fa->verifyKey( $r->user()->user2FA->secret, $r->one_time_password ) ) {
            AlertContainer::push( "Incorrect one time code - please check your code and try again.", Alert::DANGER );
            return false;
        }

        return true;
    }

    /**
     * Login or logout via the 2FA authenticator.
     *
     * This essentially decides whether the 2fa middleware will look for a 2fa code on the next request.
     *
     * @param Request   $r
     * @param bool      $login
     *
     * @return void
     */
    private function google2faLogin( Request $r, bool $login = true ): void
    {
        $authenticator = new GoogleAuthenticator( $r );
        $login ? $authenticator->login() : $authenticator->logout();
    }
}