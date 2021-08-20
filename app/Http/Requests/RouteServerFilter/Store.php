<?php

namespace IXP\Http\Requests\RouteServerFilter;

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
use Illuminate\Foundation\Http\FormRequest;

use IXP\Models\{
    Customer,
    Router,
    RouteServerFilter,
    Vlan
};

use IXP\Rules\{
    IPv4Cidr,
    Ipv4SubnetSize,
    IPv6Cidr,
    Ipv6SubnetSize
};
/**
 * Store RouteServerFilter FormRequest
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Request\RouteServerFilter
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Store extends FormRequest
{
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // If all vlans or all peers are selected (value === 0) then reset to null to avoid conflict in DB
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
        if( $this->received_prefix !== '*'){
            $ipvCheckRec       = $this->protocol === '4' ? new IPv4Cidr()          : new IPv6Cidr();
            $subnetCheckRec    = $this->protocol === '4' ? new Ipv4SubnetSize()    : new Ipv6SubnetSize();
        } else {
            $ipvCheckRec        = "string";
            $subnetCheckRec     = "";
        }

        if( $this->advertised_prefix !== '*'){
            $ipvCheckAdv       = $this->protocol === '4' ? new IPv4Cidr()          : new IPv6Cidr();
            $subnetCheckAdv    = $this->protocol === '4' ? new Ipv4SubnetSize()    : new Ipv6SubnetSize();
        } else {
            $ipvCheckAdv = "string";
            $subnetCheckAdv = "";
        }
        
        return [
            'peer_id' => [ 'nullable', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Customer::find( $value ) ) {
                        return $fail( 'Customer is invalid / does not exist.' );
                    }
                }
            ],
            'vlan_id' => [ 'nullable', 'integer',
                function( $attribute, $value, $fail ) {
                    if( !Vlan::find( $value ) ) {
                        return $fail( 'Vlan is invalid / does not exist.' );
                    }
                }
            ],
            'advertised_prefix'     => [ 'nullable', 'max:43', $ipvCheckAdv, $subnetCheckAdv ],
            'received_prefix'       => [ 'nullable', 'max:43', $ipvCheckRec, $subnetCheckRec ],
            'protocol'              => 'nullable|integer|in:' . implode( ',', array_keys( Router::$PROTOCOLS ) ),
            'action_advertise'      => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilter::$ADVERTISE_ACTION_TEXT ) ),
            'action_receive'        => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilter::$RECEIVE_ACTION_TEXT ) ),
        ];
    }
}