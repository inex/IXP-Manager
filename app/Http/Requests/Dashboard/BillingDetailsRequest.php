<?php

namespace IXP\Http\Requests\Dashboard;

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

use Auth, Countries;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Dashboard Billing Store Request
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests\Dashboard
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class BillingDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // middleware ensures custadmin access only so always authorised here:
        return Auth::getUser()->isCustAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'billingContactName'    => 'nullable|string|max:255',
            'billingAddress1'       => 'nullable|string|max:255',
            'billingAddress2'       => 'nullable|string|max:255',
            'billingAddress3'       => 'nullable|string|max:255',
            'billingTownCity'       => 'nullable|string|max:255',
            'billingPostcode'       => 'nullable|string|max:255',
            'billingCountry'        => 'nullable|string|max:255|in:' . implode( ',', array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ),
            'billingEmail'          => 'nullable|email|max:255',
            'billingTelephone'      => 'nullable|string|max:255',
            'invoiceEmail'          => 'nullable|email|max:255',
        ];
    }
}