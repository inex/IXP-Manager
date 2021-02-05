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

use IXP\Models\User;

use IXP\Exceptions\IrrdbManage;

/**
 * IP Address Request
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Irrdb extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $privs = Auth::getUser()->privs();

        if( $privs < User::AUTH_CUSTUSER ) {
            return false;
        }

        if( $privs === User::AUTH_SUPERUSER ) {
            return true;
        }

        return $this->user()->custid === $this->cust->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Configure the validator instance.
     *
     * @return void
     *
     * @throws
     */
    public function withValidator(): void
    {
        if( !( $this->cust->routeServerClient() && $this->cust->irrdbFiltered() ) ) {
            throw new IrrdbManage( 'IRRDB only applies to customers who are route server clients which are configured for IRRDB filtering.' );
        }

        if( !in_array( $this->protocol, [ 4,6 ], false ) ) {
            abort( 404 , 'Unknown protocol');
        }

        if( !in_array( $this->type, [ "asn", 'prefix' ] ) ) {
            abort( 404 , 'Unknown IRRDB type');
        }
    }
}