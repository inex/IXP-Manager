<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, Cache, D2EM, Log, Redirect, Route;

use Entities\{
    Contact             as ContactEntity,
    User                as UserEntity
};

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;




/**
 * Contact Controller
 *
 * TEMPORARY CONTROLLER BEFORE THAT THE CONTACT MANAGEMENT IS REBUILT UNDER LARAVEL
 *
 * NAME CONTACTS TO AVOID CONFLICT WITH THE ACTUAL CONTACT MANAGEMENT UNDER ZEND
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   VlanInterface
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class ContactsController extends Doctrine2Frontend {
    /**
     * The object being added / edited
     * @var ContactEntity
     */
    protected $object = null;

    /**
     * The http Request
     * @var Request
     */
    protected $request = null;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){
        $this->feParams         = (object)[
            'entity'            => ContactEntity::class,

            'pagetitle'         => 'Contact',

            'titleSingular'     => 'Contact',

            'readonly'          => 'false',
        ];
    }


    protected static function additionalRoutes( string $route_prefix ){
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::post(     'delete-user',                          'ContactsController@deleteUser'    )->name( $route_prefix . '@delete-user' );
        });
    }

    /**
     * The default routes for a Doctrine2Frontend class
     */
    public static function routes() {


        // add leading slash to class name for absolute resolution:
        $class = '\\' . get_called_class();
        $route_prefix = self::route_prefix();

        Route::group( [ 'prefix' => $route_prefix ], function() use ( $class, $route_prefix ) {
            Route::post( 'delete',      $class . '@delete'  )->name( $route_prefix . '@delete'  );
        });

        $class::additionalRoutes( $route_prefix );
    }

    /**
     * Provide array of rows for the list and view
     *
     * @param int $id The `id` of the row to load for `view`. `null` if `list`
     *
     * @return array
     */
    protected function listGetData( $id = null ) {
        return [];
    }


    /**
     * Delete the user only
     *
     * @param   Request   $r HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws
     */
    public function deleteUser( Request $r )  {

        if( !Auth::getUser()->isSuperUser() ) {
            if( $this->object->getCustomer()->getId() != Auth::getUser()->getCustomer()->getId() ) {
                Log::notice( Auth::getUser()->getUsername() . "tried to delete other customer user " . $this->object->getUser()->getUsername()  );
                AlertContainer::push( 'You are not authorised to delete this user. The administrators have been notified.', Alert::DANGER );
                return Redirect::to( '' );
            }
        }

        if( !( $this->object = D2EM::getRepository( $this->feParams->entity )->find( $r->input( 'id' ) ) ) ) {
            return abort( '404', "Unknown Contact" );
        }

        $this->removeUserData( $this->object );

        D2EM::flush();

        AlertContainer::push( 'User login account successfully removed.', Alert::SUCCESS );

        return Redirect::to( route( "customer@overview", [ "id" => $this->object->getCustomer()->getId(), "tab" => "users" ] ) );
    }

    /**
     * Function which can be over-ridden to perform any pre-deletion tasks
     *
     * You can stop the deletion by returning false but you should also add a
     * message to explain why (to the AlertContainer).
     *
     * The object to be deleted is available via `$this->>object`
     *
     * @return bool Return false to stop / cancel the deletion
     */
    protected function preDelete( ): bool {

        if( !Auth::getUser()->isSuperUser() ) {
            if( $this->object->getCustomer()->getId() != Auth::getUser()->getCustomer()->getId() ) {
                Log::notice( Auth::getUser()->getUsername() . "tried to delete other customer user " . $this->object->getUser()->getUsername() );
                AlertContainer::push( 'You are not authorised to delete this user. The administrators have been notified.', Alert::DANGER );
                return false;
            }
        } else {
            // keep the customer ID for redirection on success
            $this->request->session()->put( "ixp_contact_delete_custid", $this->object->getCustomer()->getId() );
        }


        if( $this->object->getUser() )
            $this->removeUserData( $this->object );

        return true;

    }

    /**
     * Allow D2F implementations to override where the post-delete redirect goes.
     *
     * To implement this, have it return a valid route url (e.g. `return route( "route-name" );`
     *
     * @return null|string
     */
    protected function postDeleteRedirect(){

        // retrieve the customer ID
        if( $custid = $this->request->session()->get( "ixp_contact_delete_custid" ) ) {

            $this->request->session()->remove( "ixp_contact_delete_custid" );

            return route( "customer@overview" , [ "id" => $custid, "tab" => "contacts" ] );
        }

        return null;
    }



    /**
     * Delete all the informations associated to the User (User preference, User logins, Api keys)
     *
     * @param ContactEntity $contact The contact entity
     *
     * @throws
     */
    private function removeUserData( ContactEntity $contact ){
        /** @var UserEntity $user */
        $user = $contact->getUser();
        $userName = $user->getUsername();

        // delete all the user's preferences
        foreach( $user->getPreferences() as $pref ) {
            $user->removePreference( $pref );
            D2EM::remove( $pref );
        }

        // delete all the user's login records
        foreach( $user->getLastLogins() as $ll ) {
            $user->removeLastLogin( $ll );
            D2EM::remove( $ll );
        }

        // delete all the user's API keys
        foreach( $user->getApiKeys() as $ak ) {
            $user->removeApiKey( $ak );
            D2EM::remove( $ak );
        }

        // clear the user from the contact and remove the user then
        $contact->unsetUser();
        D2EM::remove( $user );

        Cache::forget( 'oss_d2u_user_' . $user->getId() );

        Log::notice( Auth::getUser()->getUsername()." deleted user" . $userName );
    }


}