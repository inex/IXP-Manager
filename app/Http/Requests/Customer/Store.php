<?php

namespace IXP\Http\Requests\Customer;

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

use IXP\Models\Customer;

/**
 * Customer Store Request
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // middleware ensures superuser access only so always authorised here:
        return Auth::getUser()->isSuperUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $validateCommonDetails = [
            'name'                  => 'required|string|max:255',
            'type'                  => 'required|integer|in:' . implode( ',', array_keys( Customer::$CUST_TYPES_TEXT ) ),
            'shortname'             => 'required|string|max:30|regex:/[a-z0-9]+/|unique:cust,shortname'. ( $this->cust ? ','. $this->cust->id : '' ),
            'corpwww'               => 'nullable|url|max:255',
            'datejoin'              => 'required|date',
            'dateleft'              => 'nullable|date',
            'status'                => 'required|integer|in:' . implode( ',', array_keys( Customer::$CUST_STATUS_TEXT ) ),
            'md5support'            => 'nullable|string|in:'  . implode( ',', array_keys( Customer::$MD5_SUPPORT ) ),
            'abbreviatedName'       => 'required|string|max:30',
        ];

        $validateOtherDetails = [
            'autsys'                => 'int|min:1',
            'maxprefixes'           => 'nullable|int|min:0',
            'peeringemail'          => 'email',
            'peeringmacro'          => 'nullable|string|max:255',
            'peeringmacrov6'        => 'nullable|string|max:255',
            'peeringpolicy'         => 'string|in:' . implode( ',', array_keys( Customer::$PEERING_POLICIES ) ),
            'irrdb'                 => 'nullable|integer|exists:irrdbconfig,id',
            'nocphone'              => 'nullable|string|max:255',
            'noc24hphone'           => 'nullable|string|max:255',
            'nocemail'              => 'email|max:255',
            'nochours'              => 'nullable|string|in:' . implode( ',', array_keys( Customer::$NOC_HOURS ) ),
            'nocwww'                => 'nullable|url|max:255',
            'reseller'              => 'nullable|integer|exists:cust,id',
        ];

        return $this->type == Customer::TYPE_ASSOCIATE  ? $validateCommonDetails : array_merge( $validateCommonDetails, $validateOtherDetails ) ;
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     *
     * @return void
     */
    public function withValidator( Validator $validator ): void
    {
        $validator->after( function( $validator ) {
            if( $this->type != Customer::TYPE_ASSOCIATE ) {
                $this->checkReseller( $validator );
            }
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
     *
     * @return bool If false, the form is not valid
     */
    private function checkReseller( Validator $validator ): bool
    {
        /** @var Customer $c */
        $c = $this->cust;

        if( $this->isResold ) {
            if( !$this->reseller || !( $reseller = Customer::find( $this->reseller ) ) ) {
                $validator->errors()->add('reseller', 'Please choose a reseller');
                return false;
            }

            // are we changing reseller?
            if( $c && $c->resellerObject && $c->resellerObject->id !== $reseller->id ) {
                foreach( $c->virtualInterfaces as $vi ) {
                    foreach( $vi->physicalInterfaces as $pi ) {
                        if( $pi->fanoutPhysicalInterface && $pi->fanoutPhysicalInterface->virtualInterface->custid === $c->reseller ) {
                            $validator->errors()->add('reseller', 'You cannot change the reseller because there are still fanout ports from the current reseller'
                                . 'linked to this customer\'s physical interfaces. You need to reassign these first.' );
                            return false;
                        }
                    }
                }
            }
        } else if( $c && $c->resellerObject ) {
            foreach( $c->virtualInterfaces as $vi ) {
                foreach( $vi->physicalInterfaces as $pi ) {
                    if( $pi->fanoutPhysicalInterface && $pi->fanoutPhysicalInterface->virtualInterface->custid === $c->reseller ) {
                        $validator->errors()->add('reseller', 'You can not change this resold customer state because there are still physical interface(s) of '
                         . 'this customer linked to fanout ports of the current reseller. You need to reassign these first.' );
                        return false;
                    }
                }
            }
        }

        if( !$this->isReseller && $c && $c->isReseller && $c->resoldCustomers()->count() ) {
            $validator->errors()->add('isReseller', 'You can not change the reseller state because this customer still has resold customers. '
                . 'You need to reassign these first.' );
            return false;
        }
        return true;
    }
}