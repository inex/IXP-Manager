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

use Illuminate\Foundation\Http\FormRequest;

use IXP\Models\PatchPanelPort;

/**
 * Store PatchPanelPort FormRequest
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StorePatchPanelPort extends FormRequest
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
        $required   = ( $this->allocated || $this->prewired ) ? 'nullable' : 'required';
        $required2  = $this->duplex  ? 'required' : 'nullable';
        return [
            'switch_port_id'        => 'nullable|integer|exists:switchport,id',
            'customer_id'           => 'nullable|integer|exists:cust,id',
            'partner_port'          => $required2 . '|integer|exists:patch_panel_port,id',
            'number'                => $required . '|string|max:255',
            'patch_panel'           => $required,
            'description'           => 'nullable|string|max:255',
            'colo_circuit_ref'      => 'nullable|string|max:255',
            'colo_billing_ref'      => 'nullable|string|max:255',
            'ticket_ref'            => 'nullable|string|max:255',
            'state'                 => 'nullable|integer|in:' . implode( ',', array_keys( PatchPanelPort::$STATES ) ),
            'chargeable'            => 'nullable|integer|in:' . implode( ',', array_keys( PatchPanelPort::$CHARGEABLES ) ),
            'assigned_at'           => 'nullable|date',
            'connected_at'          => 'nullable|date',
            'cease_requested_at'    => 'nullable|date',
            'ceased_at'             => 'nullable|date',
            'last_state_change_at'  => 'nullable|date',
        ];
    }
}