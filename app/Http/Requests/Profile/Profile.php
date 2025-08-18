<?php

namespace IXP\Http\Requests\Profile;

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

use Auth, Hash;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Profile details Store Request
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests\Profile
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Profile extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // middleware ensures the user is logged in
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     *
     * @psalm-return array{name: 'required|string|min:2|max:255', username: string, email: 'required|email|max:255', authorisedMobile: 'nullable|string|max:30', actual_password: 'required|string|max:255'}
     */
    public function rules(): array
    {
        return [
            'name'              => 'required|string|min:2|max:255',
            'username'          => 'required|string|min:3|max:255|unique:user,username,' . Auth::id(),
            'email'             => 'required|email|max:255',
            'authorisedMobile'  => 'nullable|string|max:30',
            'actual_password'   => 'required|string|max:255',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     *
     * @return void
     */
    public function withValidator( Validator $validator ): void
    {
        $validator->after( function( $validator ) {
            if( !Hash::check( $this->actual_password, Auth::getUser()->password ) ) {
                $validator->errors()->add( 'actual_password', 'The current password is incorrect.');
                return false;
            }
            return true;
        });
    }
}