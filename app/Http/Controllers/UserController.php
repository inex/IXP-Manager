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
    Customer            as CustomerEntity,
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity
};
use Illuminate\Http\{
    Request,
    JsonResponse,
    RedirectResponse
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

use Illuminate\View\View;


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
                        'idField'    => 'custid'
                    ],

                    'privileges'    => [
                        'title'     => 'Privileges',
                        'type'      => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'    => UserEntity::$PRIVILEGES_TEXT
                    ],

                    'disabled'       => [
                        'title'         => 'Enabled',
                        'type'          => self::$FE_COL_TYPES[ 'INVERSE_YES_NO' ],
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

            case UserEntity::AUTH_CUSTADMIN || UserEntity::AUTH_CUSTUSER:

                $this->feParams->pagetitle = 'Your Users';

                $this->feParams->listColumns = [
                    'name'          => 'Name',
                    'username'      => 'Username',
                    'email'         => 'Email',

                    'privileges'    => [
                        'title'     => 'Privileges',
                        'type'      => self::$FE_COL_TYPES[ 'XLATE' ],
                        'xlator'    => UserEntity::$PRIVILEGES_TEXT
                    ],

                    'disabled'       => [
                        'title'         => 'Enabled',
                        'type'          => self::$FE_COL_TYPES[ 'INVERSE_YES_NO' ],
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


    protected static function additionalRoutes( string $route_prefix ) {
        // FIXME @yannrobin: should be POST
        Route::group( [ 'prefix' => $route_prefix ], function() use ( $route_prefix ) {
            Route::get(     'welcome-email/{id}/{resend?}',     'UserController@sendWelcomeEmail'       )->name( $route_prefix . '@welcome-email'       );
            Route::get(     'add-wizard',                       'UserController@addForm'                )->name( $route_prefix . '@add-wizard'          );
            Route::get(     'add/info/{id?}',                   'UserController@edit'                   )->name( $route_prefix . '@add-info'            );
            Route::get(     '{id}/list-customers',              'UserController@listCustomers'          )->name( $route_prefix . '@list-customers'      );
            Route::post(     'add/check-email',                 'UserController@addCheckEmail'          )->name( $route_prefix . '@add-check-email'     );
            Route::post(     '{id}/delete-customer/{custid}',   'UserController@deleteCustomerToUser'   )->name( $route_prefix . '@delete-customer'     );
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
        return D2EM::getRepository( UserEntity::class )->getAllForFeList( $this->feParams, $id, Auth::getUser() );
    }

    /**
     * All to set the url under the cancel button on the edit/add form depending on the entry point (customer overview/ user list)
     *
     * @return Void
     */
    public function manageCancelbutton(){
        request()->session()->remove( "user_post_store_redirect" );

        // check if we come from the customer overview or the user list
        if( strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
            session()->put( 'user_post_store_redirect',     'customer@overview' );
            session()->put( 'user_post_store_redirect_cid', request()->input('cust', null ) );
        } else {
            session()->put( 'user_post_store_redirect', 'user@list' );
            session()->put( 'user_post_store_redirect_cid', null );
        }
    }

    /**
     * Display the form to Add an object
     *
     * @return View
     */
    public function addForm(): View {
        $this->manageCancelbutton();

        $this->addEditSetup();

        $this->data[ 'params' ][ 'custid' ]         = request()->input("cust" );

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
    public function addCheckEmail( Request $request )
    {
        $validator = Validator::make( $request->all(), [
            'email'                 => 'required|email|max:255',
        ]);


        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $userEmail = null;

        if( D2EM::getRepository( UserEntity::class )->findBy( [ 'email' => $request->input( 'email' ) ] ) ) {
            $userEmail = $request->input( 'email' );
        }

        $url = $userEmail ? route( "user@add-info", [ 'user' => $userEmail ] ) : route("user@add" ). '?email=' . $request->input( 'email' );


        if( $request->input( "custid" ) ){
            $separator = $userEmail ? "?" : "&";
            $url = $url . $separator . "custid=" . $request->input( "custid" );
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
        $existingUser = $disabledInputs = false;
        $listUsers = [];

        $this->manageCancelbutton();


        if( request()->is( 'user/edit/*' ) ){

            if( $id !== null ) {

                if( !( $this->object = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
                    abort(404, 'User not found');
                }


                if( !( $this->c2u = D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "customer" => Auth::getUser()->getCustomer()->getId() , "user" => $this->object->getId() ] ) ) ){
                    $this->unauthorized();
                }
                
                Former::populate([
                    'name'             => array_key_exists( 'name',                $old ) ? $old['name']                 : $this->object->getName(),
                    'username'         => array_key_exists( 'username',            $old ) ? $old['username']             : $this->object->getUsername(),
                    'custid'           => array_key_exists( 'custid',              $old ) ? $old['custid']               : $this->c2u->getCustomer()->getId(),
                    'email'            => array_key_exists( 'email',               $old ) ? $old['email']                : $this->object->getEmail(),
                    'authorisedMobile' => array_key_exists( 'authorisedMobile',    $old ) ? $old['authorisedMobile']     : $this->object->getAuthorisedMobile(),
                    'enabled'          => array_key_exists( 'enabled',             $old ) ? ( $old['enabled'] ? 1 : 0 )  : ( $this->object->getDisabled() ? 0 : 1 ),
                    'privs'            => array_key_exists( 'privs',               $old ) ? $old['privs']                : $this->c2u->getPrivs(),
                ]);

            } else {

                if( request()->input( "cust" ) && ( $cust = D2EM::getRepository( CustomerEntity::class )->find( request()->input( "cust" ) ) ) ){
                    $dataCust = [
                        'custid'                    => $cust->getId(),
                    ];

                }

                $dataEmail = [
                    'email'                    => request()->input( 'email' ),
                ];

                Former::populate( array_merge( $dataCust, $dataEmail ) );
            }

            if( !Auth::getUser()->isSuperUser() ){
                $disabledInputs = true;
            }

        } else {

            $this->feParams->customBreadcrumb = "Add";

            if( $id !== null ) {
                $listUsers = [];
                if( !( $listUsers = D2EM::getRepository( UserEntity::class )->findBy( [ 'email' => $id ] ) ) ) {
                    abort(404, 'User not found');
                }

                Former::populate([
                    'custid'           => array_key_exists( 'email',               $old ) ? $old['email']                : request()->input( "cust" ),
                ]);

                $existingUser = true;

            } else {

                Former::populate([
                    'email'            => array_key_exists( 'email',               $old ) ? $old['email']                : request()->input( "email" ),
                    'custid'           => array_key_exists( 'custid',              $old ) ? $old['custid']               : request()->input( "custid" ),
                ]);
            }

        }

        return [
            'existingUser'          => $existingUser,
            "disabledInputs"        => $disabledInputs,
            'listUsers'             => $listUsers,
            'object'                => $this->object,
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

    public function doStore( Request $request ){

        // If the User already exist
        if( $request->input( 'existingUser' ) ) {

            $validator = Validator::make( $request->all(), [
                'custid'            => 'required|integer|exists:Entities\Customer,id',
                'existingUserId'    => 'required|integer|exists:Entities\User,id',
                'privs'             => 'required|integer|in:' . implode( ',', array_keys( UserEntity::$PRIVILEGES_ALL ) ),
            ] );

            if( $request->input( 'existingUserId' ) == null ){
                AlertContainer::push( "You need to select one User from the list." , Alert::DANGER );
            }

            if( D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "customer" => $request->input( 'custid' ) , "user" => $request->input( 'existingUserId' ) ] ) ){
                AlertContainer::push( "The association User/Customer already exist." , Alert::DANGER );
                return Redirect::back()->withErrors($validator)->withInput();
            }

        }else{
            // If its a superuser or if we are adding a new user
            if( Auth::getUser()->isSuperUser() || !$request->input( 'id' ) ) {
                $validator = Validator::make( $request->all(), [
                    'name'             => 'required|string|max:255',
                    'username'         => 'required|string|min:3|max:255|regex:/^[a-z0-9\-_]{3,255}$/|unique:Entities\User,username' . ( $request->input( 'id' ) ? ',' . $request->input( 'id' ) : '' ),
                    'custid'           => 'required|integer|exists:Entities\Customer,id',
                    'email'            => 'required|email|max:255',
                    'authorisedMobile' => 'nullable|string|max:50',
                    'privs'            => 'required|integer|in:' . implode( ',', array_keys( UserEntity::$PRIVILEGES_ALL ) ),
                ] );
            }else{
                // If custadmin editing
                $validator = Validator::make( $request->all(), [
                    'privs'            => 'required|integer|in:' . implode( ',', array_keys( UserEntity::$PRIVILEGES_ALL ) ),
                ] );
            }
        }

        if( $validator->fails() ) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        // Existing user, we create the CustomerToUser association
        if( $request->input( 'existingUser' ) ) {

            $this->feParams->nameSingular = "Customer2User";

            /** @var UserEntity $existingUser */
            $existingUser = D2EM::getRepository( UserEntity::class )->find( $request->input( 'existingUserId' ) );

            $this->object = new CustomerToUserEntity;
            D2EM::persist( $this->object );

            $this->object->setCustomer(     D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) );
            $this->object->setUser(         $existingUser                   );
            $this->object->setPrivs(        $request->input( 'privs' ) );
            $this->object->setCreatedAt(    new \DateTime                   );

            D2EM::flush();

            event( new WelcomeEvent( $existingUser, false ) );

        } else{
            // Editing user
            if( $request->input( 'id', false ) ) {
                if( !( $this->object = D2EM::getRepository( UserEntity::class )->find( $request->input( 'id' ) ) ) ) {
                    abort(404, 'User not found');
                }

            } else {

                // Adding user
                $this->object = new UserEntity;
                D2EM::persist( $this->object );

                $this->object->setCreated(  new \DateTime  );
                $this->object->setCreator(  Auth::getUser()->getUsername() );

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

            if( !$this->object->getId() ) {
                // adding a new user -> set a random password
                $this->object->setPassword( Hash::make( str_random(16) ) );
                $this->store_alert_success_message = "User added successfully. A welcome email is being sent to {$this->object->getEmail()} with "
                    . "instructions on how to set their password.";
            }

            $originalPrivs = $this->object->getPrivs();


            if( Auth::user()->isSuperUser() ) {
                $this->object->setPrivs( $request->input( 'privs' ) );
                $this->object->setCustomer( D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) );
            } else {
                $this->object->setPrivs( $request->input( 'privs' ) < UserEntity::AUTH_SUPERUSER ? $request->input( 'privs' ) : UserEntity::AUTH_CUSTUSER );
                $this->object->setCustomer( Auth::getUser()->getCustomer() );
            }


            // we should only add admin users to customer type internal
            if( $this->object->isSuperUser() && !$this->object->getCustomer()->isTypeInternal() ) {
                AlertContainer::push( 'Users with full administrative access can only be added to internal customer types.', Alert::DANGER );
                return Redirect::back()->withErrors( $validator )->withInput();
            }

            if( $this->object->isSuperUser() && $originalPrivs != UserEntity::AUTH_SUPERUSER ) {
                AlertContainer::push( 'Please note that you have given this user full administrative access.', Alert::WARNING );
            }

            D2EM::flush();

            // Adding A non existing user /Editing
            if( !$request->input( 'existingUser' ) ) {
                // Editing
                if( $request->input( 'id', false ) ) {
                    // Getting the CustomerToUser object
                    if( !( $this->c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ "user" => $request->input( 'id' ) , "customer" => $request->input( 'custid' ) ] ) ) ) {
                        abort(404, 'Customer to user link not found');
                    }

                } else {
                    // Creating the CustomerToUser object
                    $this->c2u = new CustomerToUserEntity();
                    D2EM::persist( $this->c2u );

                    $this->c2u->setCustomer(  $this->object->getCustomer()  );
                    $this->c2u->setUser(  $this->object  );
                    $this->c2u->setCreatedAt(  new \DateTime  );

                }

                $this->c2u->setPrivs( $request->input( 'privs' ) );

                D2EM::flush();
            }

            if( !$request->input( 'id') ) {
                event( new WelcomeEvent( $this->object, false ) );
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postStoreRedirect()
    {
        if( !Auth::getUser()->isSuperUser() ) {
            return route( 'user@list' );
        } else {

            $redirect = session()->get( "user_post_store_redirect" );
            session()->remove( "user_post_store_redirect" );

            // retrieve the customer ID
            if( $redirect === 'customer@overview' ) {
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
     */
    protected function preDelete(): bool {

        if( !Auth::getUser()->isSuperUser() ) {
            if( $this->object->getCustomer()->getId() != Auth::getUser()->getCustomer()->getId() ) {
                Log::notice( Auth::getUser()->getUsername() . " tried to delete other customer user " . $this->object->getUsername() );
                abort( 401, 'You are not authorised to delete this user. The administrators have been notified.' );
            }
        }

        if( !( $this->object = D2EM::getRepository( $this->feParams->entity )->find( request()->input( 'id' ) ) ) ) {
            return abort( '404', 'User not found' );
        }

        // delete all the user's preferences
        foreach( $this->object->getPreferences() as $pref ) {
            $this->object->removePreference( $pref );
            D2EM::remove( $pref );
        }

        // delete all the user's login records
        foreach( $this->object->getLastLogins() as $ll ) {
            $this->object->removeLastLogin( $ll );
            D2EM::remove( $ll );
        }

        // delete all the user's API keys
        foreach( $this->object->getApiKeys() as $ak ) {
            $this->object->removeApiKey( $ak );
            D2EM::remove( $ak );
        }


        // delete all the C2U for the user
        foreach( $this->object->getCustomers2User() as $c2u ) {
            $this->object->removeCustomer( $c2u );
            D2EM::remove( $c2u );
        }

        if( request()->input( "redirect-to" ) ) {
            session()->put( "ixp_user_delete_custid", $this->object->getCustomer()->getId() );
        }

        Cache::forget( 'oss_d2u_user_' . $this->object->getId() );
        Log::notice( Auth::getUser()->getUsername()." deleted user" . $this->object->getUsername() );

        return true;
    }



    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postDeleteRedirect() {
        // retrieve the customer ID
        if( $custid = session()->get( "ixp_user_delete_custid" ) ) {
            session()->remove( "ixp_user_delete_custid" );
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
    public function sendWelcomeEmail( int $id, bool $resend = false ) {

        /** @var UserEntity $user */
        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
            abort(404, 'User not found');
        }

        event( new WelcomeEvent( $user, $resend ) );

        if( $resend ){
            AlertContainer::push( sprintf( 'The welcome email has been %s.', $resend ? 'resent' : 'sent' ), Alert::SUCCESS );
            return redirect::to( route( $this::$route_prefix . "@list" ) );
        }

        return true;
    }

    public function listCustomers( int $id ){

        if( !Auth::getUser()->isSuperUser() ){
            return abort( "403" );
        }

        /** @var UserEntity $user */
        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
            abort(404, 'User not found');
        }

        return view( 'user/list-customers' )->with([
            'user'                          => $user
        ]);
    }


    /**
     * Delete a Virtual Interface
     *
     * @param   Request $request instance of the current HTTP request
     * @param   int $userid ID of the User
     * @param   int $custid ID of the Customer
     *
     * @return  JsonResponse
     *
     * @throws
     */
    public function deleteCustomerToUser( Request $request,  int $userid, int $custid ): JsonResponse {

        /** @var UserEntity $user */
        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $userid ) ) ) {
            return abort( '404', "Unknown User" );
        }

        /** @var CustomerEntity $cust */
        if( !( $cust = D2EM::getRepository( CustomerEntity::class )->find( $custid ) ) ) {
            return abort( '404', "Unknown Customer" );
        }

        /** @var CustomerToUserEntity $c2u */
        if( !( $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ "user" => $userid, "customer" => $custid ]  ) ) ) {
            return abort( '404', "Unknown Customer to User relation" );
        }

        if( $user->getCustomer()->getId() == $cust->getId() ){
            // Will set a new when login
            $user->setCustomer( null );
        }


        D2EM::remove( $c2u );
        D2EM::flush();

        AlertContainer::push( 'The Association User/Customer (' .$user->getName(). ' / ' .$cust->getName(). ') has been deleted successfully.', Alert::SUCCESS );

        return response()->json( [ 'success' => true ]);
    }

}
