<?php

namespace IXP\Http\Requests\User\CustomerToUser;

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

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use IXP\Models\{
    Customer,
    CustomerToUser,
    User
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

class Store extends FormRequest
{
    /**
     * The Customer object
     *
     * @var Customer
     */
    public $cust = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        if( $this->user()->isCustUser() ) {
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
        $rules = [
            'user_id'       => 'required|integer|exists:user,id',
            'privs'         => 'required|integer|in:' . implode( ',', array_keys( User::$PRIVILEGES_ALL ) ),
        ];

        if( Auth::user()->isSuperUser() ) {
            $extraRules = [
                'customer_id'   => 'required|integer|exists:cust,id',
            ];
        }

        return array_merge( $rules, $extraRules ?? [] );
    }

    /**
     * @param Validator $validator
     */
    public function withValidator( Validator $validator ): void
    {
        $validator->after( function( Validator $validator ) {
            if( !$this->user_id ) {
                AlertContainer::push( "You must select one user from the list." , Alert::DANGER );
                return false;
            }

            $this->cust = Auth::user()->isSuperUser() ? Customer::find( $this->customer_id ) : Auth::user()->customer;

            if( CustomerToUser::where( 'customer_id', $this->cust->id )->where( 'user_id', $this->user_id )->get()->isNotEmpty() ) {
                AlertContainer::push( "This user is already associated with " . $this->cust->name, Alert::DANGER );
                $validator->errors()->add( 'customer_id',  " " );
                return false;
            }

            if( (int)$this->privs === User::AUTH_SUPERUSER ) {
                if( !$this->user()->isSuperUser() )  {
                    $validator->errors()->add( 'privs',  "You are not allowed to set any user as a super user." );
                    return false;
                }

                if( !$this->cust->typeInternal() ) {
                    $validator->errors()->add( 'privs',  "You are not allowed to set super user privileges for non-internal (IXP) customer types." );
                    return false;
                }

                AlertContainer::push( 'Please note that you have given this user full administrative access (super user privilege).', Alert::WARNING );
            }

            return true;
        });
    }
}