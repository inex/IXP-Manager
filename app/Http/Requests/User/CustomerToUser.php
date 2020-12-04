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

use D2EM;

use Entities\{
    Customer            as CustomerEntity,
    CustomerToUser      as CustomerToUserEntity,
    User                as UserEntity,
};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

class CustomerToUser extends FormRequest
{
    /**
     * The Customer object
     * @var CustomerEntity
     */
    public $cust = null;

    /**
     * The User object
     * @var UserEntity
     */
    public $existingUser = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // check that this user can effect these changes
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

        $validator->after( function( Validator $validator ) {

            if( !$this->input( 'existingUserId' ) ) {
                AlertContainer::push( "You must select one user from the list." , Alert::DANGER );
                return false;
            }

            $this->cust         = D2EM::getRepository( CustomerEntity::class    )->find( $this->input( 'custid'         ) );
            $this->existingUser = D2EM::getRepository( UserEntity::class        )->find( $this->input( 'existingUserId' ) );

            if( !$this->user()->isSuperUser() && $this->existingUser->getCustomer()->getId() != $this->cust->getId() ) {
                abort( "403" );
            }

            if( D2EM::getRepository( CustomerToUserEntity::class)->findOneBy( [ "customer" => $this->cust , "user" => $this->existingUser ] ) ) {
                $validator->errors()->add('custid',  "This user is already associated with " . $this->cust->getName() );
                return false;
            }

            if( $this->input( 'privs' ) == UserEntity::AUTH_SUPERUSER ) {

                $cust = $this->user()->superUser() ? $this->cust : $this->user()->getCustomer();

                if( !$this->user()->superUser() )  {
                    $validator->errors()->add( 'privs',  "You are not allowed to set any user as a super user." );
                    return false;
                }

                if( !$cust->isTypeInternal() ) {
                    $validator->errors()->add( 'privs',  "You are not allowed to set super user privileges for non-internal (IXP) customer types." );
                    return false;
                }

                AlertContainer::push( 'Please note that you have given this user full administrative access (super user privilege).', Alert::WARNING );
            }

            return true;
        });
    }

}