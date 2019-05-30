<?php

namespace IXP\Http\Controllers\User;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, Cache, D2EM, Former, Hash, Log, Redirect, Route, Validator;

use IXP\Events\User\Welcome as WelcomeEvent;

use Entities\{
    Customer as CustomerEntity,
    CustomerToUser as CustomerToUserEntity,
    User as UserEntity
};

use Illuminate\Http\{
    Request,
    RedirectResponse
};

use IXP\Http\Requests\User\{
    Store as StoreUser
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\View\View;

use IXP\Http\Controllers\Doctrine2Frontend;


/**
 * User Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Controller
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends Doctrine2Frontend {

    /**
     * The object being added / edited
     * @var UserEntity
     */
    protected $object = null;

    /**
     * The c2u object being added / edited
     * @var CustomerToUserEntity
     */
    protected $c2u = null;

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
            'nameSingular'      => 'user',

            'defaultAction'     => 'list',
            'defaultController' => 'UserController',

            'listOrderBy'       => 'username',
            'listOrderByDir'    => 'ASC',

            'viewFolderName'    => 'user',

            'documentation'     => 'https://docs.ixpmanager.org/usage/users/',

        ];

        switch( Auth::getUser()->getPrivs() ) {
            case UserEntity::AUTH_SUPERUSER:

                $this->feParams->listColumns = [

                    'name'          => 'Name',
                    'username'      => 'Username',
                    'email'         => 'Email',

                    'customer'  => [
                        'title'      => 'Customer',
                        'type'       => self::$FE_COL_TYPES[ 'HAS_ONE' ],
                        'controller' => 'customer',
                        'action'     => 'overview',
                        'idField'    => 'custid',
                    ],

                    'privileges'    => [
                        'title'     => 'Privileges',
                        'type'      => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'    => UserEntity::$PRIVILEGES_TEXT_SHORT,
                    ],

                    'disabled'       => [
                        'title'         => 'Enabled',
                        'type'          => self::$FE_COL_TYPES[ 'INVERSE_YES_NO' ],
                    ],

                    'created'       => [
                        'title'     => 'Created',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ],
                    ],

                    'lastupdated'   => [
                        'title'     => 'Updated',
                        'type'      => self::$FE_COL_TYPES[ 'DATETIME' ],
                    ],
                ];

                break;

            case UserEntity::AUTH_CUSTADMIN || UserEntity::AUTH_CUSTUSER:

                $this->feParams->pagetitle = 'Your Users';

                $this->feParams->listColumns = [
                    'name'          => 'Name',
                    'username'      => 'Username',
                    'email'         => 'Email',

                    'privileges'    => [
                        'title'     => 'Privileges',
                        'type'      => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'    => UserEntity::$PRIVILEGES_TEXT,
                    ],

                    'disabled'       => [
                        'title'         => 'Enabled',
                        'type'          => self::$FE_COL_TYPES[ 'INVERSE_YES_NO' ],
                    ],

                    'created'       => [
                        'title'         => 'Created',
                        'type'          => self::$FE_COL_TYPES[ 'DATETIME' ],
                    ],
                ];
                break;


            default:
                $this->unauthorized();

        }

        $this->feParams->viewColumns = $this->feParams->listColumns;

    }


    protected static function additionalRoutes( string $route_prefix ) {
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::get(     'add-wizard',                       'User\UserController@addForm'                )->name( $route_prefix . '@add-wizard'          );
            Route::get(     'add/info/{id?}',                   'User\UserController@edit'                   )->name( $route_prefix . '@add-info'            );
            Route::post(     'add/check-email',                 'User\UserController@addCheckEmail'          )->name( $route_prefix . '@add-check-email'     );
            Route::post(     'welcome-email',                   'User\UserController@sendWelcomeEmail'       )->name( $route_prefix . '@welcome-email'       );
            Route::post(     'custom-store',                    'User\UserController@customStore'            )->name( $route_prefix . '@custom-store'        );
        });
    }

    protected function preList() {
        $this->data[ 'params' ][ 'nbC2u' ]         = D2EM::getRepository( UserEntity::class )->getNbC2UByUser();
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

        // Return the list of User depending on the privilege of the Authentificated User
        if( Auth::getUser()->isSuperUser() ){
            // Getting All the Users
            return D2EM::getRepository( UserEntity::class )->getAllForFeListSuperUser( $this->feParams, Auth::getUser(), $id  );
        } else {
            // Getting all the User for A Customer
            return D2EM::getRepository( UserEntity::class )->getAllForFeListCustAdmin( $this->feParams, Auth::getUser(), $id );
        }

    }

    /**
     * Set as a session the redirect link
     *
     * @return Void
     *
     * @throws
     */
    private function redirectLink(){
        if( !request()->old() ) {
            request()->session()->remove( "user_post_store_redirect" );
            session()->put( 'user_post_store_redirect', request()->headers->get( 'referer', "" ) );
        }
    }

    /**
     * Set as a session the cancel link for cancel buttons
     *
     * @return Void
     *
     * @throws
     */
    private function cancelLink(){
        if( !request()->old() ) {
            request()->session()->remove( "user_cancel_redirect" );
            session()->put( 'user_cancel_redirect', request()->headers->get( 'referer', "" ) );
        }
    }


    /**
     * Return the list of privileges for the dropdown depending on the loggued user priv and if the customer is internal or not
     *
     * @return array List of privileges
     *
     * @throws
     */
    private function getAllowedPrivs(){

        $privs = UserEntity::$PRIVILEGES_TEXT_NONSUPERUSER;

        // If we add a user via the customer overview users list
        if( request()->is( 'user/add*' ) && request()->input( "cust" ) ){

            /** @var $c CustomerEntity */
            if( ( $c = D2EM::getRepository( CustomerEntity::class )->find( request()->input( "cust" ) ) ) ){
                // Internal customer and SuperUser
                if( $c->isTypeInternal() && Auth::getUser()->isSuperUser() ){
                    $privs = UserEntity::$PRIVILEGES_TEXT;
                }
            }
            // If we add a user and we are a SuperUser
        } elseif( request()->is( 'user/add*' ) && Auth::getUser()->isSuperUser() ){
            $privs = UserEntity::$PRIVILEGES_TEXT;
        }

        return $privs;
    }

    /**
     * Get the Customer2User list for a user or the customer/user link
     *
     * @param   UserEntity $user
     *
     * @return array
     *
     * @throws
     */
    private function getC2Ulist( UserEntity $user )
    {
        // Getting the Customer2User list
        if( Auth::getUser()->isSuperUser() )
        {
            // Getting all the customer for the user
            if( !( $listC2u = D2EM::getRepository( CustomerToUserEntity::class)->findBy( [ "user" => $user->getId() ] ) ) )
            {
                $this->unauthorized();
            }
        } else {
            // Getting only the current logged customer for the user
            if( !( $listC2u = D2EM::getRepository( CustomerToUserEntity::class)->findBy( [ "customer" => Auth::getUser()->getCustomer()->getId() , "user" => $user->getId() ] ) ) )
            {
                $this->unauthorized();
            }
        }

        return $listC2u;
    }


    /**
     * Display the first step form to Add a User object via email address
     *
     * @return View
     */
    public function addForm(): View {
        $this->redirectLink();

        $this->addEditSetup();

        $cust = D2EM::getRepository( CustomerEntity::class )->find( request()->input("cust" , "" ) );

        $this->data[ 'params' ][ 'custid' ]         = $cust ? $cust->getId() : '';
        $this->data[ 'params' ][ 'canbelBtnLink' ]  = $cust ? route( "customer@overview", [ "id" => $cust->getId() ] ) : route( "user@list" ) ;

        return $this->display( 'add-form' );
    }


    /**
     * Function to check if the Email address is already used by a User
     *
     *      if yes we get the user information and display the information of the user
     *      if no we display the add/edit form
     *
     * @param Request $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function addCheckEmail( Request $request ){
        $validator = Validator::make( $request->all(), [
            'email'                 => 'required|email|max:255',
        ]);

        if( $validator->fails() ) {
            return Redirect::back()->withErrors( $validator )->withInput();
        }

        /** @var UserEntity $user */
        if( $user = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'email' => $request->input( 'email' ) ] ) ) {
            if( !Auth::getUser()->isSuperUser() ){
                if( D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "customer" => Auth::getUser()->getCustomer() , "user" => $user ] ) ){
                    AlertContainer::push( "A user already exists with that email address for your company." , Alert::DANGER );
                    return Redirect::back()->withErrors($validator)->withInput();
                }
            }
        }


        // building the redirect url
        $url = $user ? route( "user@add-info", [ 'user' => $user->getEmail() ] ) : route("user@add" ). '?e-mail=' . $request->input( 'email' );

        if( $request->input( "custid" ) && ( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( "custid" ) ) ) ){
            $separator = $user ? "?" : "&";
            $url = $url . $separator . "cust=" . $cust->getId();
        }


        return redirect( $url );
    }


    /**
     * Display the form to edit an object
     *
     * @param   int $id ID of the row to edit
     *
     * @return array
     *
     * @throws
     */
    protected function addEditPrepareForm( $id = null ): array {
        $old = request()->old();
        $existingUser = $disabledInputs = $addEditTpl = false;
        $listUsers = [];

        if( !request()->session()->exists( 'user_post_store_redirect' ) ){
            $this->redirectLink();
        }

        $c = D2EM::getRepository( CustomerEntity::class)->find( request()->input( "cust", false ) );

        $this->cancelLink();

        // Id we edit the user
        if( request()->is( 'user/edit/*' ) ){

            if( $id !== null ) {

                if( !( $this->object = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
                    abort(404, 'User not found');
                }

                $dataCust = [
                    'name'             => array_key_exists( 'name',                $old ) ? $old['name']                 : $this->object->getName(),
                    'username'         => array_key_exists( 'username',            $old ) ? $old['username']             : $this->object->getUsername(),
                    'email'            => array_key_exists( 'email',               $old ) ? $old['email']                : $this->object->getEmail(),
                    'authorisedMobile' => array_key_exists( 'authorisedMobile',    $old ) ? $old['authorisedMobile']     : $this->object->getAuthorisedMobile(),
                    'enabled'          => array_key_exists( 'enabled',             $old ) ? ( $old['enabled'] ? 1 : 0 )  : ( $this->object->getDisabled() ? 0 : 1 ),
                ];

                $datac2u = [];

                /** @var CustomerToUserEntity $c2u */
                foreach( $this->getC2Ulist( $this->object ) as $c2u ){

                    if( Auth::getUser()->isSuperUser() ){
                        $datac2u[ 'privs_'  . $c2u->getId() ] = array_key_exists( 'privs_' . $c2u->getId(),  $old    ) ? $old[ 'privs_' . $c2u->getId()  ]   : $c2u->getPrivs();

                    } else {
                        $datac2u[ 'privs'  ] = array_key_exists( 'privs',  $old    ) ? $old[ 'privs'   ]  : $c2u->getPrivs();

                    }
                }

                Former::populate( array_merge( $dataCust, $datac2u ) );

                if( !Auth::getUser()->isSuperUser() ){
                    $disabledInputs = true;
                }
            }

        } else {

            // Adding user / associate existing user with a customer
            $this->feParams->customBreadcrumb = "Add";

            // If we found user with the provided email address
            if( $id !== null ) {

                // search user via email address
                if( !( $listUsers = D2EM::getRepository( UserEntity::class )->findBy( [ 'email' => $id ] ) ) ) {
                    abort(404, 'User not found');
                }

                $existingUser = true;
                $addEditTpl = "customer2-user/add";

            }

            Former::populate([
                'name'                  => array_key_exists( 'name',                $old ) ? $old['name']               : request()->input( "name" ),
                'username'              => array_key_exists( 'username',            $old ) ? $old['username']           : request()->input( "username" ),
                'email'                 => array_key_exists( 'email',               $old ) ? $old['email']              : request()->input( "e-mail" ),
                'authorisedMobile'      => array_key_exists( 'authorisedMobile',    $old ) ? $old['authorisedMobile']   : request()->input( "authorisedMobile" ),
                'enabled'               => array_key_exists( 'enabled',             $old ) ? $old['enabled']            : request()->input( "enabled" ),
                'custid'                => array_key_exists( 'custid',              $old ) ? $old['custid']             : request()->input( "cust" ),
            ]);

        }

        return [
            'existingUser'          => $existingUser,
            "disabledInputs"        => $disabledInputs,
            'listUsers'             => $listUsers,
            'object'                => $this->object,
            'canbelBtnLink'         => session()->get( 'user_cancel_redirect' ),
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'privs'                 => $this->getAllowedPrivs(),
            'c'                     => $c,
            'addEditTpl'            => $addEditTpl,
        ];
    }


    /**
     * Function to do the actual validation and storing of the submitted object.
     *
     * @param StoreUser $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */

    public function customStore( StoreUser $request )
    {

        $isAdding = $request->input( 'id' ) ? false : true;

        if( $isAdding )
        {
            $this->object = new UserEntity;
            D2EM::persist( $this->object );

            $this->object->setCreated(  new \DateTime  );
            $this->object->setCreator(  Auth::getUser()->getUsername() );

            $this->object->setPassword( Hash::make( str_random(16) ) );
            $this->store_alert_success_message = "User added successfully. A welcome email is being sent to {$this->object->getEmail()} with "
                . "instructions on how to set their password.";

        } else {

            if( !( $this->object = D2EM::getRepository( UserEntity::class )->find( $request->input( 'id' ) ) ) )
            {
                abort(404, 'User not found');
            }

        }

        // Superuser OR Adding user OR Logged User edit his own user
        if( Auth::getUser()->isSuperUser() || !$this->object->getId() || $this->object->getId() == Auth::getUser()->getId() ) {
            $this->object->setName( $request->input( 'name' ) );
            $this->object->setAuthorisedMobile( $request->input( 'authorisedMobile' ) );
        }

        // Superuser OR Adding user
        if( Auth::getUser()->isSuperUser() || !$this->object->getId() ){
            $this->object->setUsername(          strtolower( $request->input( 'username' ) ) );
            $this->object->setEmail(             $request->input( 'email'                ) );
            $this->object->setDisabled( $request->input( 'enabled', 0 ) == "1" ? false : true );
        }

        $this->object->setLastupdated(       new \DateTime  );
        $this->object->setLastupdatedby(     Auth::getUser()->getId() );

        // Adding new user => set customer and priv to the User Object
        if( !$this->object->getId() ){
            $this->object->setPrivs( $request->input( 'privs' ) );
            $this->object->setCustomer( Auth::user()->isSuperUser() ? D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) : Auth::getUser()->getCustomer() );
        }

        D2EM::flush();

        if( $isAdding )
        {
            // Creating the CustomerToUser object
            $this->c2u = new CustomerToUserEntity();
            D2EM::persist( $this->c2u );

            $this->c2u->setCustomer(        $this->object->getCustomer()  );
            $this->c2u->setUser(            $this->object  );
            $this->c2u->setCreatedAt(       new \DateTime  );
            $this->c2u->setPrivs(           $request->input( 'privs' ) );
            $this->c2u->setExtraAttributes( [ "created_by" => [ "type" => "user" , "user_id" => $this->object->getId() ] ] );

            D2EM::flush();

            if( $request->input( 'privs' ) == UserEntity::AUTH_SUPERUSER ){
                AlertContainer::push( 'Please note that you have given this user full administrative access.', Alert::WARNING );
            }

            // change me
            event( new WelcomeEvent( $this->object->getCustomer(), $this->object, false, false ) );
        }

        // if efiting and not super user
        if( !$isAdding && !Auth::getUser()->isSuperUser() ){

            /** @var $c2u CustomerToUserEntity */
            if( !( $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $this->object , 'customer' => Auth::getUser()->getCustomer() ] ) ) )
            {
                abort(404, 'UserToCustomer not found');
            }

            $c2u->setPrivs( $request->input( "privs" ) );
            D2EM::flush();

        }

        $action = $request->input( 'id', '' )  ? "edited" : "added";

        Log::notice( ( Auth::check() ? Auth::user()->getUsername() : 'A public user' ) . ' ' . $action
            . ' ' . $this->feParams->nameSingular . ' with ID ' . $this->object->getId() );


        AlertContainer::push( $this->store_alert_success_message ?? $this->feParams->titleSingular . " " . $action, Alert::SUCCESS );

        return redirect()->to( $this->postStoreRedirect() ?? route( self::route_prefix() . '@' . 'list' ) );
    }

    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postStoreRedirect(){
        if( !Auth::getUser()->isSuperUser() ) {
            return route( 'user@list' );
        } else {

            $redirect = session()->get( "user_post_store_redirect" );
            session()->remove( "user_post_store_redirect" );

            // retrieve the customer ID
            if( strpos( $redirect, "customer/overview" ) ) {
                return route( 'customer@overview' , [ 'id' => $this->object->getCustomer()->getId() , 'tab' => 'users' ] );
            }
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
     * @throws
     */
    protected function preDelete(): bool
    {
        $c = null;
        $deleteUser = false;

        if( !( $this->object = D2EM::getRepository( $this->feParams->entity )->find( request()->input( 'id' ) ) ) ) {
            return abort( '404', 'User not found' );
        }

        if( !Auth::getUser()->isSuperUser() ) {
            if( !D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ "user" => $this->object , "customer" => Auth::getUser()->getCustomer()->getId() ] ) ) {
                Log::notice( Auth::getUser()->getUsername() . " tried to delete other customer user " . $this->object->getUsername() );
                abort( 401, 'You are not authorised to delete this user. The administrators have been notified.' );
            }
        }


        if( !Auth::getUser()->isSuperUser() && !request()->input( 'custid' ) ){
            redirect::to( "/" );
        }

        if( request()->input( 'custid' ) ){
            if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( request()->input( 'custid' ) ) ) ) {
                return abort( '404', 'Customer not found' );
            }
        }



        if( Auth::getUser()->isSuperUser() && !$c ){
            $deleteUser = true;
        } elseif( count( $this->object->getCustomers() ) == 1  ){
            $deleteUser = true;
        }


        // delete the user and everything linked to this user
        if( $deleteUser ){

            // delete all the user's preferences
            foreach( $this->object->getPreferences() as $pref ) {
                $this->object->removePreference( $pref );
                D2EM::remove( $pref );
            }


            // delete all the user's API keys
            foreach( $this->object->getApiKeys() as $ak ) {
                $this->object->removeApiKey( $ak );
                D2EM::remove( $ak );
            }


            // delete all the C2U for the user
            /** @var CustomerToUserEntity $c2u */
            foreach( $this->object->getCustomers2User() as $c2u ) {

                // delete all the user's login records
                foreach( $c2u->getUserLoginHistory() as $loginHistory ){
                    D2EM::remove( $loginHistory );
                }

                $this->object->removeCustomer( $c2u );
                D2EM::remove( $c2u );
            }

            // Set the customer ID in session to redirect the user to the customer overview after deleting
            if( Auth::getUser()->isSuperUser() ) {
                if( strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
                    session()->put( "ixp_user_delete_redirect", route( "customer@overview", [ "id" => $this->object->getCustomer()->getId() , "tab" => "users"] ) );
                }

            }

            Cache::forget( 'oss_d2u_user_' . $this->object->getId() );

            Log::notice( Auth::getUser()->getUsername()." deleted user" . $this->object->getUsername() );

            // If the user delete itself and is loggued as the same customer logout
            if( Auth::getUser()->getId() == $this->object->getId() ){
                Auth::logout();
            }

            return true;
        }

        return false;
    }



    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postDeleteRedirect() {

        // retrieve the customer ID
        if( $urlRedirect = session()->get( "ixp_user_delete_redirect" ) ) {
            session()->remove( "ixp_user_delete_redirect" );
            return $urlRedirect;
        }

        // If user not logged in redirect to the login form ( this happen when the user delete itself)
        if( !Auth::check() ){
            return route( "login@showForm" );
        }

        return null;
    }

    /**
     * Send or resend the welcome email to a new user
     *
     * @param Request $request
     *
     * @return bool|RedirectResponse
     */
    public function sendWelcomeEmail( Request $request ) {

        /** @var UserEntity $user */
        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $request->input( "id" ) ) ) ) {
            abort(404, 'User not found');
        }

        $resend = $request->input( "resend" ) ? true : false;

        event( new WelcomeEvent( $user->getCustomer(), $user, $resend , false ) );

        if( $resend ){
            AlertContainer::push( sprintf( 'The welcome email has been %s.', $resend ? 'resent' : 'sent' ), Alert::SUCCESS );
            return redirect::to( route( $this::$route_prefix . "@list" ) );
        }

        return true;
    }

}
