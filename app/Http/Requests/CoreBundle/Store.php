<?php

namespace IXP\Http\Requests\CoreBundle;

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

use Illuminate\Validation\Validator;

use IXP\Models\{
    CoreBundle,
    SwitchPort
};

use IXP\Utils\View\Alert\{
    Alert,
    Container as AlertContainer
};

/**
 * Store CoreBundle FormRequest
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests\CoreBundle
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Store extends FormRequest
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
        $arrayCb = [
            'custid'                    => 'required|integer|exists:cust,id',
            'description'               => 'required|string|max:255',
            'graph_title'               => 'required|string|max:255',
            'cost'                      => 'nullable|integer',
            'preference'                => 'nullable|integer',
            'type'                      => 'required|integer|in:' . implode( ',', array_keys( CoreBundle::$TYPES ) ),
            'ipv4_subnet'               => $this->type === CoreBundle::TYPE_L3_LAG ? "required" : "nullable",
        ];

        $rules  = ( $this->type === CoreBundle::TYPE_L2_LAG || $this->type === CoreBundle::TYPE_L3_LAG) ? 'required|string|max:255' : 'nullable';
        $rules2 = ( $this->type === CoreBundle::TYPE_L2_LAG || $this->type === CoreBundle::TYPE_L3_LAG) ? 'required|integer|min:0'  : 'nullable';

        $arrayVi = [
            'mtu'                       => 'required|integer|min:0|max:64000',
            'vi-name-a'                 => $rules,
            'vi-name-b'                 => $rules,
            'vi-channel-number-a'       => $rules2,
            'vi-channel-number-b'       => $rules2,
        ];

        return $this->cb  ? $arrayCb : array_merge( $arrayCb, $arrayVi );
    }

    /**
     * @param Validator $validator
     *
     * @return bool
     */
    public function withValidator( Validator $validator ): bool
    {
        if( !$validator->fails() && !$this->input( 'cb' ) ) {
            $validator->after( function( Validator $validator ) {
                if( count( $this->input( 'cl-details' ) ) === 0 ){
                    AlertContainer::push( 'You need at add least one Core link' , Alert::DANGER );
                    $validator->errors()->add( "", "" );
                    return false;
                }

                foreach( $this->input( 'cl-details' ) as $index => $detail ) {
                    foreach( [ "a", "b" ] as $side ){
                        if( !SwitchPort::find( $detail[ "hidden-sp-" . $side ] ) ) {
                            AlertContainer::push( 'Please select the switch port side ' . ucfirst( $side ) . " for the core link number " . $index , Alert::DANGER );
                            $validator->errors()->add( "switch-port", "" );
                            return false;
                        }
                    }
                }
            });
        }
        return true;
    }
}