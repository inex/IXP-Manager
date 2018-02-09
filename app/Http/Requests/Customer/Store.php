<?php

namespace IXP\Http\Requests\Customer;

/*
 * Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee.
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
    IRRDBConfig as IRRDBConfigEntity
};

use Illuminate\Foundation\Http\FormRequest;


class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // middleware ensures superuser access only so always authorised here:
        return Auth::getUser()->isSuperUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $arrayDetails = [
            'name'                  => 'required|string|max:255',
            'type'                  => 'required|integer|in:' . implode( ',', array_keys( CustomerEntity::$CUST_TYPES_TEXT ) ),
            'shortname'             => 'required|string|max:255|unique:Entities\Customer,shortname'. ( $this->input('id') ? ','. $this->input('id') : '' ),
            'corpwww'               => 'nullable|url',
            'datejoin'              => 'nullable|date',
            'dateleft'              => 'nullable|date',
            'status'                => 'required|integer|in:' . implode( ',', array_keys( CustomerEntity::$CUST_STATUS_TEXT ) ),
            'md5support'            => 'nullable|string|in:' . implode( ',', array_keys( CustomerEntity::$MD5_SUPPORT ) ),
            'abbreviatedName'       => 'required|string|max:255',

        ];

        $arrayOtherInfo = [
            'autsys'                => 'nullable|int|min:0',
            'maxprefixes'           => 'nullable|int|min:0',
            'peeringemail'          => 'nullable|email',
            'peeringmacro'          => 'nullable|string|max:255',
            'peeringmacrov6'        => 'nullable|string|max:255',
            'peeringpolicy'         => 'nullable|string|in:' . implode( ',', array_keys( CustomerEntity::$PEERING_POLICIES ) ),
            'irrdb'                 => 'nullable|integer'. $this->input( 'irrdb' ) ? "|exists:Entities\IRRDBConfig,id" : '',
            'nocphone'              => 'nullable|string|max:255',
            'noc24hphone'           => 'nullable|string|max:255',
            'nocfax'                => 'nullable|string|max:255',
            'nocemail'              => 'nullable|email|max:255',
            'nochours'              => 'nullable|string|in:' . implode( ',', array_keys( CustomerEntity::$NOC_HOURS ) ),
            'nocwww'                => 'nullable|url',
        ];

        return $this->input( 'type' ) == CustomerEntity::TYPE_ASSOCIATE  ? $arrayDetails : array_merge( $arrayDetails, $arrayOtherInfo ) ;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator( $validator )
    {
        $validator->after(function ($validator) {

            if( ( $this->input( 'type' ) == CustomerEntity::TYPE_FULL || $this->input( 'type' ) == CustomerEntity::TYPE_PROBONO ) && !$this->input( 'irrdb' ) ) {
                $validator->errors()->add( 'irrdb', 'Please select an IRRDB Config.' );
                return;
            } else if( $this->input( 'irrdb' ) ) {
                if( !( $irrdb = D2EM::getRepository( IRRDBConfigEntity::class )->find( $this->input( 'irrdb' ) ) ) ) {
                    $validator->errors()->add( 'irrdb', 'Invalid IRRDB source' );
                    return;
                }
            }
        });
    }
}