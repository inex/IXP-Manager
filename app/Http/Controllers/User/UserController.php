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

use Auth, D2EM, Former, Log, Mail, Redirect;

use Entities\{
    Customer        as CustomerEntity,
    CustomerToUser  as CustomerToUserEntity,
    User            as UserEntity,
};

use Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use IXP\Events\User\UserCreated as UserCreatedEvent;
use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\User\{
    AddCheckEmail   as AddCheckEmailRequest,
    AddStore        as AddStoreUser,
    EditStore       as EditStoreUser,
    Delete          as DeleteUser
};

use IXP\Mail\User\UserCreated as UserCreatedeMailable;
use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};


/**
 * User Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 *
 * @category   PatchPanel
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends Controller
{

    protected $cancelButton = null;


    public function __construct() {

//        if( !request()->old() ){
//            $this->cancelButton = request()->headers->get( 'referer', "" );
//        }

    }

    /**
     * Get tge list of user Depending on the Privilege
     *
     * @param int $id
     *
     * @return  array
     */
    private function getListData( $id = null ){
        return Auth::getUser()->isSuperUser() ? D2EM::getRepository( UserEntity::class )->getAllForFeListSuperUser( $id ) : D2EM::getRepository( UserEntity::class )->getAllForFeListCustAdmin( Auth::getUser(), $id );
    }

    /**
     * Display the User list
     *
     * @return  view
     */
    public function index( ): View {
        return view( 'User/index' )->with([
            'users'             => $this->getListData(),
            'nbC2u'             => D2EM::getRepository( UserEntity::class )->getNumberOfCustomers()
        ]);
    }

    /**
     * Display the first step form to Add a User object via email address
     *
     * @return View
     */
    public function addForm(): View
    {
        //$this->redirectLink();

        Former::populate([
            'cancelBtn'             => array_key_exists( 'cancelBtn',           request()->old() ) ? request()->old()['cancelBtn']          : request()->headers->get( 'referer', "" ),
        ]);

        $cust = D2EM::getRepository( CustomerEntity::class )->find( request()->input("cust" , "" ) );

        return view( 'User/add-wizard' )->with([
            'custid'             => $cust ? $cust->getId() : false,
        ]);
    }

    /**
     * Function to check if the Email address is already used by a User
     *
     *      if yes we get the user information and display the information of the user
     *      if no we display the add/edit form
     *
     * @param AddCheckEmailRequest $request
     *
     * @return bool|RedirectResponse
     *
     * @throws
     */
    public function addCheckEmail( AddCheckEmailRequest $request ){
        $custid = null;
        /** @var UserEntity $user */
        $user = D2EM::getRepository( UserEntity::class )->findOneBy( [ 'email' => $request->input( 'email' ) ] );

        if( $request->input( "custid", false ) && ( $cust = D2EM::getRepository( CustomerEntity::class )->find( $request->input( "custid", false ) ) ) ){
            $custid = $cust->getId();
        }

        // building the redirect url
        $url = $user ? route( "customer-to-user@add", [ 'user' => $user->getEmail() ] ) . ( $custid ? "?cust=" . $custid : '' ) : route("user@add" , [ 'e-mail' => $request->input( 'email' ) , 'cust' => $custid ] );

        return redirect( $url );
    }

    /**
     * Allow to display the form to create a user
     *
     * @return  View
     */
    public function add(): View {
        $old = request()->old();

//        if( !request()->session()->exists( 'user_post_store_redirect' ) ) {
//            $this->redirectLink();
//        }

        Former::populate([
            'name'                  => array_key_exists( 'name',                $old ) ? $old['name']               : request()->input( "name" ),
            'username'              => array_key_exists( 'username',            $old ) ? $old['username']           : request()->input( "username" ),
            'email'                 => array_key_exists( 'email',               $old ) ? $old['email']              : request()->input( "e-mail" ),
            'authorisedMobile'      => array_key_exists( 'authorisedMobile',    $old ) ? $old['authorisedMobile']   : request()->input( "authorisedMobile" ),
            'enabled'               => array_key_exists( 'enabled',             $old ) ? $old['enabled']            : request()->input( "enabled" ),
            'custid'                => array_key_exists( 'custid',              $old ) ? $old['custid']             : request()->input( "cust" ),
            'linkCancel'            => array_key_exists( 'linkCancel',          $old ) ? $old['linkCancel']         : request()->headers->get( 'referer', "" ),
        ]);

        return view( 'user/edit' )->with([
            'user'                  => false,
            'disabledInputs'        => false,
            'isAdd'                 => true,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'privs'                 => $this->getAllowedPrivs(),
            'c'                     => D2EM::getRepository( CustomerEntity::class)->find( request()->input( "cust", false ) ) ?? false,
        ]);
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
        if( Auth::getUser()->isSuperUser() ) {
            // Getting all the customer for the user
            if( !( $listC2u = D2EM::getRepository( CustomerToUserEntity::class)->findBy( [ "user" => $user->getId() ] ) ) ) {
                $this->unauthorized();
            }
        } else {
            // Getting only the current logged customer for the user
            if( !( $listC2u = D2EM::getRepository( CustomerToUserEntity::class)->findBy( [ "customer" => Auth::getUser()->getCustomer()->getId() , "user" => $user->getId() ] ) ) ) {
                $this->unauthorized();
            }
        }

        return $listC2u;
    }

    /**
     * Allow to display the form to Edit a user
     *
     * @param int $id
     *
     * @return  View
     */
    public function edit( int $id ): View {
        $old = request()->old();

        if( !( $u = D2EM::getRepository( UserEntity::class )->find( $id ) ) ) {
            abort(404, 'User not found');
        }

        $dataCust = [
            'name'             => array_key_exists( 'name',                $old ) ? $old['name']                 : $u->getName(),
            'username'         => array_key_exists( 'username',            $old ) ? $old['username']             : $u->getUsername(),
            'email'            => array_key_exists( 'email',               $old ) ? $old['email']                : $u->getEmail(),
            'authorisedMobile' => array_key_exists( 'authorisedMobile',    $old ) ? $old['authorisedMobile']     : $u->getAuthorisedMobile(),
            'enabled'          => array_key_exists( 'enabled',             $old ) ? ( $old['enabled'] ? 1 : 0 )  : ( $u->getDisabled() ? 0 : 1 ),
            'linkCancel'       => array_key_exists( 'linkCancel',          $old ) ? $old['linkCancel']         : request()->headers->get( 'referer', "" ),
        ];

        $datac2u = [];

        /** @var CustomerToUserEntity $c2u */
        foreach( $this->getC2Ulist( $u ) as $c2u ) {

            if( Auth::getUser()->isSuperUser() ){
                $datac2u[ 'privs_' . $c2u->getId() ] = array_key_exists( 'privs_' . $c2u->getId(),  $old ) ? $old[ 'privs_' . $c2u->getId() ]   : $c2u->getPrivs();
            } else {
                $datac2u[ 'privs'  ] = array_key_exists( 'privs',  $old    ) ? $old[ 'privs']  : $c2u->getPrivs();
            }
        }

        Former::populate( array_merge( $dataCust, $datac2u ) );


        $disabledInputs = Auth::getUser()->isSuperUser() ? false : true;


        return view( 'user/edit' )->with([
            'user'                  => $u,
            'disabledInputs'        => $disabledInputs,
            'isAdd'                 => false,
            'custs'                 => D2EM::getRepository( CustomerEntity::class )->getAsArray(),
            'privs'                 => $this->getAllowedPrivs(),
            'c'                     => D2EM::getRepository( CustomerEntity::class)->find( request()->input( "cust", false ) ) ?? false,
        ]);
    }

    /**
     * Allow to create a User
     *
     * @param   AddStoreUser $request instance of the current HTTP request
     *
     * @return  redirect
     *
     * @throws
     */
    public function addStore( AddStoreUser $request ) {

        // Creating the User object
        $user = new UserEntity;
        D2EM::persist( $user );
        $user->setCreated( now() );
        $user->setCreator( Auth::getUser()->getUsername() );
        $user->setPassword( Hash::make( str_random(16) ) );
        $user->setName( $request->input( 'name' ) );
        $user->setAuthorisedMobile( $request->input( 'authorisedMobile' ) );
        $user->setUsername( strtolower( $request->input( 'username' ) ) );
        $user->setEmail( $request->input( 'email' ) );
        $user->setDisabled( $request->input( 'enabled', 0 ) == "1" ? false : true );
        $user->setLastupdated( now() );
        $user->setLastupdatedby( Auth::getUser()->getId() );
        $user->setPrivs( $request->input( 'privs' ) );
        $user->setCustomer( Auth::user()->isSuperUser() ? D2EM::getRepository( CustomerEntity::class )->find( $request->input( 'custid' ) ) : Auth::getUser()->getCustomer() );

        D2EM::flush();

        // Creating the CustomerToUser object
        $c2u = new CustomerToUserEntity();
        D2EM::persist( $c2u );
        $c2u->setCustomer( $user->getCustomer()  );
        $c2u->setUser( $user  );
        $c2u->setCreatedAt( now() );
        $c2u->setPrivs( $request->input( 'privs' ) );
        $c2u->setExtraAttributes( [ "created_by" => [ "type" => "user" , "user_id" => $user->getId() ] ] );

        D2EM::flush();

        if( $request->input( 'privs' ) == UserEntity::AUTH_SUPERUSER ) {
            AlertContainer::push( 'Please note that you have given this user full administrative access.', Alert::WARNING );
        }

        event( new UserCreatedEvent( $user ) );

        Log::notice( Auth::user()->getUsername() . ' Added a User  with ID ' . $user->getId() );

        AlertContainer::push( "User added successfully. A welcome email is being sent to {$user->getEmail()} with "
            . "instructions on how to set their password. ", Alert::SUCCESS );

        return redirect()->to( $this->postStoreRedirect()  );
    }

    /**
     * Allow to Edit a User
     *
     * @param   EditStoreUser $request instance of the current HTTP request
     *
     * @return  redirect
     *
     * @throws
     */
    public function editStore( EditStoreUser $request ) {

        /** @var $user UserEntity*/
        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $request->input( 'id' ) ) ) ) {
            abort(404, 'User not found');
        }

        // Superuser OR Adding user OR Logged User edit his own user
        if( Auth::getUser()->isSuperUser() || $user->getId() == Auth::getUser()->getId() ) {
            $user->setName( $request->input( 'name' ) );
            $user->setAuthorisedMobile( $request->input( 'authorisedMobile' ) );
        }

        // Superuser OR Adding user
        if( Auth::getUser()->isSuperUser() ) {
            $user->setUsername( strtolower( $request->input( 'username' ) ) );
            $user->setEmail( $request->input( 'email' ) );
            $user->setDisabled( $request->input( 'enabled', 0 ) == "1" ? false : true );
        }

        $user->setLastupdated( now() );
        $user->setLastupdatedby( Auth::getUser()->getId() );

        D2EM::flush();

        // if editing and not super user
        if( !Auth::getUser()->isSuperUser() ) {

            /** @var $c2u CustomerToUserEntity */
            if( !( $c2u = D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ 'user' => $user , 'customer' => Auth::getUser()->getCustomer() ] ) ) ) {
                abort(404, 'UserToCustomer not found');
            }

            $c2u->setPrivs( $request->input( "privs" ) );
            D2EM::flush();
        }

        Log::notice( Auth::user()->getUsername() . ' edited a User with ID ' . $user->getId() );

        AlertContainer::push( 'The User has been added', Alert::SUCCESS );

        return redirect()->to( $this->postStoreRedirect() );
    }


    /**
     * @inheritdoc
     *
     * @return null|string
     */
    protected function postStoreRedirect()
    {
        if( !Auth::getUser()->isSuperUser() )
        {
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
     * Display the patch panel informations
     *
     * @param   int $id ID of the patch panel
     *
     * @return  view
     */
    public function view( int $id = null ): View {

        return view( 'user/view' )->with([
            'u'                        => $this->getListData( $id )[0]
        ]);
    }


    /**
     * Delete a user and everything related !!
     *
     * @param   DeleteUser $request Instance of HTTP request
     * @return  RedirectResponse
     * @throws
     */
    public function delete( DeleteUser $request) : RedirectResponse {

        // delete all the user's preferences
        foreach( $request->user->getPreferences() as $pref ) {
            $request->user->removePreference( $pref );
            D2EM::remove( $pref );
        }

        // delete all the user's API keys
        foreach( $request->user->getApiKeys() as $ak ) {
            $request->user->removeApiKey( $ak );
            D2EM::remove( $ak );
        }

        // delete all the C2U for the user
        /** @var CustomerToUserEntity $c2u */
        foreach( $request->user->getCustomers2User() as $c2u ) {
            // delete all the user's login records
            D2EM::getRepository( CustomerToUserEntity::class )->deleteUserLoginHistory( $c2u->getId() );

            $request->user->removeCustomer( $c2u );
            D2EM::remove( $c2u );
        }

        D2EM::remove( $request->user );
        D2EM::flush();

        AlertContainer::push( sprintf( 'The User has been deleted.' ), Alert::SUCCESS );
        Log::notice( Auth::getUser()->getUsername()." deleted user" . $request->user->getUsername() );

        // If the user delete itself and is loggued as the same customer logout
        if( Auth::getUser()->getId() == $request->user->getId() ) {
            Auth::logout();
            return Redirect::to( route( "login@showForm" ) );
        }

        if( Auth::getUser()->isSuperUser() ) {
            if( strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
                return Redirect::to( route( "customer@overview", [ "id" => $request->user->getCustomer()->getId() , "tab" => "users"] ) );
            }
        }

        return Redirect::to( route( "user@list" ) );
    }

    /**
     * Send or resend the welcome email to a new user
     *
     * @param Request $request
     *
     * @return bool|RedirectResponse
     */
    public function resendWelcomeEmail( Request $request )
    {
        /** @var UserEntity $user */
        if( !( $user = D2EM::getRepository( UserEntity::class )->find( $request->input( "id" ) ) ) ) {
            abort(404, 'User not found');
        }

        Mail::to( $user->getEmail() )->send( new UserCreatedeMailable( $user, true ) );

        AlertContainer::push( sprintf( 'The welcome email has been resent' ), Alert::SUCCESS );

        return redirect::to( route( "user@list" ) );
    }
}