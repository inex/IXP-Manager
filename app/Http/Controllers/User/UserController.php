<?php

namespace IXP\Http\Controllers\User;

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
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

use Auth, Former, Hash, Log, Mail;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\View;

use Illuminate\Http\{
    RedirectResponse,
    Request
};

use IXP\Events\User\UserCreated as UserCreatedEvent;

use IXP\Http\Controllers\Controller;

use IXP\Http\Requests\User\{
    CheckEmail      as CheckEmailRequest,
    Delete          as DeleteRequest,
    Store           as StoreUser,
    Update          as UpdateUser,
};

use IXP\Mail\User\UserCreated as UserCreatedeMailable;

use IXP\Models\{
    Customer,
    CustomerToUser,
    User
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * User Controller
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Controllers\User
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class UserController extends Controller
{
    /**
     * Set as a session the redirect link
     *
     * @return Void
     */
    private function redirectLink(): void
    {
        if( !request()->old() ) {
            request()->session()->remove( "user_post_store_redirect" );
            session()->put( 'user_post_store_redirect', request()->headers->get( 'referer', "" ) );
        }
    }

    /**
     * Get tge list of user Depending on the Privilege
     *
     * @param User|null $u
     *
     * @return  array
     */
    private function getListData( User $u = null ): array
    {
        return User::selectRaw(
            'u.id as id, 
                        u.name AS name,
                        u.username as username, 
                        u.email as email,
                        u.created_at as created,
                        u.disabled as disabled, 
                        u.peeringdb_id as peeringdb_id,
                        u.updated_at AS lastupdated,
                        u.creator as creator, 
                        c.id as custid, 
                        c.name as customer,
                        COUNT( c2u.id ) as nbC2U,
                        MAX( c2u.privs ) as privileges,
                        c2u.id as c2uid,
                        u2fa.enabled as u2fa_enabled,
                        u2fa.id as psid ' )
            ->from( 'user AS u' )
            ->leftJoin( 'cust AS c', 'c.id', 'u.custid' )
            ->leftJoin( 'customer_to_users AS c2u', 'c2u.user_id', 'u.id' )
            ->leftJoin( 'user_2fa AS u2fa', 'u2fa.user_id', 'u.id' )
            ->when( !Auth::user()->isSuperUser(), function( Builder $q ) {
                return $q->where( 'c2u.customer_id', Auth::user()->custid )
                    ->where( 'c2u.privs', '<=', User::AUTH_CUSTADMIN );
            } )
            ->when( $u, function( Builder $q, $u ) {
                return $q->where( 'u.id', $u->id );
            } )->groupBy( 'id' )
            ->orderBy( 'username' )->get()->toArray();
    }

    /**
     * Display the User list
     *
     * @return  view
     *
     * @throws AuthorizationException
     */
    public function list(): View
    {
        $this->authorize( 'any', User::class );

        return view( 'user/index' )->with([
            'users'             => $this->getListData(),
            'nbC2u'             => CustomerToUser::selectRaw( 'user_id, COUNT( id ) AS nbC2U' )
                ->groupBy( 'user_id' )->get()->keyBy( 'user_id' )->toArray()
        ]);
    }

    /**
     * Display the first step form to Add a User object via email address
     *
     * @param  Request  $r
     *
     * @param  Customer|null  $cust
     *
     * @return View
     *
     * @throws AuthorizationException
     */
    public function createForm( Request $r, Customer $cust = null ): View
    {
        $this->authorize( 'any', User::class );

        $this->redirectLink();

        Former::populate([
            'cancelBtn'     =>  $r->old( 'cancelBtn', $r->headers->get( 'referer', "" ) ),
        ]);

        return view( 'user/create-wizard' )->with([
            'custid'             => $cust->id ?? false,
        ]);
    }

    /**
     * Function to check if the Email address is already used by a User
     *
     *      if yes we get the user information and display the information of the user
     *      if no we display the create/edit form
     *
     * @param  CheckEmailRequest  $r
     *
     * @return RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function createCheckEmail( CheckEmailRequest $r ): RedirectResponse
    {
        $this->authorize( 'any', User::class );

        $custid = null;

        if( $r->custid && ( $cust = Customer::find( $r->custid ) ) ){
            $custid = $cust->id;
        }

        if( $user = User::where( 'email',  $r->email )->first() ){
            return redirect( route( "customer-to-user@create", [ 'email' => $user->email ] ) . ( $custid ? "?cust=" . $custid : '' ) );
        }

        return redirect( route("user@create" , [ 'custid' => $custid, 'email' => $r->email ] ) );
    }

    /**
     * Allow to display the form to create a user
     *
     * @param  Request  $r
     *
     * @return  View
     *
     * @throws AuthorizationException
     */
    public function create( Request $r ): View
    {
        $this->authorize( 'any', User::class );

        if( !$r->session()->exists( 'user_post_store_redirect' ) ) {
            $this->redirectLink();
        }

        Former::populate([
            'name'                  => $r->old( 'name',                 $r->name ),
            'username'              => $r->old( 'username',             $r->username ),
            'email'                 => $r->old( 'email',                $r->email ),
            'authorisedMobile'      => $r->old( 'authorisedMobile',     $r->authorisedMobile ),
            'disabled'              => $r->old( 'disabled',             $r->disabled ),
            'cust'                  => $r->old( 'cust',                 $r->cust ),
            'linkCancel'            => $r->old( 'linkCancel',           $r->headers->get( 'referer', "" ) )
        ]);

        return view( 'user/edit' )->with( [
            'user'                  => false,
            'disableInputs'         => false,
            'isAdd'                 => true,
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'privs'                 => $this->getAllowedPrivs(),
            'c'                     => Customer::find( $r->custid ) ?? false,
        ]);
    }

    /**
     * Allow to create a User
     *
     * @param  StoreUser  $r  instance of the current HTTP request
     *
     * @return  RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function store( StoreUser $r ): RedirectResponse
    {
        $this->authorize( 'any', User::class );

        // Creating the User object
        $user = new User;
        $user->creator          = Auth::user()->username;
        $user->password         = Hash::make( Str::random(16) );
        $user->name             = $r->name;
        $user->authorisedMobile = $r->authorisedMobile;
        $user->username         = strtolower( $r->username );
        $user->email            = strtolower( $r->email );
        $user->disabled         = !$r->disabled; // input as enable in the view
        $user->lastupdatedby    = Auth::id();
        $user->privs            = $r->privs;
        $user->custid           = Auth::user()->isSuperUser() ? $r->custid : Auth::user()->custid;
        $user->save();

        // Creating the CustomerToUser object
        $c2u = new CustomerToUser;
        $c2u->customer_id   = $user->custid;
        $c2u->user_id       = $user->id;
        $c2u->privs         = $r->privs;
        $c2u->extra_attributes = [ "created_by" => [ "type" => "user" , "user_id" => $user->id ] ];
        $c2u->save();

        if( (int)$r->privs === User::AUTH_SUPERUSER ) {
            AlertContainer::push( 'Please note that you have given this user full administrative access.', Alert::WARNING );
        }

        // Send Email related to the event
        event( new UserCreatedEvent( $user ) );

        Log::notice( Auth::user()->username . ' Created a User  with ID ' . $user->id );

        AlertContainer::push( "User created. A welcome email is being sent to {$user->email} with "
            . "instructions on how to set their password. ", Alert::SUCCESS );

        return redirect( $this->postStoreRedirect() );
    }

    /**
     * Allow to display the form to Edit a user
     *
     * @param  User  $u
     * @param  Request  $r
     *
     * @return  View
     *
     * @throws AuthorizationException
     */
    public function edit( Request $r, User $u ): View
    {
        $this->authorize( 'access', $u );

        $isSuperUser = Auth::user()->isSuperUser();

        if( !request()->session()->exists( 'user_post_store_redirect' ) ) {
            $this->redirectLink();
        }

        $dataCust = [
            'name'             => $r->old( 'name',                  $u->name                ),
            'username'         => $r->old( 'username',              $u->username            ),
            'email'            => $r->old( 'email',                 $u->email               ),
            'authorisedMobile' => $r->old( 'authorisedMobile',      $u->authorisedMobile    ),
            'disabled'         => $r->old( 'disabled',              !$u->disabled           ),
            'linkCancel'       => $r->old( 'linkCancel',            $r->headers->get( 'referer', "" ) ),
        ];

        $datac2u = [];

        $listC2u = $isSuperUser ? $u->customerToUser : $u->customerToUser()->where( 'customer_id', Auth::user()->custid )->get();

        foreach( $listC2u as $c2u ) {
            if( $isSuperUser ) {
                $datac2u[ 'privs_' . $c2u->id ] = $r->old( 'privs_' . $c2u->id , $c2u->privs );
            } else {
                $datac2u[ 'privs'  ] = $r->old( 'privs',  $c2u->privs );
            }
        }

        Former::populate( array_merge( $dataCust, $datac2u ) );

        return view( 'user/edit' )->with([
            'user'                  => $u,
            'disableInputs'         => !$isSuperUser,
            'isAdd'                 => false,
            'custs'                 => Customer::orderBy( 'name' )->get(),
            'privs'                 => $this->getAllowedPrivs(),
            'c'                     => Customer::find( $r->custid ) ?? false,
        ]);
    }

    /**
     * Allow to update a User
     *
     * @param  UpdateUser  $r  instance of the current HTTP request
     * @param  User  $u
     *
     * @return  RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function update( UpdateUser $r, User $u ): RedirectResponse
    {
        $this->authorize( 'access', $u );

        // Superuser OR Logged User edit his own user
        if( ( $isSuperUser = Auth::user()->isSuperUser() ) || $u->id === Auth::id() ) {
            $u->name = $r->name;
            $u->authorisedMobile = $r->authorisedMobile;
        }

        if( $isSuperUser ) {
            $u->username    = strtolower( $r->username );
            $u->email       = $r->email;
            $u->disabled    = !$r->disabled;// displayed as enabled in the view

            // Delete Remember Token for the user if disabled
            if(!$r->disabled){
                $u->userRememberTokens()->delete();
            }
        }

        $u->lastupdatedby = Auth::id();
        $u->save();

        if( !$isSuperUser ) {
            /** @var $c2u CustomerToUser  */
            if( !( $c2u = $u->customerToUser()->where( 'customer_id', Auth::user()->custid  )->first() ) ) {
                abort(404, 'UserToCustomer not found');
            }

            $c2u->privs =  $r->privs;
            $c2u->save();
        }

        Log::notice( Auth::user()->username . ' updated a User with ID ' . $u->id );
        AlertContainer::push( 'User updated', Alert::SUCCESS );
        return redirect( $this->postStoreRedirect() );
    }

    /**
     * Redirect the user post store
     */
    protected function postStoreRedirect()
    {
        if( Auth::user()->isSuperUser() ) {
            $redirect = session( "user_post_store_redirect" );
            session()->forget( "user_post_store_redirect" );

            if( strpos( $redirect, "customer/overview" ) ) {
                return $redirect;
            }
        }

        if( Auth::user()->isCustUser() ) {
            return '';
        }

        return route( 'user@list' );
    }

    /**
     * Display the patch panel information
     *
     * @param  User  $u  ID of the patch panel
     *
     * @return  view
     *
     * @throws AuthorizationException
     */
    public function view( User $u ): View
    {
        $this->authorize( 'access', $u );

        $user = $this->getListData( $u )[ 0 ] ?? null;

        if( !$user ){
            abort( 404,'User does not exist');
        }

        return view( 'user/view' )->with([
            'u'     => $user,
            'c2us'  => $u->customerToUser
        ]);
    }

    /**
     * Delete a user and everything related !!
     *
     * @param  DeleteRequest  $r
     * @param  User  $u
     *
     * @return  RedirectResponse
     *
     * @throws AuthorizationException
     */
    public function delete( DeleteRequest $r, User $u ) : RedirectResponse
    {
        $this->authorize( 'any', User::class );

        // delete all the user's API keys
        $u->apiKeys()->delete();

        // delete all the C2U for the user
        foreach( $u->customerToUser as $c2u ) {
            // delete all the user's login records
            $c2u->userLoginHistories()->delete();
            $c2u->delete();
        }

        // preserve and delete logs
        foreach( \IXP\Models\Log::whereUserId( $u->id )->orderBy( 'id', 'ASC' )->get() as $l ) {
            Log::info( "[USER DEL - PRESERVING LOG {$l->id}] {$l->model}:{$l->model_id}:{$l->action} ::: {$l->message} ::: " . json_encode( $l->models ) . " ::: {$l->created_at->format('Y-m-d H:i:s')} :::ENDS:::" );
            $l->delete();
        }

        $u->delete();

        AlertContainer::push('User deleted.', Alert::SUCCESS );
        Log::notice( Auth::user()->username." deleted user" . $u->username );

        // If the user delete itself and is loggued as the same customer logout
        if( Auth::id() === $u->id ) {
            Auth::logout();
            return redirect( route( "login@showForm" ) );
        }

        if( Auth::user()->isSuperUser() && strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
            return redirect( route( "customer@overview", [ 'cust' => $u->custid , "tab" => "users"] ) );
        }

        return redirect( route( "user@list" ) );
    }

    /**
     * Send or resend the welcome email to a new user
     *
     * @param User      $u
     *
     * @return RedirectResponse
     */
    public function resendWelcomeEmail( User $u ): RedirectResponse
    {
        Mail::to( $u->email )->send( new UserCreatedeMailable( $u, true ) );
        AlertContainer::push( sprintf( 'The welcome email has been resent' ), Alert::SUCCESS );

        if( Auth::user()->isSuperUser() && strpos( request()->headers->get('referer', "" ), "customer/overview" ) ) {
            return redirect( route( "customer@overview", [ 'cust' => $u->custid , "tab" => "users"] ) );
        }

        return redirect( route( "user@list" ) );
    }
}