<?php

namespace IXP\Http\Controllers;

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

use D2EM, Hash, Redirect, Session;

use Entities\{
    PasswordSecurity    as PasswordSecurityEntity,
    User                as UserEntity,
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Support\Google2FAAuthenticator;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};



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
class SecurityPasswordController extends Controller
{

    /**
     * Display the 2FA validation form via the profil page
     *
     * @param Request $request
     *
     * @return View
     */
    public function show2faForm( Request $request )
    {
        /** @var UserEntity $user */
        $user = $request->user();

        $qrCodeImg = $this->generateQRcode( $user );

        return view('google2fa/qr-code-form')->with([
            'user'          => $user,
            'qrCodeImg'     => $qrCodeImg,
            'ps'            => $user->getPasswordSecurity(),
        ]);

    }

    /**
     * Show the forced 2FA validation form for the superuser
     *
     * @param Request $request
     *
     * @return RedirectResponse|View
     */
    public function superuserVerification( Request $request ) {

        $user = $request->user();

        if( $user->is2FARequired() ) {
            Session::put( "2fa-". $request->user()->getId() , true );

            $qrCodeImg = $this->generateQRcode( $user );

            return view('google2fa/qr-code-form')->with([
                'user'          => $user,
                'qrCodeImg'     => $qrCodeImg,
                'ps'            => $user->getPasswordSecurity(),
            ]);
        }

        return redirect( "" );
    }


    /**
     * Check if the user password is correct
     *
     * @param Request $request
     *
     * @return RedirectResponse|View
     */
    public function checkPassword( Request $request )
    {
        /** @var UserEntity $user */
        $user = $request->user();

        if( !Hash::check( $request->input( 'pass' ), $user->getPassword() ) ) {
            AlertContainer::push( 'Incorrect password entered', Alert::DANGER );
            return Redirect::to( route( "profile@edit" ) );
        }

        return $this->show2faForm( $request );
    }

    /**
     * Create Password Security if needed and generate a QR code
     *
     * @param UserEntity $user
     *
     * @return string
     *
     * @throws
     */
    public function generateQRcode( UserEntity $user ) : String
    {
        $google2fa = app('pragmarx.google2fa');

        // If user doesnot have any PasswordSecurity
        if( !$user->getPasswordSecurity() ) {

            // Create PasswordSecurity object
            $ps = new PasswordSecurityEntity;
            D2EM::persist( $ps );

            $ps->setUser( $user );
            $ps->setGoogle2faSecret( $google2fa->generateSecretKey() );
            $ps->setGoogle2faEnable( false );
            $ps->setCreatedAt( now() );
            $ps->setUpdatedAt( now() );

            D2EM::flush();

            // Refresh the user object in order to get the 2FA secret code to generate the qr code
            D2EM::refresh( $user );
        }

        // Generate the QR Code based on the user data
        $qrCodeImg = $google2fa->getQRCodeInline(
            config('identity.titlename' ),
            $user->getEmail(),
            $user->getPasswordSecurity()->getGoogle2faSecret()
        );

        return $qrCodeImg;
    }

    /**
     * Enable 2FA for a user
     *
     * @param Request $request
     *
     * @return RedirectResponse|View
     *
     * @throws
     */
    public function enable2fa( Request $request )
    {
        /** @var UserEntity $user */
        $user = $request->user();

        $google2fa = app('pragmarx.google2fa');

        // Replace white space if any
        $code = str_replace(' ', '', $request->input('one_time_password' ) );

        // If the one time password is not valid
        if( !$google2fa->verifyKey( $user->getPasswordSecurity()->getGoogle2faSecret(), $code ) ) {
            AlertContainer::push( "Invalid Verification Code, Please try again.", Alert::DANGER );

            // Reset the input
            $request->merge( ['one_time_password' => ''] );

            return $this->show2faForm( $request );
        }

        // Enable the 2FA
        $user->getPasswordSecurity()->setGoogle2faEnable( true );
        D2EM::flush();

        // Authenticate the user via 2FA as the code is valid
        $authenticator = app(Google2FAAuthenticator::class)->boot( $request );
        $authenticator->login();

        AlertContainer::push( "2FA is Enabled Successfully.", Alert::SUCCESS );

        // Redirect the user at the intended URL
        if( Session::exists( "url.intended.2fa" ) ) {
            return redirect( Session::pull( "url.intended.2fa" ) );
        }

        return Redirect::to( route( "profile@edit" ) );

    }

    /**
     * Delete 2FA for a user
     *
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function delete2fa( Request $request ): RedirectResponse
    {
        /** @var UserEntity $user */
        $user = $request->user();

        if( !( Hash::check( $request->get('pass'), $user->getPassword() ) ) ) {
            AlertContainer::push( 'Incorrect password entered', Alert::DANGER );
            return Redirect::to( request()->headers->get('referer', "" ) );
        }

        /** @var PasswordSecurityEntity $ps */
        if( $user->isSuperUser() ) {
            if( !( $ps = D2EM::getRepository( PasswordSecurityEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404, 'Password Security not found');
            }
        } else {
            // Get the PasswordSecurity object of the logged user
            $ps = $user->getPasswordSecurity();
        }

        // getting the Password Security user
        $psUser = $ps->getUser();

        // If the password security is not null and it password security is enable we can delete
        if( $ps && $ps->isGoogle2faEnable() ) {
            D2EM::remove( $ps );
            D2EM::flush();
            AlertContainer::push( '2FA is now deleted', Alert::SUCCESS );
        }

        // If the logged user delete his own Password Security then logout the 2FAAuthenticator
        if( $user->getId() == $psUser->getId() ) {
            // Log out the user via 2fa
            $authenticator = app(Google2FAAuthenticator::class)->boot( $request );
            $authenticator->logout();
        }

        // Redirect where the action have been triggered
        return Redirect::to( request()->headers->get('referer', "" ) );
    }
}