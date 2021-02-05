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

use Former, Hash;

use Illuminate\Http\{
    JsonResponse,
    RedirectResponse
};

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\View\View;

use IXP\Http\Controllers\Controller;

use IXP\Events\Auth\PasswordReset   as PasswordResetEvent;

use IXP\Models\User;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * ResetPasswordController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\Auth
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ResetPasswordController extends Controller
{
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
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest' );
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param Request       $r
     * @param  string|null  $token
     *
     * @return View
     */
    public function showResetForm( Request $r, string $token = null ): View
    {
        Former::populate( [
            'username'      => request()->old( 'username',  $r->username    ),
            'token'         => request()->old( 'token',     $token          ),
        ] );

        return view('auth/reset-password')->with( [
            'token'     => $token,
            'username'  => $r->username
        ] );
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'token'     => 'required',
            'username'  => 'required|string',
            'password'  => 'required|confirmed|min:8',
        ];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param Request $r
     *
     * @return array
     */
    protected function credentials( Request $r ): array
    {
        return $r->only(
            'username', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  User     $user
     * @param  string   $password
     *
     * @return void
     *
     * @throws
     */
    protected function resetPassword( User $user, string $password ): void
    {
        $user->password = Hash::make( $password );
        $user->save();

        event( new PasswordResetEvent( $user ) );
        $this->redirectTo = route("login@showForm" ) . '?username=' . $user->username ;
    }
    /**
     * Get the response for a failed password reset.
     *
     * @param Request   $r
     * @param  string   $response
     *
     * @return RedirectResponse
     */
    protected function sendResetFailedResponse(Request $r, string $response ): RedirectResponse
    {
        AlertContainer::push( trans( $response ) , Alert::DANGER );
        return redirect()->back()->withInput( $r->only('username' ) );
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param Request   $r
     * @param  string   $response
     *
     * @return RedirectResponse|JsonResponse
     */
    protected function sendResetResponse( Request $r, string $response )
    {
        AlertContainer::push( trans( $response ) , Alert::SUCCESS );
        return redirect( $this->redirectPath() );
    }
}