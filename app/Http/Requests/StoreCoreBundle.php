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
    CoreBundle as CoreBundleEntity
};

use Illuminate\Foundation\Http\FormRequest;


class StoreCoreBundle extends FormRequest
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
            'customer'                  => 'required|integer',
            'description'               => 'required|string|max:255',
            'graph-title'               => 'required|string|max:255',
            'cost'                      => 'nullable|integer',
            'type'                      => 'required|integer|in:' . implode( ',', array_keys( CoreBundleEntity::$TYPES ) ),
            'subnet'                    => ( $this->input('type') == CoreBundleEntity::TYPE_L3_LAG ) ? "required" : "nullable",
            'mtu'                       => 'required|integer|min:0',
            'pi-name-a'                 => ( $this->input('type') == CoreBundleEntity::TYPE_L2_LAG || $this->input('type') == CoreBundleEntity::TYPE_L3_LAG) ? "required|string|max:255" : "nullable",
            'pi-name-b'                 => ( $this->input('type') == CoreBundleEntity::TYPE_L2_LAG || $this->input('type') == CoreBundleEntity::TYPE_L3_LAG) ? "required|string|max:255" : "nullable",
            'pi-channel-number-a'       => ( $this->input('type') == CoreBundleEntity::TYPE_L2_LAG || $this->input('type') == CoreBundleEntity::TYPE_L3_LAG) ? "required|integer|min:0" : "nullable",
            'pi-channel-number-b'       => ( $this->input('type') == CoreBundleEntity::TYPE_L2_LAG || $this->input('type') == CoreBundleEntity::TYPE_L3_LAG) ? "required|integer|min:0" : "nullable",
        ];
    }
}