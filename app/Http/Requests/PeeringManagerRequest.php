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
use Auth, Validator;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PeeringManagerRequest FormRequest
 *
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class PeeringManagerRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function __construct()
    {
        parent::__construct();
        Validator::extend("emails", function( $attribute, $value, $parameters ) {
            $rules = [
                'email' => 'required|email',
            ];

            $addresses = explode( ',', $value );

            foreach( $addresses as $address ) {
                $data = [
                    'email' => trim( $address )
                ];
                $validator = Validator::make( $data, $rules );
                if( $validator->fails() ) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'to'              => 'required|emails',
            'cc'              => 'nullable|emails',
            'bcc'             => 'nullable|emails',
            'subject'         => 'required|string',
            'message'         => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'to.emails'  => 'One or more of the email addresses are invalid',
            'cc.emails'  => 'One or more of the email addresses are invalid',
            'bcc.emails' => 'One or more of the email addresses are invalid',
        ];
    }
}