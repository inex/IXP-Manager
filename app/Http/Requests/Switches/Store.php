<?php

namespace IXP\Http\Requests\Switches;

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

use Auth;

use Illuminate\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;
use IXP\Rules\IdnValidate;

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
    public function rules()
    {
        return [
            'name'                      => 'required|string|max:255|unique:switch,name'      . ( $this->input('id') ? ','. $this->input('id') : '' ),
            'hostname'                  => [ 'required', 'string', 'max:255', 'unique:switch,hostname' . ( $this->input('id') ? ','. $this->input('id') : '' ), new IdnValidate() ],
            'cabinetid'                 => 'required|integer|exists:cabinet,id',
            'infrastructure'            => 'required|integer|exists:infrastructure,id',
            'snmppasswd'                => 'nullable|string|max:255',
            'vendorid'                  => 'required|integer|exists:vendor,id',
            'ipv4addr'                  => 'nullable|ipv4',
            'ipv6addr'                  => 'nullable|ipv6',
            'model'                     => 'nullable|string|max:255',
            'asn'                       => 'nullable|integer|min:1',
            'loopback_ip'               => 'nullable|string|max:255|unique:switch,loopback_ip' . ( $this->input('id') ? ','. $this->input('id') : '' ),
            'loopback_name'             => 'nullable|string|max:255',
            'mgmt_mac_address'          => 'nullable|string|max:17|regex:/^[a-f0-9:\.\-]{12,17}$/i',
        ];
    }

}
