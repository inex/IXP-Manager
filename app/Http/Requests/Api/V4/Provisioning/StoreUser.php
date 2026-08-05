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

use IXP\Http\Requests\User\Store;
use IXP\Models\Customer;

/**
 * Provisioning API - StoreUser request
 *
 * Extends the web form request so both entry points validate identically; see StoreCustomer
 * for the reasoning behind the DELTA split and RulesParityTest.
 *
 * The delta adds `enabled`. The web form posts a field named `disabled` whose value the
 * controller inverts (UserController@store: `$user->disabled = $r->disabled ? 0 : 1`), which
 * reads as a bug and is at best surprising. Rather than reproduce that inversion, the API
 * takes an `enabled` boolean and the controller writes the `disabled` column from it.
 *
 * @author     KleyReX
 * @category   IXP
 * @package    IXP\Http\Requests\Api\V4\Provisioning
 * @copyright  Copyright (C) 2026 KleyReX
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StoreUser extends Store
{
    /**
     * Rules added on top of the inherited web form rules.
     *
     * @return array<string,string>
     */
    public static function delta(): array
    {
        return [
            'enabled' => 'boolean',
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

    /**
     * Take the customer from the route so that callers do not have to repeat it in the body.
     *
     * The inherited rules require `custid`, and the inherited withValidator() reads it when
     * deciding whether a superuser privilege may be granted, so it has to be present before
     * validation runs rather than be filled in by the controller afterwards.
     *
     * @return void
     */
    #[\Override]
    protected function prepareForValidation(): void
    {
        // The inherited username rule reads `$this->id` to exempt the record being edited from
        // the uniqueness check. This endpoint only creates, so an `id` in the body could only
        // serve to exempt someone else's username - and the request would then fail on the
        // database constraint with a 500 rather than a 422:
        $this->request->remove( 'id' );

        if( ( $cust = $this->route( 'cust' ) ) instanceof Customer ) {
            $this->merge( [ 'custid' => $cust->id ] );
        }
    }
}
