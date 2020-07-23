<?php

namespace IXP\Http\Requests\RouteServerFilter;

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

use Illuminate\Foundation\Http\FormRequest;

use IXP\Models\{
    Customer,
    Router,
    RouteServerFilter,
    User,
    Vlan
};

use IXP\Rules\{
    IPv4Cidr,
    Ipv4SubnetSize,
    IPv6Cidr,
    Ipv6SubnetSize
};

class Store extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // If all vlans/peers are select (value 0) override the value to null, to avoid conflict in DB
        $vlanid =  $this->vlan_id === '0' ? null : $this->vlan_id;
        $peerid =  $this->peer_id === '0' ? null : $this->peer_id;
        $this->merge([ 'vlan_id' => $vlanid, 'peer_id' => $peerid ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $prefixRequired = $this->protocol ? "required" : "nullable";

        if( $this->prefix !== '*'){
            $ipvCheck       = $this->protocol === 4 ? new IPv4Cidr()          : new IPv6Cidr();
            $subnetCheck    = $this->protocol === 4 ? new Ipv4SubnetSize()    : new Ipv6SubnetSize();
        } else {
            $ipvCheck = "string";
            $subnetCheck = "";
        }

        return [
            'peer_id'               => [ 'nullable', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Customer::whereId( $value )->exists() ) {
                        return $fail( 'Customer is invalid / does not exist.' );
                    }
                }
            ],
            'vlan_id'               => [ 'nullable', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Vlan::whereId( $value )->exists() ) {
                        return $fail( 'Vlan is invalid / does not exist.' );
                    }
                }
            ],
            'prefix'                => [ $prefixRequired , 'max:43', $ipvCheck, $subnetCheck ],
            'protocol'              => 'nullable|integer|in:' . implode( ',', array_keys( Router::$PROTOCOLS ) ),
            'action_advertise'      => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilter::$ADVERTISE_ACTION_TEXT ) ),
            'action_receive'        => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilter::$RECEIVE_ACTION_TEXT ) ),
        ];
    }
}