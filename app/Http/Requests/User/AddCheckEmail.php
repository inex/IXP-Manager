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

use AUth, D2EM;

use Entities\{
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity,
};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

class AddCheckEmail extends FormRequest
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
            'email'                 => 'required|email|max:255',
        ];
    }


    public function withValidator( Validator $validator )
    {
        $validator->after( function( Validator $validator ) {
            if( !Auth::getUser()->isSuperUser() ) {
                /** @var UserEntity $user */
                foreach( D2EM::getRepository( UserEntity::class )->findBy( [ 'email' => $this->input( 'email' ) ] ) as $user ) {

                    if( D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "customer" => Auth::getUser()->getCustomer() , "user" => $user ] ) ){
                        AlertContainer::push( "A user already exists with that email address for your company." , Alert::DANGER );

                        $validator->errors()->add( 'email',  " " );
                        return false;
                    }
                }
            }

            return true;
        });
    }

}