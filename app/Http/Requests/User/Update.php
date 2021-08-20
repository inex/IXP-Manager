<?php

namespace IXP\Http\Requests\User;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use IXP\Models\{
    Customer,
    User
};

/**
 * Update FormRequest
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests\User
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
    public function rules(): array
    {
         $addUserInfo = [];

        // If its a superuser
        if( Auth::user()->isSuperUser() ) {
            $infoArray = [
                'name'                                              => 'required|string|max:255',
                'username'                                          => 'required|string|min:3|max:255|regex:/^[a-z0-9\-_\.]{3,255}$/|unique:user,username,' . $this->u->id,
                'email'                                             => 'required|email|max:255',
                'authorisedMobile'                                  => 'nullable|string|max:50',
            ];
        } else  {
            $infoArray = [
                'privs'         => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES_ALL ) )
            ];

            // If the User edit himself
            if( Auth::id() === (int)$this->u->id ) {
                $addUserInfo = [
                    'name'                                              => 'required|string|max:255',
                    'authorisedMobile'                                  => 'nullable|string|max:50'
                ];
            }
            $infoArray = array_merge( $infoArray, $addUserInfo );
        }
        return $infoArray ;
    }

    /**
     * @param Validator $validator
     *
     * @return bool
     */
    public function withValidator( Validator $validator ): bool
    {
        $isSuperUser = Auth::user()->isSuperUser();
        if( !$validator->fails() && !$isSuperUser ) {
            $validator->after( function( Validator $validator ) use ( $isSuperUser ) {
                $cust = $isSuperUser ? Customer::find( $this->custid ) : Auth::user()->customer;

                if( (int)$this->privs === User::AUTH_SUPERUSER ) {
                    if( !$isSuperUser || ( $isSuperUser && !$cust->typeInternal() ) ) {
                        $validator->errors()->add( 'privs', "You are not allowed to set this User as a Super User for " . $cust->name );
                        return false;
                    }
                }
                return true;
            });
        }
        return false;
    }
}