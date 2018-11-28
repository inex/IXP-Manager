<?php

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
    protected $redirectTo = '/';

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
        if( config( "ixp_fe.login_history.enabled" ) ){

            $log = new UserLoginHistoryEntity;
            D2EM::persist( $log );
            $log->setAt( new \DateTime() );
            $log->setIp( $_SERVER['REMOTE_ADDR'] );
            $log->setUser( $user );
            D2EM::flush();
        }

        if( method_exists( $user, 'hasPreference' ) ) {
            $user->setPreference( 'auth.last_login_from', $_SERVER['REMOTE_ADDR'] );
            $user->setPreference( 'auth.last_login_at',   time()                );
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
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        AlertContainer::push( "You have been logged out." , Alert::SUCCESS );

        return redirect('/');
    }

}