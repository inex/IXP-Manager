<?php

namespace IXP\Http\Requests;

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

use IXP\Models\{
    Router,
    User
};

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreRouter FormRequest
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IPX
 * @package    IXP\Http\Requests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StoreRouter extends FormRequest
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
        $this->merge( [ 'handle' => preg_replace( "/[^a-z0-9\-]/", '' , strtolower( $this->input( 'handle', '' ) ) ) ] );

        return [
            'handle'                   => 'required|string|max:255|unique:routers,handle' . ( $this->router ? ','. $this->router->id : '' ),
            'vlan_id'                  => 'required|integer|exists:vlan,id',
            'protocol'                 => 'required|integer|in:' . implode( ',', array_keys( Router::$PROTOCOLS ) ),
            'type'                     => 'required|integer|in:' . implode( ',', array_keys( Router::$TYPES ) ),
            'name'                     => 'required|string|max:255',
            'shortname'                => 'required|string|max:30',
            'router_id'                => 'required|ipv4',
            'peering_ip'               => 'required|ipv' . $this->protocol,
            'asn'                      => 'required|integer',
            'software'                 => 'required|integer|in:' . implode( ',', array_keys( Router::$SOFTWARES ) ),
            'software_version'         => 'nullable|string|max:255',
            'operating_system'         => 'nullable|string|max:255',
            'operating_system_version' => 'nullable|string|max:255',
            'mgmt_host'                => 'required|string|max:255',
            'api_type'                 => 'required|integer|in:' . implode( ',', array_keys( Router::$API_TYPES ) ),
            'api'                      => ( $this->api_type !== Router::API_TYPE_NONE ? 'url|required|regex:/.*[^\/]$/' : '' ),
            'lg_access'                => 'integer' . ( $this->api ? '|required|in:' . implode( ',', array_keys( User::$PRIVILEGES_ALL ) ) : '' ),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'api.regex' => 'The API URL must not end with a trailing slash',
        ];
    }
}