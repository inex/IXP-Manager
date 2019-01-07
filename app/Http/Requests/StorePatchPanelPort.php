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

use Illuminate\Foundation\Http\FormRequest;

class StorePatchPanelPort extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // middleware ensures superuser access only so always authorised here:
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $allocated = $this->input('allocated');

        $prewired = $this->input('prewired');

        $required = ($allocated or $prewired) ? '' : 'required';

        return [
            'number'                => $required.'|string|max:255',
            'patch_panel'           => $required,
            'state'                 => 'required|integer',
            'assigned_at'           => 'nullable|date',
            'connected_at'          => 'nullable|date',
            'ceased_requested_at'   => 'nullable|date',
            'ceased_at'             => 'nullable|date',
            'last_state_change_at'  => 'nullable|date',
        ];
    }
}
