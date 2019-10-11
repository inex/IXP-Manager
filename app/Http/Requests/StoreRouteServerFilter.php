<?php

namespace IXP\Http\Requests;

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

use Entities\{
    Router              as RouterEntity,
    RouteServerFilter   as RouteServerFilterEntity
};

use Illuminate\Foundation\Http\FormRequest;

use IXP\Rules\IPv4Cidr;
use IXP\Rules\IPv6Cidr;


class StoreRouteServerFilter extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if( $this->input( "prefix" ) != '*'){
            $ipvCheck = $this->input( "protocol" ) == 4 ? new IPv4Cidr() : new IPv6Cidr();
        } else {
            $ipvCheck = "string";
        }

        return [
            'peer_id'               => 'required|integer|exists:Entities\Customer,id',
            'vlan_id'               => 'nullable|integer|exists:Entities\Vlan,id',
            'prefix'                => [ 'required', 'max:43', $ipvCheck ],
            'protocol'              => 'required|integer|in:' . implode( ',', array_keys( RouterEntity::$PROTOCOLS ) ),
            'action_advertise'      => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilterEntity::$ADVERTISE_ACTION_TEXT ) ),
            'action_receive'        => 'nullable|string|max:250|in:' . implode( ',', array_keys( RouteServerFilterEntity::$RECEIVE_ACTION_TEXT ) ),
        ];
    }

}