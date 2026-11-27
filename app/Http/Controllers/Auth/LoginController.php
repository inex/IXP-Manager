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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Carbon\CarbonInterval;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\Timebox;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\{
    JsonResponse,
    RedirectResponse,
    Request
};
use Illuminate\View\View;
use IXP\Http\Controllers\Controller;
use IXP\Models\{
    Aggregators\UserAggregator,
    Customer,
    CustomerToUser,
    UnknownUserLoginAttempt,
    User
};
use IXP\Services\Auth\PasswordLockoutPolicy;
use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as RedirectResponseFoundation;

/**
 * LoginController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
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
     * @var string
     */
    protected string $redirectTo = '';

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     *
     * @psalm-return 'username'
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
        // If a user manually navigates to login, record where they came from in url.intended so we can send them back there.
        if( !session()->has('url.intended') &&
            Str::startsWith(url()->previous(), url('')) &&
            !$this->isForbiddenIntendedUrlRoute( url()->previous() )
        ) {
            // Store intended url to redirect after login
            session( [ 'url.intended' => url()->previous() ] );
        }

        return view( 'auth/login' );
    }


    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login( Request $request )
    {
        $this->validateLogin( $request );

        // Failed logins should take a definite duration to prevent timing leaks
        return app( Timebox::class )->call( function( Timebox $timebox ) use ( $request ) {

            $username = $request->post( "username" );
            $user     = User::whereUsername( $username )->first();
            $state    = $user ?? UnknownUserLoginAttempt::whereUsername( $username )->first();

            // Too many failed password attempts? Refuse the login without checking the password.
            // Check then increment is weak against bursts of requests - this risk is accepted for the time being.
            if( $lockoutUntil = $this->lockedOutUntil( $state ) ) {
                Log::warning( 'Login refused for user [' . $username . '] from IP [' . ixp_get_client_ip() . '] - logins are throttled until ' . $lockoutUntil->toDateTimeString() );
                $this->fireLockoutEvent( $request );
                return $this->sendLockoutResponse( $request, $lockoutUntil );
            }

            if( $this->attemptLogin( $request ) ) {
                // A successful login discloses that the account exists anyway, no need to pad it.
                $timebox->returnEarly();

                // Reset state if the attempt was successful.
                $this->guard()->user()->resetLockoutState();
                if( $request->hasSession() ) {
                    $request->session()->put( 'auth.password_confirmed_at', time() );
                }

                return $this->sendLoginResponse( $request );
            }

            // Increment failed password attempts. This may also throttle logins for a while.
            $state = $this->incrementFailedPasswordCount( $username, $state );

            // Check again to see if logins are throttled after this failed attempt, to provide immediate feedback.
            if( $lockoutUntil = $this->lockedOutUntil( $state ) ) {
                $this->fireLockoutEvent( $request );
                return $this->sendLockoutResponse( $request, $lockoutUntil, true );
            }

            return $this->sendFailedLoginResponse( $request );
        }, $this->failedLoginMinMicroseconds() );
    }

    /**
     * Display a message to the user telling them how long they have to wait before trying again.
     */
    protected function sendLockoutResponse( Request $request, CarbonInterface $lockoutUntil, bool $wasAttempt = false ): RedirectResponse
    {
        $seconds = now()->diffInSeconds( $lockoutUntil );

        // produce a duration like '1 second' or '5 minutes', taking care of pluralisation.
        $fmtDuration = $seconds < 30
            ? CarbonInterval::seconds( $seconds )->cascade()->forHumans( [ 'parts' => 1 ] )
            : CarbonInterval::minutes( ceil($seconds / 60) )->cascade()->forHumans( [ 'parts' => 1 ] );

        $message = $wasAttempt ? "Invalid username or password. " : "";
        $message .= "Your account is temporarily locked due to failed login attempts - try again in " . $fmtDuration . ".";
        AlertContainer::push( $message, Alert::DANGER );
        return redirect()->back()->withInput( $request->only('username') );
    }

    /**
     * The user has been authenticated.
     *
     * @param Request       $request
     * @param User          $user
     *
     * @return RedirectResponse|null
     */
    protected function authenticated( Request $request, User $user )
    {
        if ($errors = $this->passwordPolicyErrors($request)) {
            return $this->logout( $request, [ 'message' => "Your password no longer meets the password policy requirements. <a href='" . route("forgot-password@show-form") . "'>Reset your password to login</a>.<br />Reason: " . $errors->first(), 'class' => Alert::WARNING ] );
        }

        // Check if the user has Customer(s) linked
        if( !$user->customers()->count() ) {
            return $this->logout( $request, [ 'message' => "Your user account is not associated with any " . config( "ixp_fe.lang.customer.many" ) . ".", 'class' => Alert::DANGER ] );
        }

        $activeCusts = $user->customers()->active()->notDeleted()->get();

        // Check if the user has active Customer(s) linked
        if( !$activeCusts->count() ) {
            return $this->logout( $request, [ 'message' => "Your user account is not associated with any active " . config( "ixp_fe.lang.customer.many" ) . ".", 'class' => Alert::DANGER ] );
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

        return null;
    }

    /**
     * Send the response after the user was authenticated.
     */
    protected function sendLoginResponse( Request $request ): RedirectResponse|JsonResponse
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts( $request );

        if ( $response = $this->authenticated( $request, $this->guard()->user() ) ) {
            return $response;
        }

        // Catch any unwanted 'intended url' redirects after login. For example, to logout or login form.
        $intended = $request->session()->get( 'url.intended' );
        if ($intended !== null && $this->isForbiddenIntendedUrlRoute( $intended )) {
            $request->session()->forget('url.intended');
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response instance.
     *
     * @param Request       $request
     * @param string|null   $msg
     * @return RedirectResponse
     */
    protected function sendFailedLoginResponse( Request $request, ?string $msg = null ) : RedirectResponse
    {
        AlertContainer::push( $msg ?? "Invalid username or password. Please try again." , Alert::DANGER );
        return redirect()->back()->withInput( $request->only('username') );
    }

    /**
     * Log the user out of the application.
     *
     * @param Request      $request
     * @param array|null   $customMessage Custom message to display
     * @return RedirectResponse
     */
    public function logout( Request $request, ?array $customMessage = null ) : RedirectResponse
    {
        $this->guard()->logout();

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
     * SocialiteProviders\Manager\OAuth2\User {#999 ▼
     *   +id: null
     *   +nickname: null
     *   +name: "IP NOC"
     *   +email: "ipnoc@example.com"
     *   +avatar: null
     *   +user: array:8 [▼
     *     "id" => 1234
     *     "given_name" => "IP NOC"
     *     "family_name" => "NOC"
     *     "name" => "IP NOC"
     *     "verified_user" => true
     *     "email" => "ipnoc@example.com"
     *     "verified_email" => true
     *     "networks" => array:2 [▼
     *         0 => array:5 [▼
     *         "id" => 1234
     *         "name" => "INTERNET LTD"
     *         "asn" => 65500
     *         "perms" => 15
     *         "manage_peering_sessions" => 15
     *       ]
     *       1 => array:5 [▼
     *         "id" => 1235
     *         "name" => "IP LTD"
     *         "asn" => 65501
     *         "perms" => 15
     *         "manage_peering_sessions" => 15
     *       ]
     *     ]
     *   ]
     *   +attributes: array:2 [▼
     *     "name" => "IP NOC"
     *     "email" => "ipnoc@example.com"
     *   ]
     *   +token: "abcdefghijklmnopqrstuvwxyz0123456789"
     *   +refreshToken: "abcdefghijklmnopqrstuvwxyz0123456789"
     *   +expiresIn: 36000
     *   +approvedScopes: array:1 [▼
     *     0 => "profile email networks"
     *   ]
     *   +accessTokenResponseBody: array:5 [▼
     *     "access_token" => "abcdefghijklmnopqrstuvwxyz0123456789"
     *     "expires_in" => 36000
     *     "token_type" => "Bearer"
     *     "scope" => "profile email networks"
     *     "refresh_token" => "abcdefghijklmnopqrstuvwxyz0123456789"
     *   ]
     * }
     *
     * @param Request   $r
     *
     * @throws
     */
    public function peeringdbHandleProviderCallback( Request $r ): RedirectResponse
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
        session([ 'ixpm_external_2fa_completed' => true ]);
        return redirect('');
    }

    /**
     * Check if the password the user logged in with no longer meets the password policy.
     */
    private function passwordPolicyErrors( Request $r ): ?MessageBag
    {
        $validator = $this->getValidationFactory()->make(
            $r->only('password'), ['password' => Password::default()]
        );
        if (!$validator->fails()) {
            return null;
        }
        return $validator->errors();
    }

    /**
     * Some routes should not be taken as the url.intended. This function returns true if $route is one such route.
     */
    private function isForbiddenIntendedUrlRoute(string $route): bool
    {
        $forbidden = [
            route('login@logout'),
            route('login@showForm'),
        ];
        if (config('google2fa.enabled')) {
            $forbidden[] = route('2fa@authenticate');
        }
        return in_array($route, $forbidden, true);
    }

    /**
     * Load failed login minimum duration in microseconds.
     */
    private function failedLoginMinMicroseconds(): int
    {
        return config('auth.failed_login_min_duration');
    }

    /**
     * If logins are throttled for this state, return the time at which the next attempt is allowed.
     */
    private function lockedOutUntil( User|UnknownUserLoginAttempt|null $state ): ?CarbonInterface
    {
        return $state?->no_login_until?->isFuture() ? $state->no_login_until : null;
    }

    /**
     * Increment the failed password count for a given username, and return the updated state.
     * If the count crosses the configured threshold, record the time a login can be attempted again.
     *
     * Failures older than the expiry window no longer count: the count then starts again from one.
     */
    private function incrementFailedPasswordCount( string $username, User|UnknownUserLoginAttempt|null $state ): User|UnknownUserLoginAttempt
    {
        // Create the row for an unknown username outside of the transaction - concurrent first attempts are
        // settled by the unique index and the locking read below then always has a row to lock.
        $state ??= UnknownUserLoginAttempt::firstOrCreate( [ 'username' => $username ] );

        DB::transaction( function() use ( $state ) {
            // effectively a refresh() on $state, but takes values from the locking read.
            $locked = $state->newQuery()->whereKey( $state->getKey() )->lockForUpdate()->firstOrFail();
            $state->setRawAttributes( $locked->getAttributes(), true );

            if( !$state->password_last_fail_at || $state->password_last_fail_at->lt( PasswordLockoutPolicy::expiryCutoff() ) ) {
                $state->password_fail_count = 0;
                $state->no_login_until      = null;

                if( $state instanceof User ) {
                    $state->logins_throttled_notified_at = null;
                }
            }

            $state->password_fail_count++;
            $state->password_last_fail_at = now();
            if( $noLoginUntil = PasswordLockoutPolicy::noLoginUntil( $state->password_fail_count ) ) {
                $state->no_login_until = $noLoginUntil;
            }
            $state->save();
        } );

        // The notification email to a real user is sent by the SendLoginsThrottledNotifications job, not here,
        // so that this request does the same work whether or not the username exists.
        if( $state->password_fail_count === PasswordLockoutPolicy::threshold() ) {
            Log::warning( 'Throttled logins to [' . $username . '] (client IP: ' . ixp_get_client_ip() . ') due to failed login attempts.' );
        }

        return $state;
    }
}
