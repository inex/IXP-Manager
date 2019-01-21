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

use IPTools\{
    IP,
    Network
};


/**
 * IP Address Request
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 *
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StoreIpAddress extends FormRequest
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
        return [
            'vlan'                  => 'required|integer|exists:Entities\Vlan,id',
            'network'               => 'required|string|max:255',
            'decimal'               => 'bool',
            'skip'                  => 'bool',
            'overflow'              => 'bool',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $netblock = trim( htmlspecialchars( $this->input('network' ) )  );
            $pieces  = explode( '/', $netblock );

            // $pieces[ 0 ] => IP address, if exist $pieces[ 1 ] => subnet
            if( !filter_var( $pieces[0], FILTER_VALIDATE_IP ) ) {
                $validator->errors()->add('network', 'The IP address format is invalid' );
            } else {

                $ip = new IP( $pieces[ 0 ] );

                if( $ip->version == 'IPv4' ) {
                    if( !isset( $pieces[ 1 ] ) ) {
                        $pieces[ 1 ] = 32;
                    }

                    if( !filter_var( $pieces[ 1 ], FILTER_VALIDATE_INT ) || $pieces[ 1 ] < 24 || $pieces[ 1 ] > 32 ) {
                        $validator->errors()->add( 'network', 'The subnet size is invalid. For IPv4, it must be an integer between between 24 and 32 inclusive.' );
                    }
                } else {
                    if( !isset( $pieces[ 1 ] ) ) {
                        $pieces[ 1 ] = 128;
                    }

                    if( !filter_var( $pieces[ 1 ], FILTER_VALIDATE_INT ) || $pieces[ 1 ] < 120 || $pieces[ 1 ] > 128 ) {
                        $validator->errors()->add( 'network', 'The subnet size is invalid. For IPv6, it must be an integer between between 120 and 128 inclusive.' );
                    }
                }

                $network = Network::parse( $netblock );

                if( $network->getFirstIP() != $pieces[ 0 ] && substr( $pieces[ 0 ], -3 ) != '::0' ) {
                    $validator->errors()->add( 'network', 'The network you have specified above does not start at the correct IP address. '
                        . 'While it is technically valid, we require it to be set correctly to avoid unintentional errors. '
                        . 'In this instance, the correct starting address would be: <code>' . $network->getFirstIP() . '</code>.' );
                }
            }

        });
    }
}
