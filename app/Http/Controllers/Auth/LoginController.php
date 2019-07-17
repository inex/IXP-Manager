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

use Auth, D2EM, Socialite;

use Illuminate\Http\Request;
use IXP\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Entities\{Customer,
    CustomerToUser as CustomerToUserEntity,
    User as UserEntity,
    User,
    UserLoginHistory as UserLoginHistoryEntity};

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


    public function showLoginForm()
    {
        if( !session()->has('url.intended') ) {
            if( Str::startsWith( url()->previous(), url('') ) ) {
                session(['url.intended' => url()->previous()]);
            }
        }

        return view( 'auth/login' );
    }


    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->input('remember') ? true : false
        );
    }


    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  UserEntity  $user
     * @return mixed
     *
     * @throws
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if the user has Customer(s) linked
        if( count( $user->getCustomers() ) > 0 ) {

            /** @var CustomerToUserEntity $c2u */
            $c2u = D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "user" => $user , "customer" => $user->getCustomer() ] );

            // Check if the user has a default customer OR if the default customer is no longer in the C2U, then assign one
            if( !$user->getCustomer() || !$c2u ){
                $user->setCustomer( $user->getCustomers()[0] );
                D2EM::flush();
                $c2u = D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "user" => $user , "customer" => $user->getCustomer() ] );
            }


            $c2u->setLastLoginAt(  new \DateTime );
            $c2u->setLastLoginFrom( $this->getIP() );

            if( config( "ixp_fe.login_history.enabled" ) ) {

                $log = new UserLoginHistoryEntity;
                D2EM::persist( $log );

                $log->setAt(    new \DateTime() );
                $log->setIp(    $this->getIP() );
                $log->setCustomerToUser(  $c2u  );

                D2EM::flush();
            }

        } else {
            // No customer linked, logout
            $this->logout( $request, [ 'message' => "Your user account is not associated with any customers.", 'class' => Alert::DANGER ] );
        }

        D2EM::flush();
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    protected function sendFailedLoginResponse( Request $request, $msg = null ) {
        AlertContainer::push( $msg ?? "Invalid username or password. Please try again." , Alert::DANGER );
        return redirect()->back()->withInput( $request->only('username') );
    }


    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array|null  $customMessage Custom message to display
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request, $customMessage = null)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        AlertContainer::push( $customMessage ? $customMessage[ "message" ] : "You have been logged out." , $customMessage ? $customMessage[ "class" ] : Alert::SUCCESS );

        return redirect('');
    }





    /**
     * Redirect the user to the PeeringDB authentication page.
     *
     * @return \Illuminate\Http\Response
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
     *
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
            return $this->sendFailedLoginResponse($request, 'Login with PeeringDB failed. Most likely there are no customers at this IXP that match your PeeringDB affiliation(s). '
                . 'If you believe this to be an error or would like to get access to your account, please contact our support team.' );
        }

        /** @var Customer $c */
        foreach( $result['added_to'] as $c ) {
            AlertContainer::push( "Your PeeringDB affiliation with {$c->getFormattedName()} has been added to IXP Manager.", Alert::SUCCESS );
        }

        /** @var Customer $c */
        foreach( $result['removed_from'] as $c ) {
            AlertContainer::push( "Your PeeringDB affiliation with {$c->getFormattedName()} has been removed from IXP Manager as you are no longer affiliated with this network on PeeringDB.", Alert::WARNING );
        }

        Auth::login( $result['user'] );
        $this->authenticated( $request, $result['user'] );
        return redirect('');
    }


}
