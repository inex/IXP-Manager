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

use D2EM;

use Illuminate\Http\Request;
use IXP\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Entities\{
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity,
    UserLoginHistory    as UserLoginHistoryEntity
};

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
            session(['url.intended' => url()->previous()]);
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
        if( config( "ixp_fe.login_history.enabled" ) ) {

            $log = new UserLoginHistoryEntity;
            D2EM::persist( $log );

            $log->setAt(    new \DateTime() );
            $log->setIp(    request()->ip() );
            $log->setUser(  $user           );

            D2EM::flush();
        }

        if( method_exists( $user, 'hasPreference' ) ) {
            $user->setPreference( 'auth.last_login_from', request()->ip() );
            $user->setPreference( 'auth.last_login_at',   time()                );
        }

        // Check if the user has Customer(s) linked
        if( count( $user->getCustomers() ) > 0 ){

            // Check if the user has a default Customer, if not assign one
            if( !$user->getCustomer() ){
                $user->setCustomer( $user->getCustomers()[0] );
                D2EM::flush();
            } else{
                // Check If the link between the customer and the user is still valid (database row in the customer2user table)
                if( !D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ "customer" => $user->getCustomer(), "user" => $user ] ) ){
                    $user->setCustomer( $user->getCustomers()[0] );
                    D2EM::flush();
                }

            }

        } else {
            // No customer linked, logout
            $this->logout( $request, [ 'message' => "No customer assigned to this user. Please contact the support Team.", 'class' => Alert::DANGER ] );
        }

    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    protected function sendFailedLoginResponse(Request $request){
        AlertContainer::push( "Invalid username or password. Please try again." , Alert::DANGER );
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

}