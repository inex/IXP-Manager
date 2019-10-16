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
use D2EM;

use Entities\{
    Customer            as CustomerEntity,
    Router              as RouterEntity,
    RouteServerFilter   as RouteServerFilterEntity,
    User                as UserEntity,
};

use Illuminate\Foundation\Http\FormRequest;

use IXP\Rules\{
    IPv4Cidr,
    Ipv4SubnetSize,
    IPv6Cidr,
    Ipv6SubnetSize
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};


class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if( !ixp_min_auth( UserEntity::AUTH_CUSTADMIN ) ) {
            return false;
        } else {
            return true;
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $prefixRequired = $this->input( "protocol" ) ? "required" : "nullable";

        if( $this->input( "prefix" ) != '*'){
            $ipvCheck = $this->input( "protocol" ) == 4 ? new IPv4Cidr() : new IPv6Cidr();
            $subnetCheck = $this->input( "protocol" ) == 4 ? new Ipv4SubnetSize() : new Ipv6SubnetSize();
        } else {
            $ipvCheck = "string";
            $subnetCheck = "";
        }

        return [
            'peer_id'               => 'required|integer|exists:Entities\Customer,id',
            'vlan_id'               => 'nullable|integer|exists:Entities\Vlan,id',
            'prefix'                => [ $prefixRequired , 'max:43', $ipvCheck, $subnetCheck ],
            'protocol'              => 'nullable|integer|in:' . implode( ',', array_keys( RouterEntity::$PROTOCOLS ) ),
            'action_advertise'      => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilterEntity::$ADVERTISE_ACTION_TEXT ) ),
            'action_receive'        => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilterEntity::$RECEIVE_ACTION_TEXT ) ),
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param $validator
     *
     * @return void
     */
    public function withValidator( $validator )
    {
        $validator->after( function ( $validator ) {
            /** @var RouteServerFilterEntity $rsf */
            if( $this->input( 'id' ) && $this->rsf = D2EM::getRepository( RouteServerFilterEntity::class )->find( $this->input( 'id' ) ) ) {
                if( !$this->rsf ) {
                    abort(404, 'Router Server Filter not found' );
                }

                $this->c = $this->rsf->getCustomer();

            } else {
                $this->rsf = new RouteServerFilterEntity;
                D2EM::persist( $this->rsf );

                if( !( $this->c = D2EM::getRepository( CustomerEntity::class )->find( request( "custid" ) ) ) ) {
                    abort( 404, "Unknown customer" );
                }
            }

            if( !$this->user()->isSuperUser() ) {
                if( $this->c->getId() != $this->user()->getCustomer()->getId() ){
                    abort( 403, "Access forbidden" );
                }
            }

            if( !$this->c->isRouteServerClient() ){
                AlertContainer::push( "Only router server client customers can access this action.", Alert::DANGER );
                $validator->errors()->add( '',  " " );
                return false;
            }
        } );

    }

}