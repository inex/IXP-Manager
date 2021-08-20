<?php

namespace IXP\Http\Requests\Customer;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use IXP\Models\CompanyBillingDetail;

use Webpatser\Countries\CountriesFacade as Countries;

/**
 * BillingInformation FormRequest
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests\Customer
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class BillingInformation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
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
            'registeredName'        => 'nullable|string|max:255',
            'companyNumber'         => 'nullable|string|max:255',
            'jurisdiction'          => 'nullable|string|max:255',
            'address1'              => 'nullable|string|max:255',
            'address2'              => 'nullable|string|max:255',
            'address3'              => 'nullable|string|max:255',
            'townCity'              => 'nullable|string|max:255',
            'postcode'              => 'nullable|string|max:255',
            'country'               => 'nullable|string|max:255|in:' . implode( ',', array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ),

            'billingContactName'    => 'nullable|string|max:255',
            'billingFrequency'      => 'nullable|string|max:255|in:' . implode( ',', array_keys( CompanyBillingDetail::$BILLING_FREQUENCIES ) ),
            'billingAddress1'       => 'nullable|string|max:255',
            'billingAddress2'       => 'nullable|string|max:255',
            'billingAddress3'       => 'nullable|string|max:255',
            'billingTownCity'       => 'nullable|string|max:255',
            'billingPostcode'       => 'nullable|string|max:255',
            'billingCountry'        => 'nullable|string|max:255|in:' . implode( ',', array_values( Countries::getListForSelect( 'iso_3166_2' ) ) ),
            'billingEmail'          => 'nullable|email|max:255',
            'billingTelephone'      => 'nullable|string|max:255',
            'invoiceMethod'         => 'nullable|string|max:255|in:' . implode( ',', array_keys( CompanyBillingDetail::$INVOICE_METHODS ) ),
            'invoiceEmail'          => 'nullable|string|max:255',
            'vatRate'               => 'nullable|string|max:255',
            'vatNumber'             => 'nullable|string|max:255',
        ];
    }
}