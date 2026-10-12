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
 * @property-read string $website
 * @property-read string|null $ixpmurl
 * @property-read string|null $submitted_by_name
 * @property-read string|null $submitted_by_email
 * @property-read string|null $submitted_by_ml
 * @property-read int|null $since
 * @property-read array<int, array{
 *      fullname?: string,
 *      shortname?: string,
 *      city?: string|null,
 *      peeringdbid?: int|string|null,
 *      ixfid?: int|string|null,
 *      country?: string,
 *      gpsx?: string|null,
 *      gpsy?: string|null,
 *      register?: mixed
 *  }> $infrastructure
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
     * @psalm-return array{'infrastructure.*.city': 'nullable|string|max:30', 'infrastructure.*.country': 'required_if_accepted:infrastructure.*.register|string|max:2', 'infrastructure.*.fullname': 'required_if_accepted:infrastructure.*.register|nullable|string|max:255', 'infrastructure.*.gpsx': list{'nullable', 'string', \IXP\Rules\Longitude}, 'infrastructure.*.gpsy': list{'nullable', 'string', \IXP\Rules\Latitude}, 'infrastructure.*.ixfid': 'nullable|integer', 'infrastructure.*.peeringdbid': 'nullable|integer', 'infrastructure.*.register': 'nullable', 'infrastructure.*.shortname': 'required_if_accepted:infrastructure.*.register|nullable|string|max:30', ixpmurl: 'nullable|string|max:255', since: 'nullable|integer', submitted_by_email: 'nullable|email|max:100', submitted_by_ml: 'nullable|integer', submitted_by_name: 'nullable|string|max:100', website: 'required|url|max:255'}
     */
    public function rules(): array
    {
        return [
            'website'               => 'required|url|max:255',
            'ixpmurl'               => 'nullable|string|max:255',
            'submitted_by_name'     => 'nullable|string|max:100',
            'submitted_by_email'    => 'nullable|email|max:100',
            'submitted_by_ml'       => 'nullable|integer',
            'since'                 => 'nullable|integer',

            'infrastructure.*.fullname'              => 'required_if_accepted:infrastructure.*.register|nullable|string|max:255',
            'infrastructure.*.shortname'             => 'required_if_accepted:infrastructure.*.register|nullable|string|max:30',
            'infrastructure.*.city'                  => 'nullable|string|max:30',
            'infrastructure.*.peeringdbid'           => 'nullable|integer',
            'infrastructure.*.ixfid'                 => 'nullable|integer',
            'infrastructure.*.country'               => 'required_if_accepted:infrastructure.*.register|string|max:2',
            'infrastructure.*.gpsx'                  => [ 'nullable','string', new Longitude() ],
            'infrastructure.*.gpsy'                  => [ 'nullable','string', new Latitude() ],
            'infrastructure.*.register'              => 'nullable',
        ];
    }

    /**
     * @psalm-return array<string, string>
     */
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