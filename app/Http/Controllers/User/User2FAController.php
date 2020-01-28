<?php

namespace IXP\Http\Controllers\User;

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

use Auth, D2EM, Hash, Redirect;

use Illuminate\Auth\Recaller;
use Illuminate\Contracts\View\Factory;
use Entities\{
    User2FA             as User2FAEntity,
    User                as UserEntity
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use PragmaRX\Google2FALaravel\Support\Authenticator as GoogleAuthenticator;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use IXP\Http\Controllers\Controller;

use IXP\Exceptions\User2FAException;

/**
 * Security Password Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 *
 * @category   Admin
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class User2FAController extends Controller
{
    /**
     * Configure 2FA
     *
     * @param Request $request
     * @return Factory|View
     */
    public function configure( Request $request )
    {
        if( !$request->user()->getUser2FA() ) {
            $this->generateUser2FA( $request->user() );
        }

        return view( 'user/2fa/configure' )->with([
            'user'          => $request->user(),
            'qrcode'        => $this->generateQRCode( $request->user() ),
        ]);
    }

    /**
     * Enable 2FA for a user
     *
     * @param Request $request
     * @return RedirectResponse|View
     * @throws
     */
    public function enable( Request $request )
    {
        if( !$this->checkUserPassword( $request ) || !$this->testOneTimeCode( $request ) ) {
            return redirect(route('2fa@configure'));
        }

        $request->user()->getUser2FA()->setEnabled( true );

        // We also need to mark the current session as 2fa complete:
        if( $r = $request->cookies->get(Auth::getRecallerName()) ) {
            $recaller = new Recaller($r);
            $urt = d2r( 'UserRememberToken' )->findOneBy( [ 'token' => $recaller->token() ] );

            if( $urt ) {
                $urt->setIs2faComplete(true);
            }
        }

        D2EM::flush();

        $this->google2faLogin($request);
        AlertContainer::push( "2FA successfully enabled.", Alert::SUCCESS );
        return Redirect::to('');
    }

    /**
     * Delete a user's 2fa configuration
     *
     * @param Request $request
     * @return RedirectResponse|View
     * @throws
     */
    public function delete( Request $request )
    {
        if( !( $user = d2r('User')->find( $request->input('id') ) ) ) {
            abort( 404, 'Unknown user' );
        }

        if( !$request->user()->isSuperUser() ) {
            abort( 403, 'You are not authorised to perform this action' );
        }

        D2EM::remove( $user->getUser2FA() );
        $user->setUser2FA(null);
        D2EM::flush();

        AlertContainer::push( "2FA deleted for " . $user->getUsername() . ".", Alert::SUCCESS );
        return Redirect::back();
    }

    /**
     * Enable 2FA for a user
     *
     * @param Request $request
     * @return RedirectResponse|View
     * @throws
     */
    public function disable( Request $request )
    {
        if( !$this->checkUserPassword( $request ) ) {
            return redirect(route('2fa@configure'));
        }

        D2EM::remove( $request->user()->getUser2FA() );
        $request->user()->setUser2FA(null);
        D2EM::flush();

        $this->google2faLogin($request, false);
        AlertContainer::push( "2FA successfully disable.", Alert::SUCCESS );
        return Redirect::to(route('profile@edit'));
    }



    /**
     * Create Password Security if needed and generate a QR code
     *
     * @param UserEntity $user
     *
     * @return void
     *
     * @throws
     */
    private function generateUser2FA( UserEntity $user )
    {
        $google2fa = app( 'pragmarx.google2fa' );

        if( !$user->getUser2FA() ) {
            $u2fa = new User2FAEntity;
            $u2fa->setUser( $user );
            $u2fa->setSecret( $google2fa->generateSecretKey( 32 ) );
            $u2fa->setEnabled( false );
            D2EM::persist( $u2fa );
            D2EM::flush();

            // Refresh the user object in order to get the 2FA secret code to generate the qr code
            D2EM::refresh( $user );
        }
    }

    /**
     * Get a QR Code object for the user's 2fa settings
     *
     * @param UserEntity $user
     * @return mixed
     */
    private function generateQRCode( UserEntity $user )
    {
        $google2fa = app( 'pragmarx.google2fa' );

        return $google2fa->getQRCodeInline(
            config( 'identity.sitename' ),
            $user->getEmail(),
            $user->getUser2FA()->getSecret()
        );
    }

    /**
     * Check if the user password is valid
     *
     * @param Request $request
     * @return bool
     */
    private function checkUserPassword( $request ): bool
    {
        if( !Hash::check( $request->input('password'), $request->user()->getPassword() ) ) {
            AlertContainer::push( 'Incorrect user password - please check your password and try again.', Alert::DANGER );
            return false;
        }

        return true;
    }


    /**
     * Test if the one time code is valid
     *
     * @param Request $request
     * @return bool
     * @throws User2FAException
     */
    private function testOneTimeCode( Request $request )
    {
        if( !$request->user()->getUser2FA() ) {
            throw new User2FAException('Attempt to test OTC but no 2FA record exists');
        }

        $google2fa = app( 'pragmarx.google2fa' );

        if( !$google2fa->verifyKey( $request->user()->getUser2FA()->getSecret(), $request->input('one_time_password') ) ) {
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
     * @param $request
     * @param bool $login
     */
    private function google2faLogin( $request, bool $login = true )
    {
        $authenticator = new GoogleAuthenticator($request);
        $login ? $authenticator->login() : $authenticator->logout();
    }



}