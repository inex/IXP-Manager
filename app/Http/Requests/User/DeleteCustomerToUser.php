<?php

namespace IXP\Http\Requests\User;

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

use Auth, D2EM, Log;

use Entities\{
    CustomerToUser as CustomerToUserEntity,
    User as UserEntity
};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


class DeleteCustomerToUser extends FormRequest
{
    /**
     * The User object
     * @var CustomerToUserEntity
     */
    public $c2u = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if( $this->user()->isCustUser() ){
            return false;
        }

        return true;
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



    public function withValidator( Validator $validator )
    {

        $validator->after( function( ) {

            // Delete the customer2user link
            /** @var CustomerToUserEntity $c2u  */
            if( !( $this->c2u = D2EM::getRepository( CustomerToUserEntity::class )->find( $this->input( "id" ) ) ) ) {
                return abort( '404', 'Customer/user association not found' );
            }

            if( !$this->user()->isSuperUser() ) {
                if( $this->c2u->getCustomer()->getId() != $this->user()->getCustomer()->getId() ) {
                    Log::notice( Auth::user()->username() . " tried to delete another customer's user: " . $c2u->getUser()->getName() . " from " . $c2u->getCustomer()->getName() );
                    abort( 403, 'You are not authorised to delete this user. The administrators have been notified.' );
                }
            }

        });

        return true;

    }

}