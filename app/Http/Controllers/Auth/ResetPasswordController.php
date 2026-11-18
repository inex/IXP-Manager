<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Http\Controllers\Auth;

use Former\Facades\Former;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use IXP\Events\Auth\PasswordReset as PasswordResetEvent;
use IXP\Http\Controllers\Controller;
use IXP\Models\User;
use IXP\Utils\PasswordPolicyExplainer;
use IXP\Utils\View\Alert\{Alert, Container as AlertContainer};

/**
 * ResetPasswordController
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Thomas Kerin <thomas@islandbridgenetworks.ie>
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
    protected string $redirectTo = '';

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
     * @param Request                    $request
     * @param PasswordPolicyExplainer    $policyExplainer
     * @param string|null                $token
     *
     * @return View
     */
    public function showResetForm( Request $request, PasswordPolicyExplainer $policyExplainer, #[\SensitiveParameter] ?string $token = null ): View
    {
        Former::populate( [
            'username'      => request()->old( 'username',  $request->username    ),
            'token'         => request()->old( 'token',     $token                ),
        ] );

        $messages = $policyExplainer->explainDefault();

        return view('auth/reset-password')->with( [
            'token'     => $token,
            'username'  => $request->username,
            'policy'    => $messages,
        ] );
    }

    /**
     * Get the password reset validation rules.
     *
     * @return ((Password|string)[]|string)[]
     *
     * @psalm-return array{token: 'required', username: 'required|string', password: list{'required', 'confirmed', Password}}
     */
    protected function rules(): array
    {
        return [
            'token'     => 'required',
            'username'  => 'required|string',
            'password'  => [ 'required', 'confirmed', Password::default() ],
        ];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function credentials( Request $request ): array
    {
        return $request->only(
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
     */
    protected function resetPassword( User $user, #[\SensitiveParameter] string $password ): void
    {
        $user->password = Hash::make( $password );
        $user->save();

        event( new PasswordResetEvent( $user ) );
        $this->redirectTo = route("login@showForm" ) . '?username=' . $user->username ;
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param Request   $request
     * @param  string   $response
     *
     * @return RedirectResponse
     */
    protected function sendResetFailedResponse( Request $request, string $response ): RedirectResponse
    {
        AlertContainer::push( trans( $response ) , Alert::DANGER );
        return redirect()->back()->withInput( $request->only('username' ) );
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param Request $request
     * @param string  $response
     * @return RedirectResponse
     */
    protected function sendResetResponse( Request $request, string $response ): RedirectResponse
    {
        AlertContainer::push( trans( $response ) , Alert::SUCCESS );
        return redirect( $this->redirectPath() );
    }
}