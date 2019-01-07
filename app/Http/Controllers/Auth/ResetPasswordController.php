<?php

namespace IXP\Http\Controllers\Auth;

use D2EM, Former, Hash;

use Illuminate\Support\Str;

use IXP\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\Http\Request;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Entities\{
    User    as UserEntity
};

use IXP\Events\Auth\PasswordReset   as PasswordResetEvent;

class ResetPasswordController extends Controller{

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm( Request $request, $token = null  ){
        $old = request()->old();

        Former::populate([
            'username'         => array_key_exists( 'username',   $old    ) ? $old['username']   : $request->username,
            'token'            => array_key_exists( 'token',      $old    ) ? $old['token']      : $token,
        ]);

        return view('auth/reset-password')->with(
            [ 'token' => $token, 'username' => $request->username ]
        );
    }


    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'username' => 'required|string',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'username', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  UserEntity  $user
     * @param  string  $password
     *
     * @return void
     *
     * @throws
     */
    protected function resetPassword($user, $password)
    {
        $user->setPassword( Hash::make($password) );

        $user->setRememberToken(Str::random(60));

        D2EM::flush();

        event( new PasswordResetEvent( $user ) );

        $this->guard()->login( $user );
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        AlertContainer::push( trans($response) , Alert::DANGER );

        return redirect()->back()
            ->withInput( $request->only('username') );
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        AlertContainer::push( trans($response) , Alert::SUCCESS );
        return redirect( $this->redirectPath() );
    }
}