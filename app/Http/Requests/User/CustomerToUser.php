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
    Customer as CustomerEntity
};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


class CustomerToUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // middleware ensures superuser access only so always authorised here:
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
            'custid'                => 'required|integer|exists:Entities\Customer,id',
            'existingUserId'        => 'required|integer|exists:Entities\User,id',
            'privs'                 => 'required|integer|in:' . implode( ',', array_keys( UserEntity::$PRIVILEGES_ALL ) ),
        ];
    }



    public function withValidator( Validator $validator )
    {
        $validator->after( function( $validator )
        {

            if( !$this->input( 'existingUserId' ) )
            {
                AlertContainer::push( "You need to select one User from the list." , Alert::DANGER );
                return false;
            }

            $cust = D2EM::getRepository( CustomerEntity::class )->find( $this->input( 'custid' ) );

            if( D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "customer" => $cust , "user" => $this->input( 'existingUserId' ) ] ) )
            {
                AlertContainer::push( "This user is already associated with " . $cust->getName()  , Alert::DANGER );
                return Redirect::back()->withErrors($validator)->withInput();
            }

            if( !$this->allowPrivSuperUser( $this->input( 'privs' ), $cust ) )
            {
                AlertContainer::push( "You are not allowed to set this User as a Super User for " . $cust->getName()  , Alert::DANGER );
                return Redirect::back()->withErrors( $validator )->withInput();
            }

            if( $this->input( 'privs' ) == UserEntity::AUTH_SUPERUSER )
            {
                AlertContainer::push( 'Please note that you have given this user full administrative access.', Alert::WARNING );
            }

            return true;
        });
    }

}