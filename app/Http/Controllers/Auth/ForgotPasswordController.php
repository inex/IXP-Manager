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

use D2EM, Password, Redirect;

use IXP\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use IXP\Events\Auth\ForgotUsername as ForgotUsernameEvent;

use Entities\{
    User    as UserEntity
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Http\Requests\Auth\{
    ForgotUsername as ForgotUsernameRequest
};

class ForgotPasswordController extends Controller
{

    protected $redirectTo = 'auth/login';

    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */
    use SendsPasswordResetEmails;

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
     * Display the forgot password form
     *
     * @return  View
     */
    public function showLinkRequestForm() : View {
        return view( 'auth/forgot-password' );
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request){

        $this->validate($request, ['username' => 'required'] );

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('username')
        );


        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse($response)
    {
        AlertContainer::push( 'The reset link have been sent to you email address.', Alert::SUCCESS );
        return back();
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        AlertContainer::push( "We can't find a user with that username" , Alert::DANGER );
        return back();
    }


    /**
     * Display the forgot username form
     *
     * @return  View
     */
    public function showUsernameForm() : View{
        return view( 'auth/forgot-username' );
    }

    /**
     * Send the email with the list of username for an email address
     *
     * @param   ForgotUsernameRequest $request instance of the current HTTP request
     *
     * @return RedirectResponse
     */
    public function sendUsernameEmail( ForgotUsernameRequest $request ) : RedirectResponse{

        $users = D2EM::getRepository( UserEntity::class )->findBy( [ "email" => $request->input( "email" ) ] );

        if( count( $users ) ){

            event( new ForgotUsernameEvent( $users, $request->input( "email" ) ) );
        }

        AlertContainer::push( 'If your email matches user(s) on the system, then an email listing those users has been sent to you.', Alert::SUCCESS );

        return Redirect::to( route( "login@showForm" ));

    }

}
