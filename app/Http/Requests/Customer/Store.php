<?php

namespace IXP\Http\Requests\Customer;

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
    Customer    as CustomerEntity
};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


/**
 * Customer Store Request
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customers
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
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
        $validateCommonDetails = [
            'id'                    => 'nullable|integer|exists:Entities\Customer,id',
            'name'                  => 'required|string|max:255',
            'type'                  => 'required|integer|in:' . implode( ',', array_keys( CustomerEntity::$CUST_TYPES_TEXT ) ),
            'shortname'             => 'required|string|max:30|regex:/[a-z0-9]+/|unique:Entities\Customer,shortname'. ( $this->input('id') ? ','. $this->input('id') : '' ),
            'corpwww'               => 'nullable|url|max:255',
            'datejoin'              => 'date',
            'dateleft'              => 'nullable|date',
            'status'                => 'required|integer|in:' . implode( ',', array_keys( CustomerEntity::$CUST_STATUS_TEXT ) ),
            'md5support'            => 'nullable|string|in:'  . implode( ',', array_keys( CustomerEntity::$MD5_SUPPORT ) ),
            'abbreviatedName'       => 'required|string|max:30',
        ];

        $validateOtherDetails = [
            'autsys'                => 'int|min:1',
            'maxprefixes'           => 'nullable|int|min:0',
            'peeringemail'          => 'email',
            'peeringmacro'          => 'nullable|string|max:255',
            'peeringmacrov6'        => 'nullable|string|max:255',
            'peeringpolicy'         => 'string|in:' . implode( ',', array_keys( CustomerEntity::$PEERING_POLICIES ) ),
            'irrdb'                 => 'nullable|integer|exists:Entities\IRRDBConfig,id',
            'nocphone'              => 'nullable|string|max:255',
            'noc24hphone'           => 'nullable|string|max:255',
            'nocemail'              => 'email|max:255',
            'nochours'              => 'nullable|string|in:' . implode( ',', array_keys( CustomerEntity::$NOC_HOURS ) ),
            'nocwww'                => 'nullable|url|max:255',
            'reseller'              => 'nullable|integer|exists:Entities\Customer,id',
        ];

        return $this->input( 'type' ) == CustomerEntity::TYPE_ASSOCIATE  ? $validateCommonDetails : array_merge( $validateCommonDetails, $validateOtherDetails ) ;
    }


    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator( Validator $validator )
    {
        $validator->after( function( $validator ) {
            $this->checkReseller( $validator );
        });
    }

    /**
     * Checks reseller status such that:
     *
     * - we cannot remove reseller state from a reseller with resold customers
     * - we cannot reassign a resold customer to another reseller if they still have ports belonging to the current reseller
     * - we cannot unset reseller status while a resold customer has ports belonging to a reseller
     *
     * @param  Validator  $validator
     * @return bool If false, the form is not valid
     */
    private function checkReseller( Validator $validator ): bool {

        $c = null;
        if( $this->input( 'id' ) ) {
            /** @var CustomerEntity $c */
            $c = D2EM::getRepository( CustomerEntity::class )->find( $this->input( 'id' ) );
        }

        if( $this->input( 'isResold' ) ) {

            /** @var CustomerEntity $reseller */
            if( !$this->input( "reseller" ) || !( $reseller = D2EM::getRepository( CustomerEntity::class )->find( $this->input( "reseller" ) ) ) ) {
                $validator->errors()->add('reseller', 'Please choose a reseller');
                return false;
            }

            // are we changing reseller?
            if( $c && $c->getReseller() && $c->getReseller()->getId() != $reseller->getId() ) {
                foreach( $c->getVirtualInterfaces() as $vi ) {
                    foreach( $vi->getPhysicalInterfaces() as $pi ) {
                        if( $pi->getFanoutPhysicalInterface() && $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getCustomer()->getId() == $c->getReseller()->getId() ) {
                            $validator->errors()->add('reseller', 'You cannot change the reseller because there are still fanout ports from the current reseller'
                                . 'linked to this customer\'s physical interfaces. You need to reassign these first.' );
                            return false;
                        }
                    }
                }
            }

        } else if( $c && $c->getReseller() ) {

            foreach( $c->getVirtualInterfaces() as $vi ) {
                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                    if( $pi->getFanoutPhysicalInterface() && $pi->getFanoutPhysicalInterface()->getVirtualInterface()->getCustomer()->getId() == $c->getReseller()->getId() ) {
                        $validator->errors()->add('reseller', 'You can not change this resold customer state because there are still physical interface(s) of '
                         . 'this customer linked to fanout ports of the current reseller. You need to reassign these first.' );
                        return false;
                    }
                }
            }
        }


        if( !$this->input( 'isReseller' ) && $c && $c->getIsReseller() && count( $c->getResoldCustomers() ) ) {
            $validator->errors()->add('isReseller', 'You can not change the reseller state because this customer still has resold customers. '
                . 'You need to reassign these first.' );
            return false;
        }

        return true;
    }



}