<?php

namespace IXP\Http\Controllers;

/*
 * Copyright (C) 2009-2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, Cache, D2EM, Former, Redirect, Route, Validator;

use IXP\Events\User\Welcome as WelcomeEvent;

use Entities\{
    Customer            as CustomerEntity,
    User                as UserEntity
};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};
use Log;


/**
 * User Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var UserEntity
     */
    protected $object = null;

    protected static $route_prefix = "user";

    /**
     * The minimum privileges required to access this controller.
     *
     * If you set this to less than the superuser, you need to manage privileges and access
     * within your own implementation yourself.
     *
     * @var int
     */
    public static $minimum_privilege = UserEntity::AUTH_CUSTADMIN;

    /**
     * This function sets up the frontend controller
     */
    public function feInit(){

        $this->feParams         = ( object )[

            'entity'            => UserEntity::class,
            'pagetitle'         => 'Users',

            'titleSingular'     => 'User',
            'nameSingular'      => 'a user',

            'defaultAction'     => 'list',
            'defaultController' => 'UserController',

            'listOrderBy'       => 'username',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'user',

        ];



        switch( Auth::getUser()->getPrivs() ) {
            case UserEntity::AUTH_SUPERUSER:

                $this->feParams->listColumns = [

                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid'
                    ],

                    'username'      => 'Username',
                    'email'         => 'Email',

                    'privileges'    => [
                        'title'     => 'Privileges',
                        'type'      => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'    => UserEntity::$PRIVILEGES_TEXT
                    ],

                    'disabled'       => [
                        'title'         => 'Enabled',
                        'type'          => self::$FE_COL_TYPES[ 'YES_NO' ],
                    ],

                    'created'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ],

                    'lastupdated'   => [
                        'title'     => 'Updated',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];

                break;

            case UserEntity::AUTH_CUSTADMIN:

                $this->feParams->pagetitle = 'User Admin for ' . Auth::getUser()->getCustomer()->getName();

                $this->_feParams->listColumns = [
                    'username'      => 'Username',
                    'email'         => 'Email',

                    'disabled'       => [
                        'title'         => 'Disabled',
                        'type'          => self::$FE_COL_TYPES[ 'YES_NO' ],
                    ],

                    'created'       => [
                        'title'         => 'Created',
                        'type'          => self::$FE_COL_TYPES[ 'DATETIME' ]
                    ]
                ];
                break;

            default:
                $this->unauthorized();

        }

        $this->feParams->viewColumns = $this->feParams->listColumns;


    }


    protected static function additionalRoutes( string $route_prefix ){
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::get(     'welcome-email/{id}/{resend}',                          'UserController@sendWelcomeEmail'    )->name( $route_prefix . '@welcome-email' );
        });
    }


    /**
     * Provide array of rows for the list action and view action
     *
     * @param int $id The `id` of the row to load for `view` action`. `null` if `listAction`
     * @return array
     *
     * @throws
     */
    protected function listGetData( $id = null ) {

        return D2EM::getRepository( UserEntity::class )->getAllForFeList( $this->feParams, $id );
    }



    /**
     * Display the form to add/edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     *
     * @throws
     */
    protected function addEditPrepareForm( $id = null ): array {

        $old = request()->old();
        $from = "user@list";

        // check if we come from the customer overview or the customer list
        if( isset( $_SERVER[ "HTTP_REFERER" ] ) && strpos( $_SERVER[ "HTTP_REFERER" ] , $this::$route_prefix . "/list" ) === false ){
            $from = "customer@overview";
        }

        if( $id !== null ) {

            if( !( $this->object = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
                abort(404);
            }

            Former::populate( [
                'username'                  => array_key_exists( 'username',            $old ) ? $old['username']           : $this->object->getUsername(),
                'custid'                    => array_key_exists( 'custid',              $old ) ? $old['custid']             : $this->object->getCustomer()->getId(),
                'email'                     => array_key_exists( 'email',               $old ) ? $old['email']              : $this->object->getEmail(),
                'authorisedMobile'          => array_key_exists( 'authorisedMobile',    $old ) ? $old['authorisedMobile']   : $this->object->getAuthorisedMobile(),
                'diabled'                   => array_key_exists( 'diabled',             $old ) ? $old['diabled']            : ( $this->object->getDisabled()      ? 1 : 0 ),
                'privs'                     => array_key_exists( 'privs',               $old ) ? $old['privs']              : $this->object->getPrivs(),
            ] );

        }

        return [
            'object'                => $this->object,
            'from'                  => $from,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
        ];
    }


    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param Request $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function doStore( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'username'              => 'required|string|min:4|max:255|unique:Entities\User,username' . ( $request->input( 'id' ) ? ','. $request->input( 'id' ) : '' ),
            'custid'                => 'required|integer|exists:Entities\Customer,id',
            'usersecret'            => ( $request->input( 'id' ) ? 'nullable' : 'required' ) . '|string|min:8|max:255',
            'email'                 => 'required|email|max:255',
            'authorisedMobile'      => 'nullable|string|max:50',
            'privs'                 => 'required|integer|in:' . implode( ',', array_keys( UserEntity::$PRIVILEGES_ALL ) ),
        ]);



        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        if( $request->input( 'id', false ) ) {
            if( !( $this->object = D2EM::getRepository( UserEntity::class )->find( $request->input( 'id' ) ) ) ) {
                abort(404);
            }
        } else {
            $this->object = new UserEntity;
            D2EM::persist( $this->object );

            $this->object->setCreated(  new \DateTime  );
            $this->object->setCreator(  Auth::getUser()->getUsername() );
        }

        if( $request->input( 'usersecret' ) ){
            $this->object->setPassword( password_hash( $request->input( 'usersecret' ), PASSWORD_BCRYPT, [ 'cost' => 10 ] ) );
        }

        $this->object->setUsername(          $request->input( 'username'            ) );
        $this->object->setEmail(             $request->input( 'email'               ) );
        $this->object->setPrivs(             $request->input( 'privs'               ) );
        $this->object->setAuthorisedMobile(  $request->input( 'authorisedMobile'    ) );
        $this->object->setCustomer(          D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) );
        $this->object->setLastupdated(       new \DateTime  );
        $this->object->setLastupdatedby(     Auth::getUser()->getId() );

        D2EM::flush($this->object);

        if( !$request->input( 'id') ) {
            $this->sendWelcomeEmail( $this->object->getId(), false );
        }

        request()->session()->put( "user_post_store_redirect", $request->input( 'from' ) );

        return true;
    }

    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postStoreRedirect()
    {

        $redirect = request()->session()->get( "user_post_store_redirect" );
        request()->session()->remove( "ixp_user_delete_custid" );

        // retrieve the customer ID
        if( $redirect != "user@list" ) {
            return route( "customer@overview" , [ "id" => $this->object->getCustomer()->getId() , "tab" => "users" ] );
        }

        return null;

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
                Log::notice( Auth::getUser()->getUsername() . "tried to delete other customer user " . $this->object->getUser()->getUsername()  );
                AlertContainer::push( 'You are not authorised to delete this user. The administrators have been notified.', Alert::DANGER );
                return Redirect::to( '' );
            }
        }


        if( !( $this->object = D2EM::getRepository( $this->feParams->entity )->find( request()->input( 'id' ) ) ) ) {
            return abort( '404', "Unknown Contact" );
        }

        $this->removeUserData( $this->object );

        if( request()->input( "redirect-to" ) ){
            request()->session()->put( "ixp_user_delete_custid", $this->object->getCustomer()->getId() );
        }


        return true;
    }


    /**
     * Delete all the informations associated to the User (User preference, User logins, Api keys)
     *
     * @param UserEntity $user The user entity
     *
     * @throws
     */
    private function removeUserData( UserEntity $user ){
        /** @var UserEntity $user */
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

        D2EM::remove( $user );

        Cache::forget( 'oss_d2u_user_' . $user->getId() );

        Log::notice( Auth::getUser()->getUsername()." deleted user" . $userName );
    }


    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postDeleteRedirect() {
        // retrieve the customer ID
        if( $custid = $this->request->session()->get( "ixp_user_delete_custid" ) ) {

            $this->request->session()->remove( "ixp_user_delete_custid" );

            return route( "customer@overview" , [ "id" => $custid, "tab" => "users" ] );
        }

        return null;

    }

    /**
     * Send or resend the welcome email to a new user
     *
     * @param int   $id         ID of the user
     * @param bool  $resend     Do we need to resend the welcome email ?
     *
     * @return bool|RedirectResponse
     */
    protected function sendWelcomeEmail( int $id, bool $resend = false ){

        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
            abort(404, 'Unknown user');
        }

        event( new WelcomeEvent( $user, $resend ) );

        if( $resend ){
            AlertContainer::push( 'The welcome email has been sent with success.', Alert::SUCCESS );
            return redirect::to( route( $this::$route_prefix . "@list" ) );
        }

        return true;
    }

}
