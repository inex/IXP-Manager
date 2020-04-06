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

use Auth;

use Entities\{
    PhysicalInterface as PhysicalInterfaceEntity
};

use Illuminate\Foundation\Http\FormRequest;
use IXP\Rules\IdnValidate;


class StoreVirtualInterfaceWizard extends FormRequest
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
        return [
            'cust'                  => 'required|integer|exists:Entities\Customer,id',
            'vlan'                  => 'required|integer|exists:Entities\Vlan,id',
            'trunk'                 => 'boolean',
            'customvlantag'         => 'integer',

            'switch'                => 'required|integer|exists:Entities\Switcher,id',
            'switch-port'           => 'required|integer|exists:Entities\SwitchPort,id',
            'status'                => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$STATES ) ),
            'speed'                 => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$SPEED ) ),
            'duplex'                => 'required|string|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$DUPLEX ) ),

            'maxbgpprefix'          => 'integer|nullable',
            'mcastenabled'          => 'boolean',
            'rsclient'              => 'boolean',
            'irrdbfilter'           => 'boolean',
            'rsmorespecifics'       => 'boolean',
            'as112client'           => 'boolean',

            'ipv4-enabled'          => 'boolean',
            'ipv4-address'          => 'ipv4' . ( $this->input('ipv4-enabled') ? '|required' : '|nullable' ),
            'ipv4-hostname'         => [ 'string', 'max:255' , ( ( config('ixp_fe.vlaninterfaces.hostname_required' ) && $this->input('ipv4-enabled') ) ? 'required' : 'nullable' ), new IdnValidate() ],
            'ipv4-bgp-md5-secret'   => 'string|max:255|nullable',
            'ipv4canping'           => 'boolean',
            'ipv4monitorrcbgp'      => 'boolean',

            'ipv6-enabled'          => 'boolean',
            'ipv6-address'          => 'ipv6' . ( $this->input('ipv6-enabled') ? '|required' : '|nullable' ),
            'ipv6-hostname'         => [ 'string', 'max:255' , ( ( config('ixp_fe.vlaninterfaces.hostname_required' ) && $this->input('ipv6-enabled') ) ? 'required' : 'nullable' ), new IdnValidate() ],
            'ipv6-bgp-md5-secret'   => 'string|max:255|nullable',
            'ipv6canping'           => 'boolean',
            'ipv6monitorrcbgp'      => 'boolean',

        ];
    }

}


