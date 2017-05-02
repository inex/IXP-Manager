<?php

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

namespace IXP\Http\Requests;

use Auth;

use Entities\{
    Router as RouterEntity,
    User as UserEntity
};

use Illuminate\Foundation\Http\FormRequest;


class StoreRouter extends FormRequest
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
        $this->merge( [ 'handle' => preg_replace( "/[^a-z0-9\-]/", '' , strtolower( $this->input( 'handle', '' ) ) ) ] );

        return [
            'handle'                => 'required|string|max:255|unique:Entities\Router,handle' . ( $this->input('id') ? ','. $this->input('id') : '' ),
            'vlan'                  => 'required|integer',
            'protocol'              => 'required|integer|in:' . implode( ',', array_keys( RouterEntity::$PROTOCOLS ) ),
            'type'                  => 'required|integer|in:' . implode( ',', array_keys( RouterEntity::$TYPES ) ),
            'name'                  => 'required|string|max:255',
            'shortname'             => 'required|string|max:20',
            'router_id'             => 'required|ipv4',
            'peering_ip'            => 'required|ipv' . $this->input('protocol'),
            'asn'                   => 'required|integer',
            'software'              => 'required|integer|in:' . implode( ',', array_keys( RouterEntity::$SOFTWARES ) ),
            'mgmt_host'             => 'required|string|max:255',
            'api_type'              => 'required|integer|in:' . implode( ',', array_keys( RouterEntity::$API_TYPES ) ),
            'api'                   => 'url' . ( $this->input('api') ? '|required' : '' ),
            'lg_access'             => 'integer' . ( $this->input('api') ? '|required|in:' . implode( ',', array_keys( UserEntity::$PRIVILEGES_ALL ) ) : '' ),
        ];
    }
}