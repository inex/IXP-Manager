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


class Delete extends FormRequest
{
    /**
     * The User object
     * @var UserEntity
     */
    public $user = null;

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
        return [
            'id'          => 'required|integer|exists:Entities\User,id',
        ];
    }



    public function withValidator( Validator $validator )
    {
        if( !$validator->fails() ) {

            $validator->after( function( ) {

                $this->user = D2EM::getRepository( UserEntity::class )->find( request()->input( 'id' , false) );

                if( !Auth::getUser()->isSuperUser() ) {
                   // Check if the custadmin try to delete a user from an other Customer
                    if( !D2EM::getRepository( CustomerToUserEntity::class )->findOneBy( [ "user" => $this->user , "customer" => Auth::getUser()->custid ] ) ) {
                        Log::notice( Auth::getUser()->username . " tried to delete other customer user " . $this->user->getUsername() );
                        abort( 401, 'You are not authorised to delete this user. The administrators have been notified.' );
                    }
                }

                // Check if a custadmin try to delete a User that has more than 1 customer to User (this should never happen)
                if( !Auth::getUser()->isSuperUser() && count( $this->user->getCustomers() ) > 1  ) {
                    abort( 401, 'You are not authorised to delete this user. The administrators have been notified.' );
                }

            });

        }

        return true;

    }

}