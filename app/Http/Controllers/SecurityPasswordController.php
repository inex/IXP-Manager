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

use IXP\Support\Google2FAAuthenticator;

use Entities\{
    PasswordSecurity    as PasswordSecurityEntity,
    User                as UserEntity,
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

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

    public function superuserVerification( Request $request ) {

        $user = $request->user();

        if( $user->isSuperUser() && config( "google2fa.superuser_required" ) && ( !$request->user()->getPasswordSecurity() || !$request->user()->getPasswordSecurity()->isGoogle2faEnable() ) ){
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
     * Create Password Security if needed and generate a QR code
     *
     * @param UserEntity $user
     * @return string
     * @throws
     */
    public function generateQRcode( UserEntity $user ){

        $google2fa = app('pragmarx.google2fa');

        if( !$user->getPasswordSecurity() ) {

            // Add the secret key to the registration data
            $ps = new PasswordSecurityEntity;
            D2EM::persist( $ps );

            $ps->setUser( $user );
            $ps->setGoogle2faSecret( $google2fa->generateSecretKey() );
            $ps->setGoogle2faEnable( false );
            $ps->setCreatedAt( now() );
            $ps->setUpdatedAt( now() );

            D2EM::flush();
            D2EM::refresh( $user );
        }

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
     * @return RedirectResponse
     *
     * @throws
     */
    public function enable2fa( Request $request )
    {
        /** @var UserEntity $user */
        $user = $request->user();

        $google2fa = app('pragmarx.google2fa');

        $code = str_replace(' ', '', $request->input('one_time_password' ) );

        if( !$google2fa->verifyKey( $user->getPasswordSecurity()->getGoogle2faSecret(), $code ) ) {
            AlertContainer::push( "Invalid Verification Code, Please try again.", Alert::DANGER );

            // Reset the input
            $request->merge(['one_time_password' => '']);

            return $this->show2faForm( $request );
        }

        $user->getPasswordSecurity()->setGoogle2faEnable( true );
        D2EM::flush();

        // Authenticate the user via 2fa as the code is valid
        $authenticator = app(Google2FAAuthenticator::class)->boot( $request );
        $authenticator->login();

        AlertContainer::push( "2FA is Enabled Successfully.", Alert::SUCCESS );

        if( Session::exists( "intended.2fa" ) ) {
            return redirect( Session::pull( "intended.2fa" ) );
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
    public function delete2fa( Request $request ) : RedirectResponse
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
                abort(404, 'User not found');
            }
        } else {
            $ps = $user->getPasswordSecurity();
        }

        // getting the Password Security user
        $psUser = $ps->getUser();

        // If the password security is not null and it password security is enable we can delete
        if( $ps && $ps->isGoogle2faEnable() ){
            D2EM::remove( $ps );
            D2EM::flush();
            AlertContainer::push( '2FA is now deleted', Alert::SUCCESS );
        }

        // If the logged user delete his own service security then logout the 2FAAuthenticator
        if( $user->getId() == $psUser->getId() ){
            // Log out the user via 2fa
            $authenticator = app(Google2FAAuthenticator::class)->boot( $request );
            $authenticator->logout();
        }


        return Redirect::to( request()->headers->get('referer', "" ) );
    }
}