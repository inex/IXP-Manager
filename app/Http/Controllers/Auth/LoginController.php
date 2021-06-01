<?php

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Http\Controllers\Auth;

use Auth, D2EM, Socialite, Session, Str;

use Entities\{
    Customer            as CustomerEntity ,
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity,
    UserLoginHistory    as UserLoginHistoryEntity,
    UserRememberToken  as UserRememberTokenEntity
};

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\Routing\Redirector;
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    /*
     |--------------------------------------------------------------------------
     | Login Controller
     |--------------------------------------------------------------------------
     |
     | This controller handles authenticating users for the application and
     | redirecting them to your home screen. The controller uses a trait
     | to conveniently provide its functionality to your applications.
     |
     */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * THis isn't used unless the RedirectIfAuthenticated middleware's handle() method is coded to use it.
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware( 'guest' )->except( 'logout' );
    }

    /**
     * Show the login form
     *
     * @return View
     */
    public function showLoginForm() : View
    {
        if( !session()->has('url.intended') ) {
            if( Str::startsWith( url()->previous(), url('') ) ) {
                if( config('google2fa.enabled') ) {
                    if( strpos( url()->previous(), route( "2fa@authenticate" ) ) !== false ) {
                        // Store intended url to redirect after login
                        session( [ 'url.intended' => url()->previous() ] );
                        // Store intended url to redirect after 2FA
                        session( [ 'url.intended.2fa' => url()->previous() ] );
                    }
                } else {
                    // Store intended url to redirect after login
                    session( [ 'url.intended' => url()->previous() ] );
                }
            }
        }

        return view( 'auth/login' );
    }


    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param  UserEntity  $user
     *
     * @return Response
     *
     * @throws
     */
    protected function authenticated( Request $request, $user, $viaPeeringDB = false )
    {
        $activeCustomers = $user->getActiveCustomers();
        // Check if the user has Customer(s) linked
        if( !count( $activeCustomers ) ) {
            return $this->logout( $request, [ 'message' => "Your user account is not associated with any " . config( "ixp_fe.lang.customer.many" ) . ".", 'class' => Alert::DANGER ] );
        }

        $newCust = reset( $activeCustomers );/** @var $newCust CustomerEntity */

        // Check if the default customer for the user is not active
        if( ( $cust = $user->getCustomer() ) && !$cust->isActive() ){
            $user->setCustomer( $newCust );
            D2EM::flush();
            AlertContainer::push( "The default " . config( "ixp_fe.lang.customer.one" ) . " " . ucfirst( $cust->getAbbreviatedName() ) . " is no longer active. Your default " . config( "ixp_fe.lang.customer.one" ) . " is now " . ucfirst( $newCust->getAbbreviatedName() ) . "." , Alert::WARNING );
        }

        /** @var CustomerToUserEntity $c2u */
        $c2u = D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "user" => $user , "customer" => $user->getCustomer() ] );

        // Check if the user has a default customer OR if the default customer is no longer in the C2U, then assign one
        if( !$user->getCustomer() || !$c2u ){
            $user->setCustomer( $user->getCustomers()[0] );
            D2EM::flush();
        }

        D2EM::flush();
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request $request
     * @param string|null $msg
     *
     * @return Response
     */
    protected function sendFailedLoginResponse( Request $request, $msg = null ) : Response
    {
        AlertContainer::push( $msg ?? "Invalid username or password. Please try again." , Alert::DANGER );

        return redirect()->back()->withInput( $request->only('username') );
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @param  array|null  $customMessage Custom message to display
     *
     * @return Response
     */
    public function logout( Request $request, $customMessage = null ) : Response
    {
        $this->guard()->logout();
        $request->session()->invalidate();

        AlertContainer::push( $customMessage ? $customMessage[ "message" ] : "You have been logged out." , $customMessage ? $customMessage[ "class" ] : Alert::SUCCESS );
        return redirect('');
    }

    /**
     * Redirect the user to the PeeringDB authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function peeringdbRedirectToProvider()
    {
        if( Auth::check() ) {
            AlertContainer::push( "You are already logged in - Login via PeeringDB aborted." , Alert::WARNING );
            return redirect('');
        }

        if( config( 'auth.peeringdb.enabled' ) ) {
            return Socialite::driver( 'peeringdb' )->redirect();
        }

        AlertContainer::push( "Login with PeeringDB not enabled." , Alert::DANGER );
        return redirect()->route('login@showForm' );
    }

    /**
     * Obtain the user information from PeeringDB.
     *
     * Sample response:
     *
     * User {#1139 ▼
     *  +accessTokenResponseBody: array:5 [▼
     *    "access_token" => "xxxx"
     *    "token_type" => "Bearer"
     *    "expires_in" => 36000
     *    "refresh_token" => "xxxx"
     *    "scope" => "profile email networks"
     *  ]
     *  +token: "xxx"
     *  +refreshToken: "xxx"
     *  +expiresIn: 36000
     *  +id: null
     *  +nickname: null
     *  +name: "Joe Bloggs"
     *  +email: "ixpmanager@example.com"
     *  +avatar: null
     *  +user: array:8 [▼
     *    "family_name" => "Bloggs"
     *    "email" => "ixpmanager@example.com"
     *    "name" => "Joe Bloggs"
     *    "verified_user" => true
     *    "verified_email" => true
     *    "networks" => array:2 [▼
     *      0 => array:4 [▼
     *        "perms" => 1
     *        "id" => 888
     *        "name" => "INEX Route Collectors"
     *        "asn" => 65501
     *      ]
     *      1 => array:4 [▼
     *        "perms" => 1
     *        "id" => 777
     *        "name" => "INEX Route Servers"
     *        "asn" => 65500
     *      ]
     *    ]
     *    "id" => 666
     *    "given_name" => "Joe"
     *  ]
     * }
     * @param Request $request
     * @return RedirectResponse|Redirector|Response
     * @throws
     */
    public function peeringdbHandleProviderCallback( Request $request )
    {
        if( Auth::check() ) {
            AlertContainer::push( "You are already logged in - Login via PeeringDB aborted." , Alert::WARNING );
            return redirect('');
        }

        if( !config( 'auth.peeringdb.enabled' ) ) {
            AlertContainer::push( "Login with PeeringDB not enabled.", Alert::DANGER );
            return redirect()->route( 'login@showForm' );
        }

        $suser = Socialite::driver('peeringdb')->user();

        // valid PeeringDB login with affiliations?
        if( !$suser || !isset( $suser->user ) || !isset( $suser->user['networks'] ) || !is_array( $suser->user['networks'] ) || !count( $suser->user['networks'] ) ) {
            AlertContainer::push( "Login with PeeringDB failed or you have no existing affiliations.", Alert::DANGER );
            return redirect()->route( 'login@login' );
        }

        // user needs to be verified with PeeringDB first:
        if( !$suser->user['verified_user'] || !$suser->user['verified_email'] ) {
            return $this->sendFailedLoginResponse($request, 'Your PeeringDB user or email address has not been validated. Please complete your PeeringDB account registration first.');
        }

        $result = D2EM::getRepository( UserEntity::class )->findOrCreateFromPeeringDb( $suser->user );

        if( $result['user'] === null || !( $result['user'] instanceof UserEntity ) ) {
            return $this->sendFailedLoginResponse($request, 'Login with PeeringDB failed. Most likely there are no ' . config( "ixp_fe.lang.customer.many" ) . ' at this IXP that match your PeeringDB affiliation(s). '
                . 'If you believe this to be an error or would like to get access to your account, please contact our support team.' );
        }

        /** @var CustomerEntity $c */
        foreach( $result['added_to'] as $c ) {
            AlertContainer::push( "Your PeeringDB affiliation with {$c->getFormattedName()} has been added to IXP Manager.", Alert::SUCCESS );
        }

        /** @var CustomerEntity $c */
        foreach( $result['removed_from'] as $c ) {
            AlertContainer::push( "Your PeeringDB affiliation with {$c->getFormattedName()} has been removed from IXP Manager as you are no longer affiliated with this network on PeeringDB.", Alert::WARNING );
        }

        Auth::login( $result['user'] );
        $this->authenticated( $request, $result['user'], true );
        return redirect('');
    }


}
