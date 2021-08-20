<?php

namespace IXP\Http\Controllers;

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

use Auth, Hash, Redirect;

use Illuminate\Auth\Recaller;

use IXP\Models\User;
use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use IXP\Http\Requests\Profile\{
    Notification    as NotificationRequest,
    Password        as PasswordRequest,
    Profile         as ProfileRequest
};

use IXP\Models\UserRememberToken;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Profile Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ProfileController extends Controller
{
    /**
     * Display the form to edit a profile
     *
     * @return View
     */
    public function edit(): View
    {
        /** @var User $user */
        $user = Auth::getUser();

        // array used to populate the form to modify user information.
        // former doesn't allow us to populate a form the classic way when there is many forms on the same view.
        $details = [
            'name'                  => $user->name,
            'username'              => $user->username,
            'email'                 => $user->email,
            'authorisedMobile'      => $user->authorisedMobile,
        ];

        $notesNotifications = [
            'notify'                => Auth::getUser()->prefs[ 'notes' ][ 'global_notifs' ] ?? 'none',
        ];

        $mailingListSubscriptions         = [];

        // are we using mailing lists?
        if( config( 'mailinglists.enabled', false ) ) {
            $mailingListSubscriptions = $user->prefs[ 'mailinglist' ] ?? [];
        }

        return view( 'profile/edit' )->with([
            "details"                          =>  $details,
            "notesNotifications"               =>  $notesNotifications,
            "mailingListSubscriptions"         =>  $mailingListSubscriptions,
        ]);
    }

    /**
     * Update password form
     *
     * @param PasswordRequest $r instance of the current HTTP request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function updatePassword( PasswordRequest $r  ): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::getUser();

        // get the token of the current session
        if( $recallerName = $r->cookies->get( Auth::getRecallerName() ) ) {
            $recaller = new Recaller( $recallerName );
            $token = $recaller->token();
        }

        $user->password      = Hash::make( $r->new_password );
        $user->lastupdatedby = $user->id;
        $user->save();

        // Logout all the active session except the current one
        UserRememberToken::where( 'user_id', $user->id )
            ->where( 'token', '!=', $token ?? null )
            ->delete();

        AlertContainer::push( 'Password updated.', Alert::SUCCESS );
        return redirect( route( "profile@edit"  ) );
    }

    /**
     * Update the user's profile (name, email, etc..)
     *
     * @param ProfileRequest $r instance of the current HTTP request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function updateProfile( ProfileRequest $r  ): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::getUser();

        $user->name             = $r->name;
        $user->username         = $r->username;
        $user->email            = $r->email;
        $user->authorisedMobile = $r->authorisedMobile;
        $user->lastupdatedby    = $user->id;
        $user->save();

        AlertContainer::push( 'Profile details updated.', Alert::SUCCESS );
        return redirect( route( "profile@edit"  ) );
    }

    /**
     * Update the user's customer notes notification preference
     *
     * @param NotificationRequest $r instance of the current HTTP request
     *
     * @return RedirectResponse
     *
     * @throws
     */
    public function updateNotificationPreference( NotificationRequest $r ) : RedirectResponse
    {
        $user   = Auth::getUser();
        $prefs  = $user->prefs;

        $prefs[ 'notes' ][ 'global_notifs' ] = $r->notify;

        $user->prefs = $prefs;
        $user->save();

        AlertContainer::push( 'Notification preference updated.', Alert::SUCCESS );
        return Redirect::to( route( "profile@edit"  ) );
    }

    /**
     * Update the mailing list preferences of the user
     *
     * @param   Request $r instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function updateMailingLists( Request $r ) : RedirectResponse
    {
        if( config( 'mailinglists.enabled', false ) ) {
            $user           = Auth::getUser();
            $prefs          = $user->prefs;
            $mailintLists   = [];

            foreach( config( 'mailinglists.lists' ) as $name => $ml ) {
                $mailintLists[ $name ] = $r->input( "ml_" . $name );
            }

            $prefs[ 'mailinglist' ] = $mailintLists;
            $user->prefs = $prefs;
            $user->save();
            AlertContainer::push( 'Mailing list subscriptions updated and will take effect within 12 hours.', Alert::SUCCESS );
            return Redirect::to( route( "profile@edit"  ) );
        }

        AlertContainer::push( 'Mailing list subscriptions is not enabled.', Alert::DANGER );
        return Redirect::to( route( "profile@edit"  ) );
    }
}