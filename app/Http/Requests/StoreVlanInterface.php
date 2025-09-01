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
use IXP\Rules\IdnValidate;

/**
 * Store VlanInterface FormRequest
 *
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   IXP
 * @package    IXP\Http\Requests
 * @copyright  Copyright (C) 2009 - 2021 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StoreVlanInterface extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var User $us */
        $us = Auth::getUser();
        // middleware ensures superuser access only so always authorised here:
        return $us->isSuperUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return ((IdnValidate|string)[]|string)[]
     *
     * @psalm-return array{vlanid: 'required|integer|exists:vlan,id', virtualinterfaceid: 'required|integer|exists:virtualinterface,id', irrdbfilter: 'boolean', mcastenabled: 'boolean', ipv4enabled: 'boolean', ipv4address: 'ipv4|nullable'|'ipv4|required', ipv4hostname: list{'string', 'max:255', 'nullable'|'required', IdnValidate}, ipv4bgpmd5secret: 'string|max:255|nullable', ipv4canping: 'boolean', ipv4monitorrcbgp: 'boolean', ipv4maxbgpprefix: 'integer|nullable', ipv6maxbgpprefix: 'integer|nullable', rsclient: 'boolean', rsmorespecifics: 'boolean', as112client: 'boolean', busyhost: 'boolean', ipv6enabled: 'boolean', ipv6address: 'ipv6|nullable'|'ipv6|required', ipv6hostname: list{'string', 'max:255', 'nullable'|'required', IdnValidate}, ipv6bgpmd5secret: 'string|max:255|nullable', ipv6canping: 'boolean', ipv6monitorrcbgp: 'boolean'}
     */
    public function rules(): array
    {
        return [
            'vlanid'                => 'required|integer|exists:vlan,id',
            'virtualinterfaceid'    => 'required|integer|exists:virtualinterface,id',
            'irrdbfilter'           => 'boolean',
            'mcastenabled'          => 'boolean',
            'ipv4enabled'           => 'boolean',
            'ipv4address'           => 'ipv4' .  ( $this->ipv4enabled ? '|required' : '|nullable' ),
            'ipv4hostname'          => [ 'string', 'max:255' , ( ( config('ixp_fe.vlaninterfaces.hostname_required' ) && $this->ipv4enabled ) ? 'required' : 'nullable' ), new IdnValidate() ],
            'ipv4bgpmd5secret'      => 'string|max:255|nullable',
            'ipv4maxbgpprefix'      => 'integer|nullable',
            'ipv4canping'           => 'boolean',
            'ipv4monitorrcbgp'      => 'boolean',
            'rsclient'              => 'boolean',
            'rsmorespecifics'       => 'boolean',
            'as112client'           => 'boolean',
            'busyhost'              => 'boolean',
            'ipv6enabled'           => 'boolean',
            'ipv6address'           => 'ipv6' . ( $this->ipv6enabled ? '|required' : '|nullable' ),
            'ipv6hostname'          => [ 'string', 'max:255' , ( ( config('ixp_fe.vlaninterfaces.hostname_required' ) && $this->ipv6enabled ) ? 'required' : 'nullable' ), new IdnValidate() ],
            'ipv6bgpmd5secret'      => 'string|max:255|nullable',
            'ipv6maxbgpprefix'      => 'integer|nullable',
            'ipv6canping'           => 'boolean',
            'ipv6monitorrcbgp'      => 'boolean',
        ];
    }
}