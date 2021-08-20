<?php

namespace IXP\Http\Controllers\Auth;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GpNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, Socialite, Str;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\Routing\Redirector;

use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Models\{
    Aggregators\UserAggregator,
    Customer,
    CustomerToUser,
    User
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Symfony\Component\HttpFoundation\{
    RedirectResponse as RedirectResponseFoundation,
    Response
};

/**
 * LoginController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Auth
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
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
    public function username(): string
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
        if( !session()->has('url.intended') && Str::startsWith(url()->previous(), url(''))) {
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
        return view( 'auth/login' );
    }

    /**
     * The user has been authenticated.
     *
     * @param Request       $r
     * @param User          $user
     *
     * @return Response|void
     *
     */
    protected function authenticated( Request $r, User $user )
    {
        // Check if the user has Customer(s) linked
        if( !$user->customers()->count() ) {
            return $this->logout( $r, [ 'message' => "Your user account is not associated with any " . config( "ixp_fe.lang.customer.many" ) . ".", 'class' => Alert::DANGER ] );
        }

        $activeCusts = $user->customers()->active()->notDeleted()->get();

        // Check if the user has active Customer(s) linked
        if( !$activeCusts->count() ) {
            return $this->logout( $r, [ 'message' => "Your user account is not associated with any active " . config( "ixp_fe.lang.customer.many" ) . ".", 'class' => Alert::DANGER ] );
        }

        $newCust = $activeCusts->first();

        // Check if the default customer for the user is not active
        if( ( $cust = $user->customer ) && $user->customer()->active()->notDeleted()->doesntExist() ){
            $user->custid = $newCust->id;
            $user->save();
            AlertContainer::push( "The default " . config( "ixp_fe.lang.customer.one" ) . " " . ucfirst( $cust->abbreviatedName ) . " is no longer active. Your default " . config( "ixp_fe.lang.customer.one" ) . " is now " . ucfirst( $newCust->abbreviatedName ) . "." , Alert::WARNING );
        }

        $c2u = CustomerToUser::where( [ 'user_id' => $user->id ] )->where( [ "customer_id" => $user->custid ] )->first();

        // Check if the user has a default customer OR if the default customer is no longer in the C2U, then assign one
        if( !$user->customer || !$c2u ){
            $user->custid = $newCust->id;
            $user->save();
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request       $r
     * @param string|null   $msg
     *
     * @return Response
     */
    protected function sendFailedLoginResponse( Request $r, $msg = null ) : Response
    {
        AlertContainer::push( $msg ?? "Invalid username or password. Please try again." , Alert::DANGER );
        return redirect()->back()->withInput( $r->only('username') );
    }

    /**
     * Log the user out of the application.
     *
     * @param   Request      $r
     * @param   array|null   $customMessage Custom message to display
     *
     * @return Response
     */
    public function logout( Request $r, $customMessage = null ) : Response
    {
        $this->guard()->logout();
        $r->session()->invalidate();

        AlertContainer::push( $customMessage ? $customMessage[ "message" ] : "You have been logged out." , $customMessage ? $customMessage[ "class" ] : Alert::SUCCESS );
        return redirect('');
    }

    /**
     * Redirect the user to the PeeringDB authentication page.
     *
     * @return RedirectResponseFoundation
     */
    public function peeringdbRedirectToProvider(): RedirectResponseFoundation
    {
        if( Auth::check() ) {
            AlertContainer::push( "You are already logged in - Login via PeeringDB aborted." , Alert::WARNING );
            return redirect('');
        }

        if( config( 'auth.peeringdb.enabled' ) ) {
            return Socialite::driver( 'peeringdb' )->redirect();
        }

        AlertContainer::push( "Login with PeeringDB not enabled." , Alert::DANGER );
        return redirect( route('login@showForm' ) );
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
     * @param Request   $r
     *
     * @return RedirectResponse|Redirector|Response
     *
     * @throws
     */
    public function peeringdbHandleProviderCallback( Request $r )
    {
        if( Auth::check() ) {
            AlertContainer::push( "You are already logged in - Login via PeeringDB aborted." , Alert::WARNING );
            return redirect('');
        }

        if( !config( 'auth.peeringdb.enabled' ) ) {
            AlertContainer::push( "Login with PeeringDB not enabled.", Alert::DANGER );
            return redirect()->route( 'login@showForm' );
        }

        $suser = Socialite::driver('peeringdb' )->user();

        // valid PeeringDB login with affiliations?
        if( !$suser || !isset( $suser->user ) || !isset( $suser->user['networks'] ) || !is_array( $suser->user['networks'] ) || !count( $suser->user['networks'] ) ) {
            AlertContainer::push( "Login with PeeringDB failed or you have no existing affiliations.", Alert::DANGER );
            return redirect()->route( 'login@login' );
        }

        // user needs to be verified with PeeringDB first:
        if( !$suser->user['verified_user'] || !$suser->user['verified_email'] ) {
            return $this->sendFailedLoginResponse( $r, 'Your PeeringDB user or email address has not been validated. Please complete your PeeringDB account registration first.' );
        }

        $result = UserAggregator::findOrCreateFromPeeringDb( $suser->user );

        if( $result['user'] === null || !( $result['user'] instanceof User ) ) {
            return $this->sendFailedLoginResponse( $r, 'Login with PeeringDB failed. Most likely there are no ' . config( "ixp_fe.lang.customer.many" ) . ' at this IXP that match your PeeringDB affiliation(s). '
                . 'If you believe this to be an error or would like to get access to your account, please contact our support team.' );
        }

        /** @var Customer $c */
        foreach( $result['added_to'] as $c ) {
            AlertContainer::push( "Your PeeringDB affiliation with {$c->getFormattedName()} has been added to IXP Manager.", Alert::SUCCESS );
        }

        foreach( $result['removed_from'] as $c ) {
            AlertContainer::push( "Your PeeringDB affiliation with {$c->getFormattedName()} has been removed from IXP Manager as you are no longer affiliated with this network on PeeringDB.", Alert::WARNING );
        }

        Auth::login( $result['user'] );
        $this->authenticated( $r, $result['user'] );
        return redirect('');
    }
}