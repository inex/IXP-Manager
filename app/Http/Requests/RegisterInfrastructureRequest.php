<?php
/*
 * Copyright (C) 2009 - 2026 Internet Neutral Exchange Association Company Limited By Guarantee.
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

declare(strict_types=1);

namespace IXP\Http\Requests;

use IXP\Rules\Latitude;
use IXP\Rules\Longitude;
use Auth;

use Illuminate\Foundation\Http\FormRequest;
/**
 * RegisterInfrastructureRequest
 *
 * Contains validation rules for IXP Registration form
 */
class RegisterInfrastructureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::user()->isSuperUser();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return string[]
     *
     * @psalm-return array{name: 'required|string|max:255', colo_reference: 'required|string|max:255', cabinet_id: 'required|integer|exists:cabinet,id', cable_type: 'required|integer', connector_type: 'required|integer', installation_date: 'date', port_prefix: 'string|nullable', u_position: 'numeric|nullable', colo_pp_type: 'numeric', mounted_at: string, numberOfPorts: 'required|integer'}
     */
    public function rules(): array
    {
        return [
            'website'               => 'required|url|max:255',
            'ixpmurl'               => 'nullable|string|max:255',
            'submitted_by_name'     => 'nullable|string|max:100',
            'submitted_by_email'    => 'nullable|email|max:100',
            'since'                 => 'nullable|integer',

            'infrastructure.*.fullname'              => 'required_if_accepted:infrastructure.*.register|nullable|string|max:255',
            'infrastructure.*.shortname'             => 'required_if_accepted:infrastructure.*.register|nullable|string|max:30',
            'infrastructure.*.city'                  => 'nullable|string|max:30',
            'infrastructure.*.peeringdbid'           => 'nullable|integer',
            'infrastructure.*.ixfid'                 => 'nullable|integer',
            'infrastructure.*.country'               => 'required_if_accepted:infrastructure.*.register|string|max:2',
            'infrastructure.*.gpsx'                  => [ 'nullable','string', new Longitude() ],
            'infrastructure.*.gpsy'                  => [ 'nullable','string', new Latitude() ],
            'infrastructure.*.register'              => [ 'nullable',  ],
        ];
    }

    #[\Override]
    public function messages(): array
    {
        return [
            'infrastructure.*.fullname.required_if_accepted'    => 'Please provide a name for the infrastructure',
            'infrastructure.*.shortname.required_if_accepted'   => 'Please provide a short name for the infrastructure',
            'infrastructure.*.country.required_if_accepted'     => 'Please provide the country',
        ];
    }
}