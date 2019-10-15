<?php

namespace IXP\Http\Requests\RouteServerFilter;

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
use D2EM, Route;

use Entities\{
    Customer            as  CustomerEntity,
    RouteServerFilter   as  RouteServerFilterEntity,
    User                as  UserEntity,
};

use Illuminate\Foundation\Http\FormRequest;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};


class CheckPrivsCustAdmin extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute;

    public function __construct(){
        $this->redirect = '/';
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        $minAuth = UserEntity::AUTH_CUSTADMIN;

        $action =  explode('@', Route::getCurrentRoute()->getActionName() )[1];

        if( in_array( $action, [ "view", "list" ] ) ){
            $minAuth = UserEntity::AUTH_CUSTUSER;
        }

        if( !ixp_min_auth( $minAuth ) ) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Configure the validator instance.
     *
     * @param $validator
     *
     * @return void|bool
     */
    public function withValidator( $validator )
    {
        $validator->after( function ( $validator ) {
            if( request( "custid" ) ) {
                if( !( $this->c = D2EM::getRepository( CustomerEntity::class )->find( request( "custid" ) ) ) ) {
                    abort( 404, "Unknown customer" );
                }
            }

            if( request( "id" ) ) {
                if( !( $this->rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( request( "id" ) ) ) ) {
                    abort( 404, "Unknown customer" );
                }

                $this->c = $this->rsf->getCustomer();
            }

            if( !$this->user()->isSuperUser() ) {
                if( $this->c->getId() != $this->user()->getCustomer()->getId() ){
                    abort( 403, "Access forbidden" );
                }
            }

            if( !$this->c->isRouteServerClient() ){
                AlertContainer::push( "Only router server client customers can access this page", Alert::DANGER );
                $validator->errors()->add( '',  " " );
                return false;
            }

        } );
    }
}