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

use IXP\Models\PhysicalInterface;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Store PhysicalInterface FormRequest
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StorePhysicalInterface extends FormRequest
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
        return [
            'virtualinterfaceid'        => 'required|integer|exists:virtualinterface,id',
            'switch'                    => 'required|integer|exists:switch,id',
            'switchportid'              => 'required|integer|exists:switchport,id',
            'status'                    => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterface::$STATES ) ),
            'speed'                     => 'required|integer|in:' . implode( ',', array_keys( PhysicalInterface::$SPEED ) ),
            'duplex'                    => 'required|string|in:'  . implode( ',', array_keys( PhysicalInterface::$DUPLEX ) ),
            'rate_limit'                => 'nullable|integer|min:0',
            'notes'                     => 'string|nullable',
            'switch-fanout'             => 'integer' . ( $this->fanout ? '|required|exists:switch,id'   : '|nullable' ),
            'switch-port-fanout'        => 'integer' . ( $this->fanout ? '|required|exists:switchport,id' : '|nullable' ),
        ];
    }
}