<?php

namespace IXP\Http\Controllers;

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

use Auth, D2EM, DateTime, Hash, Redirect;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use Illuminate\View\View;

use Entities\{
    User                as  UserEntity,
    UserRememberToken  as UserRememberTokenEntity,
    Session             as SessionEntity
};

use IXP\Http\Requests\Profile\{
    Notification    as NotificationRequest,
    Password        as PasswordRequest,
    Profile         as ProfileRequest

};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Profile Controller
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Profile
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
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
        /** @var UserEntity $user */
        $user = Auth::getUser();

        // array used to populate the form to modify user information.
        // former doesn't allow us to populate a form the classic way when there is many forms on the same view.
        $profileDetails = [
            'name'                  => $user->getName(),
            'username'              => $user->getUsername(),
            'email'                 => $user->getEmail(),
            'authorisedMobile'      => $user->getAuthorisedMobile(),
        ];

        $customerNotesNotificationOption = [
            'notify'                => Auth::getUser()->getPreference( "customer-notes.notify" ),
        ];

        $mailingListSubscriptions         = [];

        // are we using mailing lists?
        if( $mailingListsEnabled = config( 'mailinglists.enabled', false ) ) {
            foreach( config( "mailinglists.lists") as $name => $ml ) {
                $mailingListSubscriptions[$name] = $user->getPreference( "mailinglist.{$name}.subscribed" );
            }
        }

        return view( 'profile/edit' )->with([
            "profileDetails"                   =>  $profileDetails,
            "customerNotesNotificationOption"  =>  $customerNotesNotificationOption,
            "mailingListSubscriptions"         =>  $mailingListSubscriptions,
            "mailingListsEnabled"              =>  $mailingListsEnabled,
        ]);
    }


    /**
     * Update password form
     *
     * @param PasswordRequest $r instance of the current HTTP request
     * @return RedirectResponse
     *
     * @throws
     */
    public function updatePassword( PasswordRequest $r  ): RedirectResponse
    {
        /** @var UserEntity $user */
        $user = Auth::getUser();

        $user->setPassword( Hash::make( $r->input('new_password') ) );
        $user->setLastUpdated( new DateTime() );
        $user->setLastUpdatedBy( $user->getId() );

        D2EM::flush();

        AlertContainer::push( 'Password updated successfully', Alert::SUCCESS );

        // Logout all the active session except the current one
        D2EM::getRepository( UserRememberTokenEntity::class )->deleteByUser( $user->getId(), false );

        return Redirect::to( route( "profile@edit"  ) );
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
        /** @var UserEntity $user */
        $user = Auth::getUser();

        $user->setName(             $r->input( "name") );
        $user->setUsername(         $r->input( "username") );
        $user->setEmail(            $r->input( "email") );
        $user->setLastUpdated(      new DateTime() );
        $user->setLastUpdatedBy(    $user->getId() );

        D2EM::flush();

        AlertContainer::push( 'Your profile has been updated successfully', Alert::SUCCESS );

        return Redirect::to( route( "profile@edit"  ) );
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
        Auth::getUser()->setPreference( 'customer-notes.notify', $r->input( 'notify' ) );
        D2EM::flush();

        AlertContainer::push( 'Notification preference has been updated.', Alert::SUCCESS );

        return Redirect::to( route( "profile@edit"  ) );
    }

    /**
     * Update the mailing list preferences of the user
     *
     * @param Request $r instance of the current HTTP request
     * @return RedirectResponse
     * @throws
     */
    public function updateMailingLists( Request $r ) : RedirectResponse
    {
        /** @var UserEntity $user */
        $user = Auth::getUser();

        if( config( 'mailinglists.enabled', false ) ) {

            foreach( config( 'mailinglists.lists' ) as $name => $ml ) {
                $user->setPreference( "mailinglist.{$name}.subscribed", $r->input( "ml_" . $name ) ?? 0 );
            }
            D2EM::flush();
        }

        AlertContainer::push( 'Your mailing list subscriptions have been updated and will take effect within 12 hours.', Alert::SUCCESS );

        return Redirect::to( route( "profile@edit"  ) );
    }


}

