<?php

namespace IXP\Http\Requests\Api\V4\Provisioning;

/*
 * Copyright (C) 2026 KleyReX. All Rights Reserved.
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

use IXP\Http\Requests\Customer\Store;
use IXP\Models\Customer;

/**
 * Provisioning API - StoreCustomer request
 *
 * Extends the web form request so that the API and the web UI validate against the same
 * rules. Anything this class adds lives in DELTA, which RulesParityTest uses to assert that
 * nothing else has diverged: if upstream changes a rule we inherit, that test fails and the
 * difference is dealt with deliberately rather than discovered in production.
 *
 * The delta itself covers fillable attributes the web request does not validate. Three of
 * them are worth naming, because they are easy to mistake for typos here rather than there:
 * the columns are `dateleave`, `MD5Support` and `isReseller`, whereas the web rules refer to
 * `dateleft` and `md5support`. Those two rules therefore match nothing and the values reach
 * Customer::create() unvalidated. We do not change the web rules - that is upstream's call -
 * but the API validates the names the database actually uses.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Requests\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StoreCustomer extends Store
{
    /**
     * Rules added on top of the inherited web form rules.
     *
     * Kept as a separate, machine-readable constant so that the parity test can subtract it.
     *
     * @return array<string,string>
     */
    public static function delta(): array
    {
        return [
            'dateleave'             => 'nullable|date',
            'MD5Support'            => 'nullable|string|in:' . implode( ',', array_keys( Customer::$MD5_SUPPORT ) ),
            'isReseller'            => 'boolean',
            'isResold'              => 'boolean',
            'activepeeringmatrix'   => 'boolean',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,mixed>
     *
     * @psalm-suppress LessSpecificImplementedReturnType
     *      The parent's return type is an inferred array shape. Merging the delta widens it
     *      by construction, so a narrower annotation here is not possible.
     */
    #[\Override]
    public function rules(): array
    {
        return array_merge( parent::rules(), self::delta() );
    }
}
