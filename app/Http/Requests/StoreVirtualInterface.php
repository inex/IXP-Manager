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

use Illuminate\Foundation\Http\FormRequest;


class StoreVirtualInterface extends FormRequest
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
            'cust'                  => 'integer'. ( $this->input('selectedCust') ? '|nullable' : '|required' ),
            'name'                  => 'string|max:255|nullable',
            'description'           => 'string|max:255|nullable',
            'channel-group'         => 'integer|nullable',
            'mtu'                   => 'integer|min:1|max:64000|nullable',
        ];
    }
}
