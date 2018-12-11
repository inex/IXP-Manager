<?php

namespace IXP\Http\Requests;

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

use Auth;

use Entities\{
    PhysicalInterface as PhysicalInterfaceEntity
};

use Illuminate\Foundation\Http\FormRequest;


class StorePhysicalInterface extends FormRequest
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
            'viid'                      => 'required|integer|exists:Entities\VirtualInterface,id',
            'switch'                    => 'required|integer|exists:Entities\Switcher,id',
            'switch-port'               => 'required|integer|exists:Entities\SwitchPort,id',
            'status'                    => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$STATES ) ),
            'speed'                     => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterfaceEntity::$SPEED ) ),
            'duplex'                    => 'required|string|in:'  . implode( ',', array_keys( PhysicalInterfaceEntity::$DUPLEX ) ),
            'notes'                     => 'string|nullable',
            'switch-fanout'             => 'integer' . ( $this->input('fanout') ? '|required|exists:Entities\Switcher,id'   : '|nullable' ),
            'switch-port-fanout'        => 'integer' . ( $this->input('fanout') ? '|required|exists:Entities\SwitchPort,id' : '|nullable' ),
        ];
    }
}