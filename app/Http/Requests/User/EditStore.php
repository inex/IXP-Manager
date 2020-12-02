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

use Auth, D2EM;

use Entities\{
    Customer    as CustomerEntity,
    User        as UserEntity,
};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


class EditStore extends FormRequest
{
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
         $addUserInfo = [];

        // If its a superuser
        if( Auth::user()->superUser() ) {
            $infoArray = [
                'name'                                              => 'required|string|max:255',
                'username'                                          => 'required|string|min:3|max:255|regex:/^[a-z0-9\-_\.]{3,255}$/|unique:Entities\User,username' . ( $this->input( 'id' ) ? ',' . $this->input( 'id' ) : '' ),
                'email'                                             => 'required|email|max:255',
                'authorisedMobile'                                  => 'nullable|string|max:50',
            ];

        } else  {

            $infoArray = [
                'privs'         => 'required|integer|in:' . implode( ',', array_keys( UserEntity::$PRIVILEGES_ALL ) )
            ];

            // If the User edit himself
            if( Auth::id() === (int)$this->input( 'id' ) ) {
                $addUserInfo = [
                    'name'                                              => 'required|string|max:255',
                    'authorisedMobile'                                  => 'nullable|string|max:50'

                ];
            }

            $infoArray = array_merge( $infoArray, $addUserInfo );

        }

        return $infoArray ;
    }



    public function withValidator( Validator $validator )
    {
        if( !Auth::user()->superUser() ) {
            if( !$validator->fails() ) {

                $validator->after( function( Validator $validator ) {

                    $cust = Auth::user()->isSuperUser() ? D2EM::getRepository( CustomerEntity::class )->find( $this->input( 'custid' ) ) : Auth::user()->custid;

                    if( $this->input( 'privs' ) == UserEntity::AUTH_SUPERUSER ) {
                        if( !Auth::user()->superUser() || Auth::user()->superUser() && !$cust->isTypeInternal() ) {
                            $validator->errors()->add( 'privs', "You are not allowed to set this User as a Super User for " . $cust->getName() );
                            return false;
                        }
                    }

                    return true;
                });

            }
        }


        return false;

    }

}